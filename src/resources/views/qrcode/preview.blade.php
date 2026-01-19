@extends('layouts.app')

@section('title', 'QR Code - ' . $item->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Detail Barang</a>
</div>

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6 text-center">🏷️ QR Code Label</h1>
        
        <!-- QR Code Preview -->
        <div class="text-center mb-6">
            <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                {!! QrCode::size(200)->errorCorrection('H')->generate($item->qr_code_url) !!}
            </div>
        </div>

        <!-- Item Info -->
        <div class="text-center mb-6 p-4 bg-gray-50 rounded-lg">
            <h2 class="font-bold text-lg">{{ $item->name }}</h2>
            <p class="text-gray-500 text-sm font-mono">{{ $item->qr_code_key }}</p>
            <p class="text-gray-400 text-xs mt-1">{{ $item->category->name }} • {{ $item->location->name }}</p>
        </div>

        <!-- URL Preview -->
        <div class="text-center mb-6">
            <p class="text-xs text-gray-400">URL yang dikodekan:</p>
            <code class="text-xs text-gray-600 break-all">{{ $item->qr_code_url }}</code>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('qrcode.print', $item) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-center font-semibold">
                🖨️ Cetak Label
            </a>
            <a href="{{ route('qrcode.bulk', ['items' => [$item->id]]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-center font-semibold">
                📋 Cetak Massal
            </a>
        </div>
    </div>
</div>
@endsection
