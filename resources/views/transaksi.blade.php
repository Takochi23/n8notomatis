@extends('layouts.app')

@section('title', 'Daftar Transaksi')
@section('page_title', 'Transaksi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
@endpush

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- Form Tambah Transaksi -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-plus-circle text-primary"></i> Tambah Transaksi Baru</h2>
        </div>
        <div class="card-body">
            <!-- Alert success mapping from ScanStruk saving -->
            @if(session('success'))
                <div class="alert alert-success mb-4 text-sm p-4 bg-green-50 border border-green-200 text-green-700 rounded-md shadow-sm">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <form id="formTransaksi" class="grid grid-cols-2 gap-4">
                <div class="form-group col-span-2 md:col-span-1">
                    <label class="form-label" for="judul">Judul Transaksi</label>
                    <input type="text" id="judul" class="form-control" placeholder="Contoh: Beli Kopi" required>
                </div>
                
                <div class="form-group col-span-2 md:col-span-1">
                    <label class="form-label" for="jumlah">Jumlah (Rp)</label>
                    <div class="relative">
                        <span class="input-prefix">Rp</span>
                        <input type="number" id="jumlah" class="form-control pl-12" placeholder="0" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tipe">Tipe</label>
                    <select id="tipe" class="form-control" required>
                        <option value="pengeluaran">Pengeluaran</option>
                        <option value="pemasukan">Pemasukan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" class="form-control" required>
                </div>

                <div class="form-group col-span-2">
                    <label class="form-label" for="kategori">Kategori</label>
                    <select id="kategori" class="form-control" required>
                        <option value="Makanan & Minuman">Makanan & Minuman</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Belanja">Belanja</option>
                        <option value="Tagihan">Tagihan & Utilitas</option>
                        <option value="Hiburan">Hiburan</option>
                        <option value="Gaji">Gaji</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="col-span-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-large" id="btnSimpan" style="width: 100%;">
                        <i class="fa-solid fa-save"></i> Simpan Transaksi
                    </button>
                    <p class="text-muted text-center mt-2 text-xs">Disimpan ke Database</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="card">
        <div class="card-header flex justify-between items-center results-header">
            <h2 class="card-title">Riwayat Transaksi</h2>
            <div class="flex gap-2">
                <select class="form-control btn-small" id="filterTipe">
                    <option value="semua">Semua Tipe</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
                <select class="form-control btn-small" id="filterKategori">
                    <option value="semua">Semua Kategori</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%">Detail</th>
                        <th>Kategori</th>
                        <th class="text-right">Jumlah</th>
                        <th style="width: 60px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactionsList">
                    <!-- Default Loading State -->
                    <tr>
                        <td colspan="4" class="loading-state">
                            <i class="fa-solid fa-circle-notch fa-spin text-primary loading-icon"></i>
                            Memuat data transaksi...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/transaksi.js') }}"></script>
@endsection

