@extends('layouts.app')

@section('title', 'Scan Struk')
@section('page_title', 'Scan Struk Belanja AI')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/scanstruk.css') }}">
@endpush

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
                    <div class="upload-zone">
                        <i class="fa-solid fa-camera upload-icon"></i>
                        <p class="upload-title">Pilih foto struk belanja Anda</p>
                        <p class="upload-hint text-muted">Mendukung: JPG, PNG, WEBP (maks 10MB)</p>
                        <input type="file" name="receipt_image" id="fileInput" accept="image/*" required class="form-control file-input-custom">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-large">
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
                <details class="details-summary">
                    <summary>Lihat Data Mentah (Debug n8n)</summary>
                    <pre class="debug-pre">{{ json_encode(session('raw_result'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </details>
                @endif

                <!-- Store Info -->
                <div class="flex items-center justify-between mb-6 results-header">
                    <div>
                        <h3 class="store-name">{{ $data['store_name'] ?? $data['nama_toko'] ?? 'Toko Tidak Diketahui' }}</h3>
                        <span class="badge badge-primary">{{ ucfirst($data['category'] ?? $data['kategori'] ?? 'Lainnya') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-muted text-sm">Total Akhir</span>
                        <h3 class="total-amount">Rp {{ number_format($data['total'] ?? $data['total_belanja'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Items -->
                <h4 class="mb-4 items-title">Daftar Item:</h4>
                @if(!empty($data['items']) && is_array($data['items']))
                    <div class="table-responsive mb-6">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['items'] as $item)
                                <tr>
                                    <td class="font-medium">{{ $item['nama'] ?? $item['nama_produk'] ?? $item['name'] ?? 'Item' }}</td>
                                    <td class="text-center text-muted">{{ $item['qty'] ?? $item['jumlah'] ?? 1 }}x</td>
                                    <td class="text-right" style="color: var(--text-main);">Rp {{ number_format((float)($item['harga'] ?? $item['total'] ?? $item['harga_satuan'] ?? $item['price'] ?? 0), 0, ',', '.') }}</td>
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
                    <form method="POST" action="/scanstruk/save" style="width: 100%;" id="scanSaveForm">
                        @csrf
                        <input type="hidden" name="scan_data" value="{{ json_encode($data) }}">
                        <input type="hidden" name="user_id" id="scan_user_id" value="">
                        <button type="submit" class="btn btn-success btn-block btn-save-scan">
                            <i class="fa-solid fa-plus-circle"></i> Simpan ke Transaksi
                        </button>
                    </form>
                    <a href="/scanstruk" class="btn btn-outline btn-block btn-new-scan">
                        <i class="fa-solid fa-rotate-right"></i> Scan Struk Baru
                    </a>
                </div>
            @else
                <div class="flex flex-col items-center justify-center text-center empty-results-container">
                    <div class="empty-icon-circle">
                        <i class="fa-solid fa-receipt" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="empty-title">Belum ada hasil</h3>
                    <p class="text-sm">Upload foto struk di sebelah kiri untuk melihat hasil.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/scanstruk.js') }}"></script>
@endsection

