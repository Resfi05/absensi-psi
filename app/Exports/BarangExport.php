<?php

namespace App\Exports;

use App\Models\Barang;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class BarangExport
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
        $sheet->setTitle('Data Barang');

        // Query data
        $query = Barang::with('kategori');

        // Filter Kategori
        if (!empty($this->filters['kategori']) && $this->filters['kategori'] !== 'semua') {
            $query->where('kategori_id', $this->filters['kategori']);
        }
        
        // Filter Lokasi
        if (!empty($this->filters['lokasi'])) {
            $query->where('lokasi_id', $this->filters['lokasi'])
                  ->orWhere('lokasi', $this->filters['lokasi']); 
        }

        // Filter Status
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'aktif') {
                $query->where('is_active', true);
            } elseif ($this->filters['status'] === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        // Filter Pencarian
        if (!empty($this->filters['search'])) {
            $s = $this->filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('kode_barang', 'like', "%{$s}%")
                  ->orWhere('nama_barang', 'like', "%{$s}%")
                  ->orWhere('lokasi', 'like', "%{$s}%");
            });
        }

        $data = $query->orderBy('kode_barang')->get();

        // Set lebar kolom (Sekarang 10 kolom: A sampai J)
        $sheet->getColumnDimension('A')->setWidth(6);   // No
        $sheet->getColumnDimension('B')->setWidth(16);  // Kode Barang
        $sheet->getColumnDimension('C')->setWidth(30);  // Nama Barang
        $sheet->getColumnDimension('D')->setWidth(20);  // Kategori
        $sheet->getColumnDimension('E')->setWidth(18);  // Merk
        $sheet->getColumnDimension('F')->setWidth(18);  // Model
        $sheet->getColumnDimension('G')->setWidth(18);  // Tipe
        $sheet->getColumnDimension('H')->setWidth(28);  // Lokasi
        $sheet->getColumnDimension('I')->setWidth(15);  // Status
        $sheet->getColumnDimension('J')->setWidth(30);  // Keterangan

        // Judul Laporan
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'PT PADMA SOODE INDONESIA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'DATA BARANG');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . now()->format('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(16);

        // Header Tabel
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Merk', 'Model', 'Tipe', 'Lokasi', 'Status', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(22);

        // Isi Data
        $row = 6;
        foreach ($data as $i => $item) {
            $isEven = ($i % 2 === 1);
            $bgColor = $isEven ? 'F8FAFC' : 'FFFFFF';

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $item->kode_barang);
            $sheet->setCellValue('C' . $row, $item->nama_barang);
            $sheet->setCellValue('D' . $row, $item->kategori->nama ?? $item->jenis_barang ?? '-');
            $sheet->setCellValue('E' . $row, $item->merk ?? '-');
            $sheet->setCellValue('F' . $row, $item->model ?? '-');
            $sheet->setCellValue('G' . $row, $item->tipe ?? '-');
            $sheet->setCellValue('H' . $row, $item->lokasi ?? '-');
            $sheet->setCellValue('I' . $row, $item->is_active ? 'Aktif' : 'Tidak Aktif');
            $sheet->setCellValue('J' . $row, $item->keterangan ?? '-');

            // Style Baris
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Warna Status (Hijau jika Aktif, Merah jika Nonaktif)
            if ($item->is_active) {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '16A34A']],
                ]);
            } else {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'EF4444']],
                ]);
            }

            // Warna Kategori Dinamis
            $catWarna = str_replace('#', '', $item->kategori->warna ?? '2563EB');
            $sheet->getStyle('D' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $catWarna]],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Baris Total / Ringkasan
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $sheet->setCellValue('A' . $row, 'Total Data: ' . $data->count() . ' barang');
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E40AF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // Output Download File Excel
        $filename = 'Data_Barang_PT_Padma_Soode_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}