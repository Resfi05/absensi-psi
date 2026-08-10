<?php

namespace App\Exports;

use App\Models\Jadwal;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JadwalExport
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
        $sheet->setTitle('Jadwal Pengecekan');

        // 🔥 REVISI 4: Panggil relasi kategori
        $query = Jadwal::with(['barang.kategori', 'user']);

        // 🔥 REVISI 4: Tangkap filter 'kategori' (bukan 'jenis' lagi)
        if (!empty($this->filters['kategori']) && $this->filters['kategori'] !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('kategori_id', $this->filters['kategori']));
        }
        
        if (!empty($this->filters['status']) && $this->filters['status'] !== 'semua') {
            // Logika baru untuk jatuh tempo
            if ($this->filters['status'] === 'jatuh_tempo') {
                $query->where('status', 'pending')
                      ->whereBetween('tanggal_jadwal', [now(), now()->addDays(7)]);
            } else {
                $query->where('status', $this->filters['status']);
            }
        }
        
        if (!empty($this->filters['bulan'])) {
            [$tahun, $bln] = explode('-', $this->filters['bulan']);
            $query->whereYear('tanggal_jadwal', $tahun)->whereMonth('tanggal_jadwal', $bln);
        }

        $data = $query->orderBy('tanggal_jadwal')->get();

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20); // Lebarkan untuk nama kategori
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(30);

        // Title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'PT PADMA SOODE INDONESIA — JADWAL PENGECEKAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Diekspor pada: ' . now()->format('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header (Ganti Jenis jadi Kategori)
        $headers = ['No', 'Judul', 'Kode Barang', 'Nama Barang', 'Kategori', 'Petugas', 'Tanggal Jadwal', 'Frekuensi', 'Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Data
        $row = 5;
        foreach ($data as $i => $item) {
            $bg = ($i % 2 === 1) ? 'F8FAFC' : 'FFFFFF';
            $statusColor = match($item->status) {
                'selesai'   => '16A34A',
                'terlambat' => 'EF4444',
                default     => 'D97706',
            };

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $item->judul);
            $sheet->setCellValue('C' . $row, $item->barang->kode_barang ?? '-');
            $sheet->setCellValue('D' . $row, $item->barang->nama_barang ?? '-');
            // 🔥 REVISI 4: Panggil nama kategori
            $sheet->setCellValue('E' . $row, $item->barang->kategori->nama ?? $item->barang->jenis_barang ?? '-');
            $sheet->setCellValue('F' . $row, $item->user->name ?? '-');
            $sheet->setCellValue('G' . $row, \Carbon\Carbon::parse($item->tanggal_jadwal)->format('d/m/Y'));
            $sheet->setCellValue('H' . $row, ucfirst($item->frekuensi));
            $sheet->setCellValue('I' . $row, ucfirst($item->status));

            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $statusColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Summary
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->setCellValue('A' . $row, 'Total: ' . $data->count() . ' jadwal');
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);

        $filename = 'Jadwal_Pengecekan_PT_Padma_Soode_' . now()->format('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}