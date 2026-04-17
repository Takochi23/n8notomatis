@extends('layouts.app')

@section('title', 'Scan Struk')
@section('page_title', 'Scan Struk Belanja AI')

@section('content')
<div class="grid grid-cols-2 gap-6">
    <!-- Kolom Upload -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-cloud-arrow-up text-muted"></i> Upload Struk</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('scanstruk.scan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="fileInput">Pilih Foto Struk</label>
                    <div style="border: 2px dashed var(--border-strong); padding: 40px 20px; text-align: center; border-radius: var(--radius-lg); background-color: var(--bg-main);">
                        <i class="fa-solid fa-camera" style="font-size: 3rem; color: var(--primary); margin-bottom: 16px;"></i>
                        <p style="font-weight: 500; margin-bottom: 8px;">Pilih foto struk belanja Anda</p>
                        <p class="text-muted" style="font-size: 0.75rem; margin-bottom: 16px;">Mendukung: JPG, PNG, WEBP (maks 10MB)</p>
                        <input type="file" name="receipt_image" id="fileInput" accept="image/*" required class="form-control" style="max-width: 300px; margin: 0 auto; background: white;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1rem;">
                    <i class="fa-solid fa-paper-plane"></i> Proses dengan n8n
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Hasil -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-clipboard-list text-muted"></i> Hasil Scan</h2>
        </div>
        <div class="card-body">
            @if(session('scan_result'))
                @php $data = session('scan_result'); @endphp

                <!-- Debug -->
                @if(session('raw_result'))
                <details style="margin-bottom: 16px; font-size: 0.75rem;">
                    <summary style="cursor: pointer; color: var(--text-muted);">Lihat Data Mentah (Debug n8n)</summary>
                    <pre style="background: var(--bg-hover); padding: 12px; border-radius: var(--radius-sm); overflow-x: auto; margin-top: 8px; border: 1px solid var(--border);">{{ json_encode(session('raw_result'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </details>
                @endif

                <!-- Store Info -->
                <div class="flex items-center justify-between mb-6" style="padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">{{ $data['store_name'] ?? 'Toko Tidak Diketahui' }}</h3>
                        <span class="badge badge-primary">{{ ucfirst($data['category'] ?? 'Lainnya') }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span class="text-muted" style="font-size: 0.875rem;">Total Akhir</span>
                        <h3 style="color: var(--danger); font-size: 1.5rem; font-weight: 800; margin-top: 4px;">Rp {{ number_format($data['total'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Items -->
                <h4 class="mb-4" style="font-size: 1rem; color: var(--text-muted);">Daftar Item:</h4>
                @if(!empty($data['items']) && is_array($data['items']))
                    <div class="table-responsive mb-6">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: right;">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['items'] as $item)
                                <tr>
                                    <td style="font-weight: 500;">{{ $item['nama'] ?? $item['name'] ?? 'Item' }}</td>
                                    <td style="text-align: center;" class="text-muted">{{ $item['qty'] ?? 1 }}x</td>
                                    <td style="text-align: right; color: var(--text-main);">Rp {{ number_format((float)($item['harga'] ?? $item['price'] ?? 0), 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Item belanja tidak dapat diekstrak atau kosong.</span>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col gap-4 mt-6">
                    <form method="POST" action="/scanstruk/save" style="width: 100%;">
                        @csrf
                        <input type="hidden" name="scan_data" value="{{ json_encode($data) }}">
                        <button type="submit" class="btn btn-success btn-block" style="padding: 12px; font-weight: 600; background-color: var(--success); color: white; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-plus-circle"></i> Simpan ke Transaksi
                        </button>
                    </form>
                    <a href="/scanstruk" class="btn btn-outline btn-block mt-4" style="text-align: center; display: block; padding: 12px; margin-top: 10px;">
                        <i class="fa-solid fa-rotate-right"></i> Scan Struk Baru
                    </a>
                </div>

            @else
                <div class="flex flex-col items-center justify-center text-center" style="padding: 40px 20px; min-height: 300px; color: var(--text-muted);">
                    <div style="width: 80px; height: 80px; background-color: var(--bg-hover); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                        <i class="fa-solid fa-receipt" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 8px;">Belum ada hasil</h3>
                    <p style="font-size: 0.875rem;">Upload foto struk di sebelah kiri untuk melihat hasil.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
