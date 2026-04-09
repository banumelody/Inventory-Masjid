@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">🔔 Notifikasi</h1>
    @if($notifications->where('read_at', null)->count() > 0)
    <form action="{{ route('notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm">
            Tandai Semua Dibaca
        </button>
    </form>
    @endif
</div>

<div class="space-y-3">
    @forelse($notifications as $notification)
    <div class="bg-white rounded-xl shadow p-4 {{ $notification->read_at ? 'opacity-60' : 'border-l-4 border-blue-500' }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    @switch($notification->type)
                        @case('loan_overdue')
                            <span class="text-red-500">⏰</span>
                            @break
                        @case('maintenance_pending')
                            <span class="text-yellow-500">🔧</span>
                            @break
                        @case('feedback_new')
                            <span class="text-blue-500">💬</span>
                            @break
                        @case('low_stock')
                            <span class="text-orange-500">📦</span>
                            @break
                        @default
                            <span class="text-gray-500">🔔</span>
                    @endswitch
                    <h3 class="font-semibold text-gray-800 text-sm">{{ $notification->title }}</h3>
                </div>
                <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notification->read_at)
            <form action="{{ route('notifications.markRead', $notification) }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 whitespace-nowrap">
                    {{ $notification->link ? 'Buka' : 'Tandai Dibaca' }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
        Tidak ada notifikasi.
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endsection
