@extends('layouts.app')

@section('title', 'Too Many Requests')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
        <i class="ri-timer-flash-line text-4xl text-red-500"></i>
    </div>
    <h1 class="text-4xl font-bold text-slate-900 mb-2">429</h1>
    <h2 class="text-xl font-semibold text-slate-800 mb-4">Too Many Requests</h2>
    <p class="text-slate-500 max-w-md mb-8">
        You have made too many requests in a short period. Please wait a moment before trying again.
    </p>
    <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
        Back to Home
    </a>
</div>
@endsection
