<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanStrukController extends Controller
{
    public function index()
    {
        return view('scanstruk');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|max:10240',
        ]);

        $webhookUrl = config('services.n8n.webhook_url');

        if (!$webhookUrl) {
            return back()->with('error', 'N8N Webhook URL belum dikonfigurasi di .env');
        }

        try {
            $file = $request->file('receipt_image');

            $response = Http::withoutVerifying()
                ->timeout(60)
                ->attach(
                    'receipt_image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post($webhookUrl, [
                    'user_id' => auth()->id() ?? 1,
                ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal menghubungi n8n webhook. Status: ' . $response->status());
            }

            $result = $response->json();

            if (!$result || !isset($result['receipt_data'])) {
                return back()->with('error', 'Response dari n8n tidak valid atau kosong.');
            }

            $receiptData = $result['receipt_data'];

            // Cek apakah balasan masih berupa struktur raw Gemini API
            if (is_array($receiptData) && isset($receiptData['parts'][0]['text'])) {
                $receiptData = $receiptData['parts'][0]['text'];
            }

            // Defensively decode if it's a string instead of an array/object
            if (is_string($receiptData)) {
                // Try to clean up markdown block if exists
                $cleanStr = preg_replace('/```(?:json)?\s*/i', '', $receiptData);
                $cleanStr = preg_replace('/\s*```\s*/', '', $cleanStr);
                $decoded = json_decode(trim($cleanStr), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $receiptData = $decoded;
                }
            }

            return back()->with([
                'success' => 'Struk berhasil diproses!',
                'scan_result' => $receiptData,
                'raw_result' => $result // Kirim data mentah untuk dicek jika butuh
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
        $data = $request->input('scan_data');
        if (!$data) {
            return back()->with('error', 'Tidak ada data untuk disimpan.');
        }

        $decodedData = json_decode($data, true);
        if (!$decodedData) {
            return back()->with('error', 'Data tidak valid.');
        }

        $mockApiUrl = 'https://69ae8872c8b37f499835c282.mockapi.io/api/v1/transactions';
        $tanggal = $decodedData['tanggal'] ?? date('Y-m-d');
        if ($tanggal == 'tidak terbaca') {
            $tanggal = date('Y-m-d');
        }

        try {
            if (!empty($decodedData['items'])) {
                foreach ($decodedData['items'] as $item) {
                    $itemName = $item['nama'] ?? $item['name'] ?? 'Item Struk';
                    $itemPrice = $item['harga'] ?? $item['price'] ?? 0;
                    $itemCat = $item['kategori'] ?? $decodedData['category'] ?? 'belanja';
                    
                    Http::withoutVerifying()->post($mockApiUrl, [
                        'judul' => $itemName,
                        'jumlah' => (float)$itemPrice,
                        'tipe' => 'pengeluaran',
                        'tanggal' => $tanggal,
                        'kategori' => $itemCat
                    ]);
                }
            } else {
                $total = $decodedData['total'] ?? 0;
                Http::withoutVerifying()->post($mockApiUrl, [
                    'judul' => $decodedData['store_name'] ?? 'Struk Belanja',
                    'jumlah' => (float)$total,
                    'tipe' => 'pengeluaran',
                    'tanggal' => $tanggal,
                    'kategori' => $decodedData['category'] ?? 'belanja'
                ]);
            }

            return redirect('/transaksi')->with('success', 'Transaksi berhasil disimpan dari struk!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan ke MockAPI: ' . $e->getMessage());
        }
    }
}
