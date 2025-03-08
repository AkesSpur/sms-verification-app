@extends('layouts.users')

@section('content')
<div class="container d-flex justify-content-between align-items-center mb-4">
   <h2 class="fw-bold">Dashboard</h2>
   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNumberModal">+ Add Number</button>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm">
            <h3 class="fs-6 fw-semibold">Balance</h3>
            <p class="fs-4 fw-bold">${{$balance}}</p>
            <p class="text-muted">Available for SMS verification</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm">
            <h3 class="fs-6 fw-semibold">Active Numbers</h3>
            <p class="fs-4 fw-bold">1</p>
            <p class="text-muted">Currently active SMS numbers</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-white rounded shadow-sm">
            <h3 class="fs-6 fw-semibold">Recent Transactions</h3>
            <p class="fs-4 fw-bold">0</p>
            <p class="text-muted">Total transactions</p>
        </div>
    </div>
</div>
</div>

 <!-- Modal for Adding Number -->
 <div class="modal fade" id="addNumberModal" tabindex="-1" aria-labelledby="addNumberModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="addNumberModalLabel">Add New Number</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form action="{{ route('order.store') }}" method="POST">
                   @csrf
                   <div class="mb-3">
                       <label for="service" class="form-label fw-bold">Select Service</label>
                       <select class="form-select" id="service" name="service" required>
                           <option value="">Choose a service...</option>
                           @foreach($services as $service)
                               <option value="{{ $service->code }}">{{ $service->name }} - ${{ $service->price }}</option>
                           @endforeach
                       </select>
                   </div>
                   <div class="mb-3">
                       <label for="country" class="form-label fw-bold">Select Country</label>
                       <select class="form-select" id="country" name="country" required>
                           <option value="">Choose a country...</option>
                           <option value="6">Russia</option>
                           <option value="7">USA</option>
                           <option value="8">UK</option>
                       </select>
                   </div>
                   {{-- <div class="mb-3">
                       {!! NoCaptcha::renderJs() !!}
                       {!! NoCaptcha::display() !!}
                   </div> --}}
                   <button type="submit" class="btn btn-primary text-white">Get Number</button>
               </form>
           </div>
       </div>
   </div>
</div>

@endsection