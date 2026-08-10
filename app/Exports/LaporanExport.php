<?php

namespace App\Exports;

use App\Models\Jadwal;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengecekan');

        // Penentuan Periode Filter
        if (!empty($this->filters['bulan'])) {
            [$tahun, $bln] = explode('-', $this->filters['bulan']);
            $tglMulai = Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
            $tglAkhir = Carbon::createFromDate($tahun, $bln, 1)->endOfMonth();
        } else {
            $tglMulai = !empty($this->filters['tanggal_mulai'])
                ? Carbon::parse($this->filters['tanggal_mulai'])->startOfDay()
                : now()->startOfMonth();
            $tglAkhir = !empty($this->filters['tanggal_akhir'])
                ? Carbon::parse($this->filters['tanggal_akhir'])->endOfDay()
                : now()->endOfMonth();
        }

        $query = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan.user'])
            ->whereBetween('tanggal_jadwal', [$tglMulai, $tglAkhir]);

        if (!empty($this->filters['kategori']) && $this->filters['kategori'] !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('kategori_id', $this->filters['kategori']));
        }

        if (!empty($this->filters['lokasi']) && $this->filters['lokasi'] !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('lokasi_id', $this->filters['lokasi']));
        }

        $allJadwal = $query->orderBy('tanggal_jadwal', 'asc')->get();

        $mapped = $allJadwal->map(function ($jadwal) {
            $p = $jadwal->pengecekan->sortByDesc('created_at')->first();

            $status = match (true) {
                !$p => 'belum_dicek',
                $p->status === 'aman' => 'aman',
                $p->status === 'perlu_tindakan' => in_array($p->status_tindak_lanjut, ['selesai', 'diabaikan'])
                    ? 'selesai_ditutup'
                    : 'proses_perbaikan',
                default => 'belum_dicek',
            };

            return ['jadwal' => $jadwal, 'pengecekan' => $p, 'status' => $status];
        });

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['status'] === $this->filters['status']);
        }

        if (!empty($this->filters['tindak_lanjut']) && $this->filters['tindak_lanjut'] !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['pengecekan'] && $m['pengecekan']->status_tindak_lanjut === $this->filters['tindak_lanjut']);
        }

        if (!empty($this->filters['search'])) {
            $s = strtolower($this->filters['search']);
            $mapped = $mapped->filter(function ($m) use ($s) {
                return str_contains(strtolower($m['jadwal']->barang->nama_barang ?? ''), $s)
                    || str_contains(strtolower($m['jadwal']->barang->kode_barang ?? ''), $s)
                    || str_contains(strtolower($m['jadwal']->barang->lokasiRelasi->nama ?? ''), $s);
            });
        }

        $data = $mapped->values();

        $statusLabel = [
            'aman'             => 'Kondisi Aman',
            'proses_perbaikan' => 'Proses Perbaikan',
            'selesai_ditutup'  => 'Selesai / Ditutup',
            'belum_dicek'      => 'Belum Dicek',
        ];

        // Pengaturan Lebar Kolom Excel (Kolom K diperlebar agar muat gambar)
        $widths = [
            'A'=>6, 'B'=>18, 'C'=>16, 'D'=>26, 'E'=>18, 
            'F'=>20, 'G'=>18, 'H'=>20, 'I'=>20, 'J'=>18, 'K'=>32, 'L'=>35
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Header Judul Dokumen
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'PT PADMA SOODE INDONESIA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'LAPORAN PENGECEKAN BARANG & BUKTI FISIK');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'Periode: ' . $tglMulai->translatedFormat('d F Y') . ' — ' . $tglAkhir->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Header Tabel Kolom
        $headers = [
            'No', 'Tgl Pengecekan', 'Kode Barang', 'Nama Barang', 'Kategori',
            'Lokasi', 'Petugas', 'Status Pengecekan', 'Status Tindak Lanjut', 
            'Tgl Tindak Lanjut', 'Foto Bukti (Fisik)', 'Catatan / Temuan'
        ];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '5', $h);
            $col++;
        }
        $sheet->getStyle('A5:L5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(24);

        // Isi Data Baris
        $row = 6;
        foreach ($data as $i => $m) {
            $jadwal = $m['jadwal'];
            $p      = $m['pengecekan'];
            $barang = $jadwal->barang;
            $bgColor = ($i % 2 === 1) ? 'F8FAFC' : 'FFFFFF';
            
            $tglCheck = $p && $p->checked_at 
                ? Carbon::parse($p->checked_at)->format('d/m/Y H:i') 
                : Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y');

            $tglTL = ($p && $p->status_tindak_lanjut && in_array($p->status_tindak_lanjut, ['selesai', 'ditangani', 'diabaikan']) && $p->updated_at)
                ? Carbon::parse($p->updated_at)->format('d/m/Y H:i')
                : '-';

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $tglCheck);
            $sheet->setCellValue('C' . $row, $barang->kode_barang ?? '-');
            $sheet->setCellValue('D' . $row, $barang->nama_barang ?? '-');
            $sheet->setCellValue('E' . $row, $barang->kategori->nama ?? '-');
            $sheet->setCellValue('F' . $row, $barang->lokasiRelasi->nama ?? '-');
            $sheet->setCellValue('G' . $row, $p->user->name ?? $jadwal->user->name ?? '-');
            $sheet->setCellValue('H' . $row, $statusLabel[$m['status']] ?? $m['status']);
            $sheet->setCellValue('I' . $row, $p && $p->status_tindak_lanjut ? strtoupper($p->status_tindak_lanjut) : '-');
            $sheet->setCellValue('J' . $row, $tglTL);
            $sheet->setCellValue('K' . $row, ''); // Kolom K dikosongkan untuk tempat gambar
            $sheet->setCellValue('L' . $row, $p->notes ?? '-');

            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Center alignment
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 🔥 RAHASIA PENYEMATAN GAMBAR KE EXCEL 🔥
            $hasImage = false;
            $offsetX = 5; // Geser sedikit dari kiri sel K

            if ($p) {
                if ($p->photo_before) {
                    $pathBef = public_path('storage/' . $p->photo_before);
                    if (file_exists($pathBef)) {
                        $drawingBef = new Drawing();
                        $drawingBef->setName('Foto Sebelum');
                        $drawingBef->setDescription('Sebelum');
                        $drawingBef->setPath($pathBef);
                        $drawingBef->setHeight(45); // Tinggi gambar dalam piksel
                        $drawingBef->setCoordinates('K' . $row);
                        $drawingBef->setOffsetX($offsetX);
                        $drawingBef->setOffsetY(5);
                        $drawingBef->setWorksheet($sheet);
                        $hasImage = true;
                        $offsetX += 45; // Jika ada foto kedua, digeser ke kanan
                    }
                }

                if ($p->photo_after) {
                    $pathAft = public_path('storage/' . $p->photo_after);
                    if (file_exists($pathAft)) {
                        $drawingAft = new Drawing();
                        $drawingAft->setName('Foto Sesudah');
                        $drawingAft->setDescription('Sesudah');
                        $drawingAft->setPath($pathAft);
                        $drawingAft->setHeight(45);
                        $drawingAft->setCoordinates('K' . $row);
                        $drawingAft->setOffsetX($offsetX);
                        $drawingAft->setOffsetY(5);
                        $drawingAft->setWorksheet($sheet);
                        $hasImage = true;
                    }
                }
            }

            // Jika ada gambar, lebarkan tinggi baris excel agar gambar muat dengan sempurna
            if ($hasImage) {
                $sheet->getRowDimension($row)->setRowHeight(55);
            } else {
                $sheet->setCellValue('K' . $row, 'Tanpa Foto');
                $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K' . $row)->getFont()->setItalic(true)->getColor()->setRGB('94A3B8');
                $sheet->getRowDimension($row)->setRowHeight(20);
            }

            $row++;
        }

        // Baris Total Bawah
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, "Total Data Ditampilkan: " . $data->count() . " catatan pengecekan");
        $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E40AF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);

        $filename = 'Laporan_Pengecekan_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}