@extends('layouts.app')

@section('title', 'Forbidden')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
    <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mb-6">
        <i class="ri-shield-keyhole-line text-4xl text-orange-500"></i>
    </div>
    <h1 class="text-4xl font-bold text-slate-900 mb-2">403</h1>
    <h2 class="text-xl font-semibold text-slate-800 mb-4">Access Denied</h2>
    <p class="text-slate-500 max-w-md mb-8">
        You do not have permission to access this page.
    </p>
    <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
        Back to Home
    </a>
</div>
@endsection
