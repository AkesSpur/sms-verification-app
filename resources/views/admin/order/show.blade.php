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
            <div class="invoice">
              <div class="invoice-print">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="invoice-title">
                      <h2></h2>
                      <div class="invoice-number">Order #{{$order->invoice_id}}</div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-6">
                        <address>
                          <strong>Billed To:</strong><br>
                            <b>Name:</b> {{$order->user->name}}<br>
                            <b>Email: </b> {{$order->user->email}}<br>
                        </address>
                      </div>
                      <div class="col-md-6 text-md-right">
                        <address>
                            <strong>Billed To:</strong><br>
                              <b>Name:</b> {{$order->user->name}}<br>
                              <b>Email: </b> {{$order->user->email}}<br>
                        </address>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <address>
                          {{-- <strong>Payment Information:</strong><br>
                          <b>Method:</b> {{$order->payment_method}}<br>
                          <b>Transaction Id: </b>{{@$order->transaction->transaction_id}} <br>
                          <b>Status: </b> {{$order->payment_status === 1 ? 'Complete' : 'Pending'}} --}}
                        </address>
                      </div>
                      <div class="col-md-6 text-md-right">
                        <address>
                          <strong>Order Date:</strong><br>
                          {{date('d F, Y h:i A', strtotime($order->created_at))}}<br><br>
                        </address>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-md-12">
                    <div class="section-title">Order Summary</div>
                    <p class="section-lead">All items here cannot be deleted.</p>
                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-md">
                        <tr>
                          <th data-width="40">#</th>
                          <th>Item</th>
                          <th class="text-center">Price</th>
                          <th class="text-center">Quantity</th>
                          <th class="text-right">Totals</th>
                        </tr>
                            <tr>
                            <td>{{$product->id}}</td>
                            @if (isset($product->slug))
                            <td><a target="_blank" href="{{route('gift-detail', $product->slug)}}">{{$product->name}}</a></td>
                            @else
                                <td>{{$product->name}}</td>
                            @endif
                            <td class="text-center">{{$product->price}} </td>
                            <td class="text-center">{{$order->qty}}</td>
                            <td class="text-right">{{($product->price * $order->qty)}}</td>
                            </tr>

                      </table>
                    </div>
                    <div class="row mt-4">
                      <div class="col-lg-8">
                        <div class="col-md-4">
                          <div class="invoice-detail-item">
                              <strong>Payment Status</strong>
                              <div class="invoice-detail-name">
                                Completed
                              </div>
                            </div>

                            <div class="invoice-detail-item">
                              <strong class="mt-1">Order Status</strong>
                              <div class="invoice-detail-name">
                                {{$order->status}}
                              </div>
                            </div>
                        </div>
                      </div>
                      <div class="col-lg-4 text-right">
                        <hr class="mt-2 mb-2">
                        <div class="invoice-detail-item">
                          <div class="invoice-detail-name">Total</div>
                          <div class="invoice-detail-value invoice-detail-value-lg">NGN {{$order->total_price}}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <div class="text-md-right">
                <button class="btn btn-warning btn-icon icon-left print_invoice"><i class="fas fa-print"></i> Print</button>
              </div>
            </div>
          </div>
        </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){

            $('.print_invoice').on('click', function(){
                let printBody = $('.invoice-print');
                let originalContents = $('body').html();

                $('body').html(printBody.html());

                window.print();

                $('body').html(originalContents);

            })
        })
    </script>
@endpush
