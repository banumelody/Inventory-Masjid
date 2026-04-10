@props(['items' => []])

<nav class="flex mb-4 text-sm" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 flex-wrap">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600">
                🏠 Dashboard
            </a>
        </li>
        @foreach($items as $item)
        <li class="inline-flex items-center">
            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 0l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-blue-600">{{ $item['label'] }}</a>
            @else
                <span class="text-gray-700 font-medium">{{ $item['label'] }}</span>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
