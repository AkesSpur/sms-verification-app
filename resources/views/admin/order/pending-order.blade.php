@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Orders</h1>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="table-responsive">
                            <div id="table-2_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-striped dataTable no-footer" id="table-2" role="grid"
                                        aria-describedby="table-2_info">
                                        <thead>
                                            {{-- column title --}}
                                            <tr role="row">
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Id
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Invoice Id
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Customer
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Qty
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Amount
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="table-2" rowspan="1"
                                                    colspan="1"
                                                    aria-label="Task Name: activate to sort column ascending">
                                                    Custom
                                                </th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                                    aria-label="Progress">
                                                    Status
                                                </th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                                    aria-label="Progress">
                                                    Date
                                                </th>
                                                <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Due Date: activate to sort column ascending">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- each data --}}
                                            @foreach ($orders as $order)
                                                <tr role="row" class="odd">

                                                    <td>
                                                        <div class="mt-2">
                                                            {{ $order->id }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        {{ $order->invoice_id }}
                                                    </td>

                                                    <td class="text-capitalize">
                                                        <div class="mt-2">
                                                            {{ $order->user->name }}
                                                        </div>
                                                    </td>

                                                    <td class="text-capitalize">
                                                        <div class="mt-2">
                                                            {{ $order->qty }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="mt-2">
                                                            {{ $order->total_price }}
                                                        </div>
                                                    </td>

                                                    <td class="text-capitalize">
                                                        @foreach ($order->giftOrders as $giftOrder)
                                                            @if ($giftOrder->customize == 1)
                                                                <div class="btn btn-icon btn-dark mt-2">
                                                                    Yes
                                                                </div>
                                                            @else
                                                                <div class="btn btn-icon btn-light mt-2">
                                                                    No
                                                                </div>
                                                            @endif
                                                            @endforeach
                                                    </td>

                                                    <td>
                                                        <div class="p-2 text-capitalize badge badge-warning">
                                                            {{ $order->status }}
                                                        </div>
                                                    </td>
                                                    
                                                    <td>{{ date('d F, h:i A', strtotime($order->created_at)) }}</td>
                                                    
                                                    <td>
                                                        <a href="{{ route('admin.order.show', $order->id) }}"
                                                            class='btn btn-info'><i class='far fa-eye'></i></a>
                                                        <div class="dropdown d-inline">
                                                            <button class="btn btn-dark dropdown-toggle" type="button"
                                                                id="dropdownMenuButton2" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <i class='far fa-edit'></i>
                                                            </button>
                                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                                style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                <a class="dropdown-item has-icon" href="{{ route('admin.gift-info', $order->id) }}">
                                                                    <i class="far fa-file">
                                                                    </i> View Gift Info
                                                                </a>
                                                                <a class="dropdown-item has-icon" href="{{route('admin.update-gift-order',$order->id)}}"><i
                                                                    class="far fa-edit">
                                                                </i> Update Tracking Id
                                                                </a>
                                                            </div>
                                                            </div>
                                                            <a href="{{route('admin.order.destroy', $order->id)}}" class='btn btn-danger ml-2 mr-2 delete-item'>
                                                              <i class='far fa-trash-alt'></i>
                                                            </a>
                                                        </td>
                                                </tr>
                                                {{ $image = null }}
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
