@extends('layouts.app')

@section('title', __('ui.dashboard') . ' - Inventory Masjid')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📊 {{ __('ui.dashboard') }}</h1>
    <button onclick="toggleWidgetSettings()" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-gray-600">
        ⚙️ {{ __('ui.widget_settings') }}
    </button>
</div>

<!-- Widget Settings Panel -->
<div id="widget-settings" class="hidden bg-white rounded-lg shadow p-4 mb-6 border border-blue-200">
    <h3 class="font-semibold text-gray-700 mb-3">{{ __('ui.show_hide_widgets') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
        @php $widgetLabels = [
            'stats_overview' => __('ui.widget_stats_overview'),
            'charts' => __('ui.widget_charts'),
            'most_borrowed' => __('ui.widget_most_borrowed'),
            'condition_summary' => __('ui.widget_condition_summary'),
            'overdue_loans' => __('ui.widget_overdue_loans'),
            'recent_items' => __('ui.widget_recent_items'),
            'recent_movements' => __('ui.widget_recent_movements'),
            'items_by_category' => __('ui.widget_items_by_category'),
            'items_by_location' => __('ui.widget_items_by_location'),
            'recent_scans' => __('ui.widget_recent_scans'),
            'quick_actions' => __('ui.widget_quick_actions'),
        ]; @endphp
        @foreach($widgetLabels as $key => $label)
        <label class="flex items-center gap-2 text-sm cursor-pointer p-1.5 rounded hover:bg-gray-50">
            <input type="checkbox" class="widget-toggle rounded" data-widget="{{ $key }}"
                {{ ($widgetPrefs[$key] ?? true) ? 'checked' : '' }}>
            <span>{{ $label }}</span>
        </label>
        @endforeach
    </div>
</div>

@if($widgetPrefs['stats_overview'] ?? true)
<!-- Stats Overview -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8" data-widget-section="stats_overview">
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <div class="text-2xl md:text-3xl font-bold text-green-600">{{ $stats['total_items'] }}</div>
        <div class="text-xs md:text-sm text-gray-500">{{ __('ui.total_items') }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ number_format($stats['total_quantity']) }}</div>
        <div class="text-xs md:text-sm text-gray-500">{{ __('ui.total_stock') }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <div class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $stats['active_loans'] }}</div>
        <div class="text-xs md:text-sm text-gray-500">{{ __('ui.borrowed') }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <div class="text-2xl md:text-3xl font-bold {{ $stats['overdue_loans'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['overdue_loans'] }}</div>
        <div class="text-xs md:text-sm text-gray-500">{{ __('ui.overdue') }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <div class="text-2xl md:text-3xl font-bold text-purple-600">{{ $stats['items_with_qr'] }}</div>
        <div class="text-xs md:text-sm text-gray-500">🏷️ {{ __('ui.has_qr') }}</div>
    </div>
</div>
@endif

@if($widgetPrefs['charts'] ?? true)
<!-- Analytics Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Loan Trends Chart -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <h2 class="text-base md:text-lg font-semibold mb-4">📈 {{ __('ui.loan_trends') }}</h2>
        <div class="relative" style="height: 250px;">
            <canvas id="loanTrendsChart"></canvas>
        </div>
    </div>

    <!-- Condition Pie Chart -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <h2 class="text-base md:text-lg font-semibold mb-4">🥧 {{ __('ui.item_condition') }}</h2>
        <div class="relative flex justify-center" style="height: 250px;">
            <canvas id="conditionChart"></canvas>
        </div>
    </div>
</div>
@endif

@if($widgetPrefs['most_borrowed'] ?? true)
<!-- Most Borrowed Items Chart -->
<div class="bg-white rounded-lg shadow p-4 md:p-6 mb-6 md:mb-8">
    <h2 class="text-base md:text-lg font-semibold mb-4">🏆 {{ __('ui.most_borrowed') }}</h2>
    <div class="relative" style="height: 300px;">
        <canvas id="mostBorrowedChart"></canvas>
    </div>
</div>
@endif

@if($widgetPrefs['condition_summary'] ?? true)
<!-- Kondisi Barang Summary -->
<div class="grid grid-cols-3 gap-2 md:gap-4 mb-6 md:mb-8">
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 md:p-4">
        <div class="flex flex-col md:flex-row md:items-center">
            <span class="text-xl md:text-2xl md:mr-3 mb-1 md:mb-0">✅</span>
            <div>
                <div class="text-xl md:text-2xl font-bold text-green-700">{{ $stats['items_good'] }}</div>
                <div class="text-xs md:text-sm text-green-600">{{ __('ui.condition_good') }}</div>
            </div>
        </div>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 md:p-4">
        <div class="flex flex-col md:flex-row md:items-center">
            <span class="text-xl md:text-2xl md:mr-3 mb-1 md:mb-0">⚠️</span>
            <div>
                <div class="text-xl md:text-2xl font-bold text-yellow-700">{{ $stats['items_need_repair'] }}</div>
                <div class="text-xs md:text-sm text-yellow-600">{{ __('ui.condition_repair') }}</div>
            </div>
        </div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-lg p-3 md:p-4">
        <div class="flex flex-col md:flex-row md:items-center">
            <span class="text-xl md:text-2xl md:mr-3 mb-1 md:mb-0">❌</span>
            <div>
                <div class="text-xl md:text-2xl font-bold text-red-700">{{ $stats['items_broken'] }}</div>
                <div class="text-xs md:text-sm text-red-600">{{ __('ui.condition_broken') }}</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(($widgetPrefs['overdue_loans'] ?? true) && $overdueLoans->count() > 0)
<div class="bg-red-50 border border-red-300 rounded-lg p-3 md:p-4 mb-4 md:mb-6">
    <h2 class="text-base md:text-lg font-semibold text-red-800 mb-3">🚨 {{ __('ui.overdue_loans') }}</h2>
    <div class="space-y-2">
        @foreach($overdueLoans as $loan)
        <div class="bg-white rounded p-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <div class="min-w-0">
                <div class="font-medium truncate">{{ $loan->item->name }}</div>
                <div class="text-sm text-gray-500">{{ $loan->borrower_name }}</div>
            </div>
            <div class="text-red-600 text-sm font-medium flex-shrink-0">
                {{ $loan->due_at->diffForHumans() }}
            </div>
        </div>
        @endforeach
    </div>
    <a href="{{ route('loans.index', ['status' => 'overdue']) }}" class="block text-center text-red-600 hover:text-red-800 mt-3 text-sm touch-target py-2">
        Lihat Semua →
    </a>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    @if($widgetPrefs['recent_items'] ?? true)
    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <h2 class="text-base md:text-lg font-semibold mb-3">📦 {{ __('ui.recent_items') }}</h2>
        <div class="space-y-2">
            @forelse($recentItems as $item)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="min-w-0 flex-1 mr-2">
                    <a href="{{ route('items.show', $item) }}" class="font-medium text-blue-600 hover:text-blue-800 truncate block">{{ $item->name }}</a>
                    <div class="text-xs text-gray-500">{{ $item->category->name }}</div>
                </div>
                <div class="text-sm text-gray-500 flex-shrink-0">{{ $item->quantity }} {{ $item->unit }}</div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">{{ __('ui.no_items') }}</p>
            @endforelse
        </div>
        <a href="{{ route('items.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm touch-target py-2">
            Lihat Semua →
        </a>
    </div>
    @endif

    @if($widgetPrefs['recent_movements'] ?? true)
    <!-- Recent Movements -->
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <h2 class="text-base md:text-lg font-semibold mb-3">📊 {{ __('ui.recent_movements') }}</h2>
        <div class="space-y-2">
            @forelse($recentMovements as $movement)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="min-w-0 flex-1 mr-2">
                    <div class="font-medium truncate">{{ $movement->item->name }}</div>
                    <div class="text-xs text-gray-500">{{ $movement->reason }}</div>
                </div>
                <div class="text-sm font-medium flex-shrink-0 {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">{{ __('ui.no_mutations') }}</p>
            @endforelse
        </div>
        <a href="{{ route('stock-movements.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm touch-target py-2">
            Lihat Semua →
        </a>
    </div>
    @endif

    @if($widgetPrefs['items_by_category'] ?? true)
    <!-- Items by Category -->
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <h2 class="text-base md:text-lg font-semibold mb-3">📁 {{ __('ui.by_category') }}</h2>
        <div class="space-y-2">
            @foreach($itemsByCategory as $category)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium">{{ $category->name }}</div>
                <div class="text-sm text-gray-500">{{ $category->items_count }} barang</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($widgetPrefs['items_by_location'] ?? true)
    <!-- Items by Location -->
    <div class="bg-white rounded-lg shadow p-3 md:p-4">
        <h2 class="text-base md:text-lg font-semibold mb-3">📍 {{ __('ui.by_location') }}</h2>
        <div class="space-y-2">
            @foreach($itemsByLocation as $location)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium">{{ $location->name }}</div>
                <div class="text-sm text-gray-500">{{ $location->items_count }} barang</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if(($widgetPrefs['recent_scans'] ?? true) && $recentScans->count() > 0)
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-6 md:mb-8">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base md:text-lg font-semibold">📷 {{ __('ui.recent_scans') }}</h2>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('scan-logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Semua →</a>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="text-left text-xs text-gray-500 border-b">
                    <th class="pb-2">Barang</th>
                    <th class="pb-2">User</th>
                    <th class="pb-2">Tujuan</th>
                    <th class="pb-2 text-right">Waktu</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($recentScans as $scan)
                <tr class="border-b last:border-0">
                    <td class="py-2">
                        @if($scan->item)
                            <a href="{{ route('items.show', $scan->item) }}" class="text-blue-600 hover:text-blue-800">{{ Str::limit($scan->item->name, 25) }}</a>
                        @else
                            <span class="text-gray-400">Dihapus</span>
                        @endif
                    </td>
                    <td class="py-2 text-gray-600">{{ $scan->user->name ?? 'Guest' }}</td>
                    <td class="py-2">
                        @if($scan->purpose)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $scan->purpose_label }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-2 text-gray-400 text-right text-xs">{{ $scan->scanned_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($widgetPrefs['quick_actions'] ?? true)
<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-3 md:p-4">
    <h2 class="text-base md:text-lg font-semibold mb-3">⚡ {{ __('ui.quick_actions') }}</h2>
    <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 md:gap-3">
        @if(auth()->user()->canEditItems())
        <a href="{{ route('items.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center text-sm md:text-base touch-target">+ {{ __('ui.add_item') }}</a>
        @endif
        @if(auth()->user()->canManageLoans())
        <a href="{{ route('loans.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg text-center text-sm md:text-base touch-target">📤 {{ __('ui.lend') }}</a>
        <a href="{{ route('qrcode.audit-scan') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-3 rounded-lg text-center text-sm md:text-base touch-target">📋 {{ __('ui.audit_scan') }}</a>
        @endif
        @if(auth()->user()->canManageStock())
        <a href="{{ route('stock-movements.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-3 rounded-lg text-center text-sm md:text-base touch-target">📊 {{ __('ui.stock_mutation') }}</a>
        @endif
        <a href="{{ route('export.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center text-sm md:text-base touch-target">📥 {{ __('ui.export') }}</a>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Loan Trends Line Chart
    const loanTrendsEl = document.getElementById('loanTrendsChart');
    if (loanTrendsEl) {
    const loanTrendsCtx = loanTrendsEl.getContext('2d');
    new Chart(loanTrendsCtx, {
        type: 'line',
        data: {
            labels: @json($loanTrends['labels']),
            datasets: [
                {
                    label: 'Dipinjam',
                    data: @json($loanTrends['borrowed']),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Dikembalikan',
                    data: @json($loanTrends['returned']),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    } // end loanTrends

    // Condition Pie Chart
    const conditionEl = document.getElementById('conditionChart');
    if (conditionEl) {
    const conditionCtx = conditionEl.getContext('2d');
    new Chart(conditionCtx, {
        type: 'doughnut',
        data: {
            labels: @json($conditionStats['labels']),
            datasets: [{
                data: @json($conditionStats['data']),
                backgroundColor: @json($conditionStats['colors']),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    } // end condition

    // Most Borrowed Items Bar Chart
    const mostBorrowedEl = document.getElementById('mostBorrowedChart');
    if (mostBorrowedEl) {
    const mostBorrowedCtx = mostBorrowedEl.getContext('2d');
    new Chart(mostBorrowedCtx, {
        type: 'bar',
        data: {
            labels: @json($mostBorrowedItems['labels']),
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: @json($mostBorrowedItems['counts']),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false,
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    } // end mostBorrowed
});

function toggleWidgetSettings() {
    const panel = document.getElementById('widget-settings');
    panel.classList.toggle('hidden');
}

document.querySelectorAll('.widget-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const widgets = {};
        document.querySelectorAll('.widget-toggle').forEach(cb => {
            widgets[cb.dataset.widget] = cb.checked;
        });

        fetch('{{ route("dashboard.updateWidgets") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ widgets: widgets }),
        }).then(() => {
            location.reload();
        });
    });
});
</script>
@endsection
