@props(['paginator'])

@if($paginator->hasPages())
<div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 text-xs">

    {{-- Count --}}
    <span class="text-gray-400 tabular-nums">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        <span class="text-gray-300">of</span>
        {{ $paginator->total() }}
    </span>

    {{-- Page buttons --}}
    <div class="flex items-center gap-1">

        {{-- Prev --}}
        @if($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                <i class="ri-arrow-left-s-line text-sm"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white border border-gray-100 text-gray-500 hover:border-primary-200 hover:text-primary-600 transition-all">
                <i class="ri-arrow-left-s-line text-sm"></i>
            </a>
        @endif

        {{-- Page numbers — collapse to ellipsis when many pages --}}
        @php
            $current  = $paginator->currentPage();
            $last     = $paginator->lastPage();
            $window   = 1; // pages each side of current
            $pages    = [];
            for ($p = 1; $p <= $last; $p++) {
                if ($p === 1 || $p === $last || abs($p - $current) <= $window) {
                    $pages[] = $p;
                }
            }
            // Insert ellipsis markers
            $rendered = [];
            $prev = null;
            foreach ($pages as $p) {
                if ($prev !== null && $p - $prev > 1) {
                    $rendered[] = '…';
                }
                $rendered[] = $p;
                $prev = $p;
            }
        @endphp

        @foreach($rendered as $item)
            @if($item === '…')
                <span class="inline-flex items-center justify-center w-7 h-7 text-gray-300 text-xs select-none">…</span>
            @elseif($item == $current)
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold text-white"
                      style="background:linear-gradient(135deg,#475569,#1e293b);">{{ $item }}</span>
            @else
                <a href="{{ $paginator->url($item) }}"
                   class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-medium bg-white border border-gray-100 text-gray-500 hover:border-primary-200 hover:text-primary-600 transition-all">
                    {{ $item }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white border border-gray-100 text-gray-500 hover:border-primary-200 hover:text-primary-600 transition-all">
                <i class="ri-arrow-right-s-line text-sm"></i>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed">
                <i class="ri-arrow-right-s-line text-sm"></i>
            </span>
        @endif

    </div>
</div>
@endif
