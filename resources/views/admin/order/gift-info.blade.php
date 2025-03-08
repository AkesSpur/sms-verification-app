@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Orders</h1>
          </div>
          <div class="mb-3">
            <a href="{{route('admin.order.index')}}" class="btn btn-primary">Back</a>
         </div>
          <div class="section-body">

            <div class="container-fluid">
                <div class="row">
                    <!-- Main Content Area -->
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>#{{$order->order_id}} Order Information</h3>
                            </div>
                            <div class="card-body info_print">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Recipient Information</h4>
                                        <p><strong>Receiver Name:</strong> {{ $order->receiver_name }}</p>
                                        <p><strong>Street:</strong> {{ $order->street }}</p>
                                        <p><strong>Apt Number:</strong> {{ $order->apt_number }}</p>
                                        <p><strong>City:</strong> {{ $order->city }}</p>
                                        <p><strong>State:</strong> {{ $order->state }}</p>
                                        <p><strong>Country:</strong> {{ $order->country }}</p>
                                        <p><strong>Phone Number:</strong> {{ $order->number }}</p>
                                        <p><strong>Zip Code:</strong> {{ $order->zip_code }}</p>
                                        <p><strong>Customization:</strong> 
                                            @if($order->customize)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Sender Information</h4>
                                        <p><strong>Sender Name:</strong> {{ $order->sender_name }}</p>
                                        <p><strong>Sender Number:</strong> {{ $order->sender_number }}</p>
                                        <p><strong>Sender Email:</strong> {{ $order->sender_email }}</p>
                                        <p><strong>Love Note:</strong> {{ $order->note }}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Gift Preview</h4>
                                        @if($order->customize)
                                            <div class="preview-box mb-3">
                                                <img src="{{ asset($order->main_image) }}" alt="Gift Image" class="img-fluid">
                                                <a href="{{ asset($order->main_image) }}" class="btn btn-primary mt-3" download>Download Image</a>
                                              </div>
                                        @else
                                            <p>No image available.</p>
                                        @endif
                                    </div>
                                </div>
                                <button class="btn btn-warning btn-icon icon-left print_invoice"><i class="fas fa-print"></i> Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

          </div>
        </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){

            $('.print_invoice').on('click', function(){
                let printBody = $('.info_print');
                let originalContents = $('body').html();

                $('body').html(printBody.html());

                window.print();

                $('body').html(originalContents);

            })
        })
    </script>
@endpush
