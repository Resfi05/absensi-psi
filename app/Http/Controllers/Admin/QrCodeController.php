<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('qrCode')->orderBy('kode_barang');

        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $query->where('jenis_barang', $request->jenis);
        }
        if ($request->filled('qr_status')) {
            if ($request->qr_status === 'sudah') {
                $query->whereHas('qrCode', fn($q) => $q->where('is_active', true));
            } elseif ($request->qr_status === 'belum') {
                $query->whereDoesntHave('qrCode');
            }
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_barang', 'like', "%{$s}%")
                  ->orWhere('nama_barang', 'like', "%{$s}%")
                  ->orWhere('lokasi', 'like', "%{$s}%");
            });
        }

        $barang      = $query->paginate(12)->withQueryString();
        $total_all   = Barang::count();
        $total_qr    = QrCode::where('is_active', true)->count();
        $total_belum = $total_all - $total_qr;

        return view('admin.qrcode.index', compact(
            'barang', 'total_all', 'total_qr', 'total_belum'
        ));
    }

    public function generate(Request $request, Barang $barang)
    {
        if ($barang->qrCode) {
            $oldPath = storage_path('app/public/' . $barang->qrCode->qr_code_path);
            if (file_exists($oldPath)) unlink($oldPath);
            $barang->qrCode->delete();
        }

        $token   = Str::uuid()->toString();
        $scanUrl = url('/user/scan/validasi/' . $token);

        $folder = storage_path('app/public/qrcodes');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        $filename    = 'qr_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $barang->kode_barang) . '_' . time() . '.png';
        $storagePath = 'qrcodes/' . $filename;
        $fullPath    = storage_path('app/public/' . $storagePath);

        $this->generateQrWithLogo($scanUrl, $fullPath);

        QrCode::create([
            'barang_id'    => $barang->id,
            'qr_code_path' => $storagePath,
            'qr_token'     => $token,
            'is_active'    => true,
        ]);

        return redirect()
            ->route('admin.qrcode.index', $request->only(['jenis', 'qr_status', 'search', 'page']))
            ->with('success', "QR Code untuk {$barang->kode_barang} — {$barang->nama_barang} berhasil digenerate.");
    }

    public function generateAll()
    {
        $barangBelumQr = Barang::whereDoesntHave('qrCode')->get();

        if ($barangBelumQr->isEmpty()) {
            return redirect()
                ->route('admin.qrcode.index')
                ->with('info', 'Semua barang sudah memiliki QR Code.');
        }

        $folder = storage_path('app/public/qrcodes');
        if (!file_exists($folder)) mkdir($folder, 0755, true);

        $count = 0;
        foreach ($barangBelumQr as $barang) {
            $token       = Str::uuid()->toString();
            $scanUrl     = url('/user/scan/validasi/' . $token);
            $filename    = 'qr_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $barang->kode_barang) . '_' . time() . rand(100, 999) . '.png';
            $storagePath = 'qrcodes/' . $filename;
            $fullPath    = storage_path('app/public/' . $storagePath);

            $this->generateQrWithLogo($scanUrl, $fullPath);

            QrCode::create([
                'barang_id'    => $barang->id,
                'qr_code_path' => $storagePath,
                'qr_token'     => $token,
                'is_active'    => true,
            ]);
            $count++;
        }

        return redirect()
            ->route('admin.qrcode.index')
            ->with('success', "{$count} QR Code berhasil digenerate sekaligus.");
    }

    /**
     * Generate QR PNG pakai Imagick + merge logo GD
     */
    private function generateQrWithLogo(string $url, string $savePath, int $size = 400): void
    {
        // ── Step 1: Generate QR PNG pakai BaconQrCode + Imagick ──
        $renderer = new ImageRenderer(
            new RendererStyle($size, 2),
            new ImagickImageBackEnd()
        );
        $writer    = new Writer($renderer);
        $qrBinary  = $writer->writeString($url);

        // ── Step 2: Load QR binary ke GD ─────────────────────────
        $qrImage = imagecreatefromstring($qrBinary);
        $qrW     = imagesx($qrImage);
        $qrH     = imagesy($qrImage);

        // ── Step 3: Load logo & tempel di tengah ─────────────────
        $logoPath = public_path('images/logo.png');

        if (file_exists($logoPath)) {
            $logoInfo = getimagesize($logoPath);
            $logoSrc  = match($logoInfo['mime'] ?? '') {
                'image/png'  => imagecreatefrompng($logoPath),
                'image/jpeg' => imagecreatefromjpeg($logoPath),
                'image/webp' => imagecreatefromwebp($logoPath),
                default      => null,
            };

            if ($logoSrc) {
                $origW = imagesx($logoSrc);
                $origH = imagesy($logoSrc);

                // Logo 22% dari QR
                $logoW = (int)($qrW * 0.22);
                $logoH = (int)($origH * ($logoW / $origW));

                // Posisi tengah
                $logoX = (int)(($qrW - $logoW) / 2);
                $logoY = (int)(($qrH - $logoH) / 2);

                // Background putih di belakang logo
                $pad   = 12;
                $white = imagecolorallocate($qrImage, 255, 255, 255);
                imagefilledrectangle(
                    $qrImage,
                    $logoX - $pad,
                    $logoY - $pad,
                    $logoX + $logoW + $pad,
                    $logoY + $logoH + $pad,
                    $white
                );

                // Tempel logo
                imagecopyresampled(
                    $qrImage, $logoSrc,
                    $logoX, $logoY,
                    0, 0,
                    $logoW, $logoH,
                    $origW, $origH
                );

                imagedestroy($logoSrc);
            }
        }

        // ── Step 4: Simpan PNG ────────────────────────────────────
        imagepng($qrImage, $savePath, 0);
        imagedestroy($qrImage);
    }

    public function download(QrCode $qrcode)
    {
        $path = storage_path('app/public/' . $qrcode->qr_code_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan.');
        }

        $filename = 'QR_' . $qrcode->barang->kode_barang . '_' . $qrcode->barang->nama_barang . '.png';
        return response()->download($path, $filename);
    }

    public function cetak(QrCode $qrcode)
    {
        $qrcode->load('barang');
        return view('admin.qrcode.cetak', compact('qrcode'));
    }

    public function cetakSemua(Request $request)
    {
        $ids     = explode(',', $request->ids);
        $qrcodes = QrCode::with('barang')
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get();

        return view('admin.qrcode.cetak-semua', compact('qrcodes'));
    }
}