@extends('layouts.app')

@section('title', __('ui.loan_list') . ' - Inventory Masjid')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📤 {{ __('ui.loan_list') }}</h1>
    <div class="flex gap-2">
        @if(auth()->user()->canManageLoans())
        <a href="{{ route('loans.scan-return') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
            📷 {{ __('ui.scan_qr') }}
        </a>
        <a href="{{ route('loans.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
            + {{ __('ui.add_loan') }}
        </a>
        @endif
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
    <form action="{{ route('loans.index') }}" method="GET" class="space-y-3 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
        <div class="flex-1 min-w-0 md:min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.borrower_name') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.search_placeholder') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-2 text-base">
        </div>
        <div class="md:w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.status') }}</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-2 text-base">
                <option value="">{{ __('ui.all') }}</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>{{ __('ui.status_borrowed') }}</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>{{ __('ui.status_returned') }}</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('ui.status_overdue') }}</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:py-2 rounded-lg font-semibold touch-target">{{ __('ui.filter') }}</button>
            <a href="{{ route('loans.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 md:py-2 rounded-lg font-semibold text-center touch-target">Reset</a>
        </div>
    </form>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-3">
    @forelse($loans as $loan)
    <div class="bg-white rounded-lg shadow p-4 {{ $loan->isOverdue() ? 'border-l-4 border-red-500' : '' }}">
        <div class="flex justify-between items-start mb-2">
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-900 truncate">{{ $loan->item->name }}</div>
                <div class="text-sm text-gray-500">{{ $loan->item->category->name }}</div>
            </div>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $loan->status_color }}-100 text-{{ $loan->status_color }}-800 flex-shrink-0">
                {{ $loan->status }}
            </span>
        </div>
        
        <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
            <div>
                <span class="font-medium">{{ $loan->borrower_name }}</span>
                @if($loan->borrower_phone)
                    <a href="tel:{{ $loan->borrower_phone }}" class="text-blue-600 ml-1">📞</a>
                @endif
            </div>
            <div class="text-gray-400">•</div>
            <div>{{ $loan->quantity }} {{ $loan->item->unit }}</div>
        </div>
        
        <div class="flex justify-between items-center text-xs text-gray-500 mb-3 pb-3 border-b">
            <div>📅 Pinjam: {{ $loan->borrowed_at->format('d/m/Y') }}</div>
            <div>
                @if($loan->due_at)
                    ⏰ Tempo: {{ $loan->due_at->format('d/m/Y') }}
                    @if($loan->isOverdue())
                        <span class="text-red-600 font-medium">({{ $loan->due_at->diffForHumans() }})</span>
                    @endif
                @else
                    -
                @endif
            </div>
        </div>
        
        <div class="flex gap-2">
            @if(!$loan->isReturned() && auth()->user()->canManageLoans())
            <a href="{{ route('loans.return', $loan) }}" class="flex-1 bg-green-100 hover:bg-green-200 text-green-700 px-3 py-2 rounded-lg text-center text-sm font-medium">
                ✅ Kembalikan
            </a>
            <a href="{{ route('loans.qr.print', $loan) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm" title="Cetak QR">
                🏷️
            </a>
            @endif
            @if($loan->isReturned())
            <div class="flex-1 text-center text-sm text-gray-500 py-2">
                Dikembalikan {{ $loan->returned_at->format('d/m/Y') }}
            </div>
            @endif
            @if(auth()->user()->isAdmin())
            <form action="{{ route('loans.destroy', $loan) }}" method="POST" data-confirm="Yakin hapus?">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">🗑️</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <div class="text-4xl mb-2">📤</div>
        <p>{{ __('ui.no_loans') }}</p>
        @if(auth()->user()->canManageLoans())
        <a href="{{ route('loans.create') }}" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
            + {{ __('ui.add_loan') }}
        </a>
        @endif
    </div>
    @endforelse
</div>

<!-- Desktop Table View -->
<div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ui.items') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ui.borrower_name') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">{{ __('ui.quantity') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ui.borrow_date') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">{{ __('ui.due_date') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ui.status') }}</th>
                    <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('ui.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($loans as $loan)
                <tr class="hover:bg-gray-50 {{ $loan->isOverdue() ? 'bg-red-50' : '' }}">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $loan->item->name }}</div>
                        <div class="text-xs text-gray-500">{{ $loan->item->category->name }}</div>
                        <div class="lg:hidden text-xs text-gray-400">{{ $loan->quantity }} {{ $loan->item->unit }}</div>
                    </td>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $loan->borrower_name }}</div>
                        @if($loan->borrower_phone)
                        <div class="text-xs text-gray-500">{{ $loan->borrower_phone }}</div>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->borrowed_at->format('d/m/Y') }}</td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                        {{ $loan->due_at ? $loan->due_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $loan->status_color }}-100 text-{{ $loan->status_color }}-800">
                            {{ $loan->status }}
                        </span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$loan->isReturned() && auth()->user()->canManageLoans())
                            <a href="{{ route('loans.return', $loan) }}" class="text-green-600 hover:text-green-900 px-2 py-1" title="Kembalikan">✅</a>
                            <a href="{{ route('loans.qr.print', $loan) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1" title="Cetak QR">🏷️</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="inline" data-confirm="Yakin hapus?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1" title="Hapus">🗑️</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <div class="text-4xl mb-2">📤</div>
                        <p>{{ __('ui.no_loans') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $loans->links() }}
</div>
@endsection
