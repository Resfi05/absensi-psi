<?php

namespace App\Exports;

use App\Models\Jadwal;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonitoringExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monitoring Pengecekan');

        // ── Rentang tanggal ───────────────────────────
        $tglMulai = !empty($this->filters['tanggal_mulai'])
            ? Carbon::parse($this->filters['tanggal_mulai'])->startOfDay()
            : now()->startOfMonth();
        $tglAkhir = !empty($this->filters['tanggal_akhir'])
            ? Carbon::parse($this->filters['tanggal_akhir'])->endOfDay()
            : now()->endOfMonth();

        // ── Query jadwal (sama logika dengan controller) ──
        $query = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan.user'])
            ->whereBetween('tanggal_jadwal', [$tglMulai, $tglAkhir]);

        if (!empty($this->filters['kategori']) && $this->filters['kategori'] !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('kategori_id', $this->filters['kategori']));
        }
        if (!empty($this->filters['lokasi']) && $this->filters['lokasi'] !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('lokasi_id', $this->filters['lokasi']));
        }

        $allJadwal = $query->get();

        // ── Mapping status (mutlak, sama dengan controller) ──
        $mapped = $allJadwal->map(function ($jadwal) {
            $p = $jadwal->pengecekan->sortByDesc('created_at')->first();

            if (!$p) {
                $status = 'belum_dicek';
            } elseif ($p->status === 'aman') {
                $status = 'sudah_dicek';
            } elseif ($p->status === 'perlu_tindakan') {
                $status = 'perlu_tindakan';
            } else {
                $status = 'belum_dicek';
            }

            return ['jadwal' => $jadwal, 'pengecekan' => $p, 'status' => $status];
        });

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['status'] === $this->filters['status']);
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
            'sudah_dicek'    => 'Sudah Dicek',
            'belum_dicek'    => 'Belum Dicek',
            'perlu_tindakan' => 'Perlu Tindak Lanjut',
        ];

        // ── Lebar kolom ────────────────────────────────
        $widths = ['A'=>6,'B'=>15,'C'=>30,'D'=>14,'E'=>28,'F'=>14,'G'=>20,'H'=>22,'I'=>20,'J'=>34];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Header dokumen ─────────────────────────────
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'PT PADMA SOODE INDONESIA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'LAPORAN MONITORING PENGECEKAN');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Periode: ' . $tglMulai->translatedFormat('d F Y') . ' — ' . $tglAkhir->translatedFormat('d F Y'));
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A4:J4');
        $sheet->setCellValue('A4', 'Diekspor pada: ' . now()->format('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94A3B8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(16);
        $sheet->getRowDimension(4)->setRowHeight(14);

        // ── Header tabel ───────────────────────────────
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Lokasi', 'Tgl Jadwal', 'Status', 'Terakhir Dicek', 'Petugas', 'Catatan Temuan'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '6', $h);
            $col++;
        }
        $sheet->getStyle('A6:J6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(22);

        // ── Data rows ───────────────────────────────────
        $row = 7;
        $countSudah = 0; $countBelum = 0; $countPerlu = 0;

        foreach ($data as $i => $m) {
            $jadwal = $m['jadwal'];
            $p      = $m['pengecekan'];
            $barang = $jadwal->barang;
            $isEven = ($i % 2 === 1);
            $bgColor = $isEven ? 'F8FAFC' : 'FFFFFF';

            if ($m['status'] === 'sudah_dicek') $countSudah++;
            elseif ($m['status'] === 'perlu_tindakan') $countPerlu++;
            else $countBelum++;

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $barang->kode_barang ?? '-');
            $sheet->setCellValue('C' . $row, $barang->nama_barang ?? '-');
            $sheet->setCellValue('D' . $row, $barang->kategori->nama ?? '-');
            $sheet->setCellValue('E' . $row, $barang->lokasiRelasi->nama ?? '-');
            $sheet->setCellValue('F' . $row, Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $statusLabel[$m['status']]);
            $sheet->setCellValue('H' . $row, $p && $p->checked_at ? Carbon::parse($p->checked_at)->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('I' . $row, $p->user->name ?? '-');
            $sheet->setCellValue('J' . $row, $p->notes ?? '-');

            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Warna status
            $statusColor = match($m['status']) {
                'sudah_dicek'    => '16A34A',
                'perlu_tindakan' => 'EF4444',
                default          => 'D97706',
            };
            $sheet->getStyle('G' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $statusColor]],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // ── Summary ──────────────────────────────────────
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $sheet->setCellValue('A' . $row, "Total: {$data->count()} jadwal — Sudah Dicek: {$countSudah} | Belum Dicek: {$countBelum} | Perlu Tindak Lanjut: {$countPerlu}");
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E40AF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // ── Output ───────────────────────────────────────
        $filename = 'Monitoring_Pengecekan_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}