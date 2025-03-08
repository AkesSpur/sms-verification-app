
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create New Order</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('order.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="service" class="form-label text-white">Select Service</label>
                            <select class="form-select" id="service" name="service" required>
                                <option value="">Choose a service...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->code }}">{{ $service->name }} - ${{ $service->price }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label text-white">Select Country</label>
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
</div>
@endsection