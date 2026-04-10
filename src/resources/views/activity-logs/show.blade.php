@extends('layouts.app')

@section('title', 'Detail Activity - Inventory Masjid')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Activity Log']]" />
    <div class="mb-6">
        <a href="{{ route('activity-logs.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Activity Log</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-3 mb-6">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-{{ $activityLog->action_color }}-100">
                @switch($activityLog->action)
                    @case('create')
                        <span class="text-2xl">➕</span>
                        @break
                    @case('update')
                        <span class="text-2xl">✏️</span>
                        @break
                    @case('delete')
                        <span class="text-2xl">🗑️</span>
                        @break
                    @case('login')
                        <span class="text-2xl">🔐</span>
                        @break
                    @case('logout')
                        <span class="text-2xl">🚪</span>
                        @break
                    @case('import')
                        <span class="text-2xl">📥</span>
                        @break
                    @default
                        <span class="text-2xl">📝</span>
                @endswitch
            </span>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $activityLog->action_label }}</h1>
                <p class="text-sm text-gray-500">{{ $activityLog->model_name }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">User</label>
                    <p class="text-gray-900">{{ $activityLog->user?->name ?? 'System' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Waktu</label>
                    <p class="text-gray-900">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">IP Address</label>
                    <p class="text-gray-900">{{ $activityLog->ip_address ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Model ID</label>
                    <p class="text-gray-900">{{ $activityLog->model_id ?? '-' }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                <p class="text-gray-900">{{ $activityLog->description }}</p>
            </div>

            @if($activityLog->user_agent)
            <div>
                <label class="text-sm font-medium text-gray-500">User Agent</label>
                <p class="text-gray-900 text-sm break-all">{{ $activityLog->user_agent }}</p>
            </div>
            @endif

            @if($activityLog->old_values)
            <div>
                <label class="text-sm font-medium text-gray-500">Data Sebelumnya</label>
                <pre class="mt-1 p-3 bg-red-50 rounded-lg text-sm overflow-x-auto">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->new_values)
            <div>
                <label class="text-sm font-medium text-gray-500">Data Baru</label>
                <pre class="mt-1 p-3 bg-green-50 rounded-lg text-sm overflow-x-auto">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
