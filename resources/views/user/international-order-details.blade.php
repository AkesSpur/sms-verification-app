@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' — International')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Back / Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('user.all-countries') }}"
               class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-indigo-600 transition-colors mb-1">
                <i class="ri-arrow-left-line"></i> Back to International Numbers
            </a>
            <h1 class="text-sm font-bold text-gray-900">Order #{{ $order->id }}</h1>
        </div>
        @php
            $statusMap = [
                'completed' => 'bg-emerald-50 text-emerald-700',
                'pending'   => 'bg-amber-50 text-amber-700',
                'cancelled' => 'bg-red-50 text-red-500',
            ];
            $sc = $statusMap[$order->status] ?? 'bg-blue-50 text-blue-700';
        @endphp
        <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $sc }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    {{-- ── Timeline ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-5">Order Progress</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

            {{-- Placed --}}
            <div class="flex flex-col items-center text-center gap-1.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600">
                    <i class="ri-shopping-bag-2-line text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700">Order Placed</p>
                <p class="text-[10px] text-gray-400">{{ $order->created_at->format('d M, H:i') }}</p>
            </div>

            {{-- Number Assigned --}}
            <div class="flex flex-col items-center text-center gap-1.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $order->phone_number ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-300' }}">
                    <i class="ri-smartphone-line text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700">Number Assigned</p>
                <p class="text-[10px] {{ $order->phone_number ? 'text-emerald-600' : 'text-gray-400' }}">
                    {{ $order->phone_number ? 'Done' : 'Pending' }}
                </p>
            </div>

            {{-- SMS --}}
            <div class="flex flex-col items-center text-center gap-1.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center
                    {{ $order->sms_code ? 'bg-emerald-50 text-emerald-600' : ($order->status === 'cancelled' ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-300') }}">
                    <i class="{{ $order->status === 'cancelled' ? 'ri-close-line' : 'ri-chat-1-line' }} text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700">SMS Received</p>
                <p class="text-[10px] {{ $order->sms_code ? 'text-emerald-600' : ($order->status === 'cancelled' ? 'text-red-500' : 'text-gray-400') }}">
                    {{ $order->sms_code ? 'Done' : ($order->status === 'cancelled' ? 'Cancelled' : 'Waiting') }}
                </p>
            </div>

            {{-- Complete --}}
            <div class="flex flex-col items-center text-center gap-1.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center
                    {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($order->status === 'cancelled' ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-300') }}">
                    <i class="{{ $order->status === 'cancelled' ? 'ri-close-line' : 'ri-check-line' }} text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-gray-700">Completed</p>
                <p class="text-[10px] {{ $order->status === 'completed' ? 'text-emerald-600' : ($order->status === 'cancelled' ? 'text-red-500' : 'text-gray-400') }}">
                    {{ $order->status === 'completed' ? $order->updated_at->format('d M, H:i') : ($order->status === 'cancelled' ? 'Cancelled' : 'Pending') }}
                </p>
            </div>

        </div>
    </div>

    {{-- ── Details grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Order details --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Order Details</p>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Order ID</span>
                    <span class="font-semibold text-gray-800">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Country</span>
                    <span class="font-semibold text-gray-800">{{ $order->country_name ?? ($order->country->name ?? 'Unknown') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Service</span>
                    <span class="font-semibold text-gray-800">{{ $order->service->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Phone Number</span>
                    @if($order->phone_number)
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-semibold text-gray-800">{{ $order->phone_number }}</span>
                            <button onclick="copyText('{{ $order->phone_number }}')"
                                    class="text-gray-300 hover:text-indigo-500 transition-colors">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </div>
                    @else
                        <span class="text-gray-400">Not assigned yet</span>
                    @endif
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">SMS Code</span>
                    @if($order->sms_code)
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $order->sms_code }}</span>
                            <button onclick="copyText('{{ $order->sms_code }}')"
                                    class="text-gray-300 hover:text-emerald-500 transition-colors">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </div>
                    @else
                        <span class="text-gray-400">Waiting…</span>
                    @endif
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-400">Amount</span>
                    <span class="font-bold text-gray-800">&#8358;{{ number_format($order->amount ?? $order->price ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Timing --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Timing</p>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Created</span>
                    <span class="font-semibold text-gray-800">{{ $order->created_at->format('d M Y, H:i:s') }}</span>
                </div>
                @if($order->expires_at)
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Expires</span>
                    <span class="font-semibold text-gray-800">{{ $order->expires_at->format('d M Y, H:i:s') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Time Remaining</span>
                    <span id="countdown"
                          class="font-mono font-semibold text-amber-500"
                          data-expires="{{ $order->expires_at->toISOString() }}">
                        Calculating…
                    </span>
                </div>
                @endif
                @if($order->status === 'completed')
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-400">Completed</span>
                    <span class="font-semibold text-gray-800">{{ $order->updated_at->format('d M Y, H:i:s') }}</span>
                </div>
                @endif
                @if($order->refund_status)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-400">Refund</span>
                    <span class="font-semibold {{ $order->refund_status === 'refunded' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ ucfirst($order->refund_status) }}
                    </span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Info notice ── --}}
    <div class="flex items-start gap-3 bg-indigo-50 border border-indigo-100 rounded-2xl p-4">
        <i class="ri-information-line text-indigo-500 flex-shrink-0 mt-0.5"></i>
        <div class="text-xs text-indigo-700 space-y-1 leading-relaxed">
            <p class="font-semibold">How it works</p>
            <p>Use the phone number above to receive SMS verification codes. Numbers expire after 20 minutes — if no SMS arrives, your balance is refunded automatically.</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyText(text) {
    navigator.clipboard.writeText(text)
        .then(() => notify('success', 'Copied!'))
        .catch(() => {
            const t = document.createElement('textarea');
            t.value = text; document.body.appendChild(t); t.select();
            document.execCommand('copy'); document.body.removeChild(t);
            notify('success', 'Copied!');
        });
}

(function() {
    const el = document.getElementById('countdown');
    if (!el) return;
    const expires = new Date(el.dataset.expires);

    function tick() {
        const left = expires - Date.now();
        if (left <= 0) {
            el.textContent = 'Expired';
            el.className = 'font-mono font-semibold text-red-500';
            return;
        }
        const m = Math.floor(left / 60000);
        const s = Math.floor((left % 60000) / 1000);
        el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        el.className = m < 5 ? 'font-mono font-semibold text-red-500'
                     : m < 10 ? 'font-mono font-semibold text-amber-600'
                     : 'font-mono font-semibold text-amber-500';
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush