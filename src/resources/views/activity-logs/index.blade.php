@extends('layouts.app')

@section('title', 'Activity Log - Inventory Masjid')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📋 Activity Log</h1>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
    <form action="{{ route('activity-logs.index') }}" method="GET" class="space-y-3 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
        <div class="flex-1 min-w-0 md:min-w-[150px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Deskripsi..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
        </div>
        <div class="md:w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
            <select name="action" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
                <option value="">Semua</option>
                @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
                <option value="">Semua</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
        </div>
        <div class="md:w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">Filter</button>
            <a href="{{ route('activity-logs.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-center">Reset</a>
        </div>
    </form>
</div>

<!-- Activity List -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="divide-y divide-gray-200">
        @forelse($logs as $log)
        <div class="p-4 hover:bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-{{ $log->action_color }}-100">
                        @switch($log->action)
                            @case('create')
                                <span class="text-{{ $log->action_color }}-600">➕</span>
                                @break
                            @case('update')
                                <span class="text-{{ $log->action_color }}-600">✏️</span>
                                @break
                            @case('delete')
                                <span class="text-{{ $log->action_color }}-600">🗑️</span>
                                @break
                            @case('login')
                                <span class="text-{{ $log->action_color }}-600">🔐</span>
                                @break
                            @case('logout')
                                <span class="text-{{ $log->action_color }}-600">🚪</span>
                                @break
                            @case('import')
                                <span class="text-{{ $log->action_color }}-600">📥</span>
                                @break
                            @case('export')
                                <span class="text-{{ $log->action_color }}-600">📤</span>
                                @break
                            @default
                                <span class="text-{{ $log->action_color }}-600">📝</span>
                        @endswitch
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-{{ $log->action_color }}-100 text-{{ $log->action_color }}-800">
                            {{ $log->action_label }}
                        </span>
                        @if($log->model_type)
                        <span class="text-xs text-gray-500">{{ $log->model_name }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-900 mt-1">{{ $log->description }}</p>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                        <span>{{ $log->user?->name ?? 'System' }}</span>
                        <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        @if($log->ip_address)
                        <span class="hidden sm:inline">IP: {{ $log->ip_address }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('activity-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <div class="text-4xl mb-2">📋</div>
            <p>Belum ada activity log.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection
