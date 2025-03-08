
@extends('layouts.users')

@section('content')
<div>
    <h2>Dashboard</h2>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Balance</h5>
            <p class="card-text">$10.00</p>
            <p class="card-text"><small class="text-muted">Available for SMS verification</small></p>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Active Numbers</h5>
            <p class="card-text">1</p>
            <p class="card-text"><small class="text-muted">Currently active SMS numbers</small></p>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Recent Transactions</h5>
            <p class="card-text">0</p>
            <p class="card-text"><small class="text-muted">Total transactions</small></p>
        </div>
    </div>
</div>
@endsection