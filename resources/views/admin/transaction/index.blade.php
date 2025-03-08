@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Transactions</h1>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Transactions</h4>
                        </div>
                        <div class="card-body">
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
                                                    <th class="sorting" tabindex="0" aria-controls="table-2"
                                                        rowspan="1" colspan="1"
                                                        aria-label="Task Name: activate to sort column ascending">
                                                        Id
                                                    </th>
                                                    <th class="sorting" tabindex="0" aria-controls="table-2"
                                                        rowspan="1" colspan="1"
                                                        aria-label="Task Name: activate to sort column ascending">
                                                        User Email
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Reference
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Payment Method
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Type
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Amount ({{$settings->currency_name}})
                                                    </th>
                                                    <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                                        rowspan="1" colspan="1"
                                                        aria-label="Due Date: activate to sort column ascending">
                                                        Stauts
                                                    </th>
                                                    <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                                        rowspan="1" colspan="1"
                                                        aria-label="Status: activate to sort column ascending">
                                                        Date
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- each data --}}
                                                @foreach ($transactions as $transaction)
                                                    <tr role="row" class="odd">
                                                        <td>{{ $transaction->id }}</td>

                                                        <td>{{ $transaction->email }}</td>

                                                        <td>{{ $transaction->reference }}</td>

                                                        <td>{{ $transaction->payment_method }}</td>

                                                        <td>
                                                            @if ($transaction->type == 'deposit')
                                                              <div class="badge badge-info">
                                                                Deposit
                                                              </div>
                                                            @else
                                                            <div class="badge badge-primary">
                                                              Withdraw
                                                            </div>
                                                            @endif
                                                    </td>

                                                        <td>{{ $transaction->amount }}</td>

                                                        <td>
                                                                @if ($transaction->status == 'successful')
                                                                  <div class="badge badge-success">
                                                                    Successful
                                                                  </div>
                                                                  @elseif ($transaction->status == 'pending')
                                                                  <div class="badge badge-warning">
                                                                    Pending
                                                                  </div>
                                                                @else
                                                                <div class="badge badge-danger">
                                                                  Failed
                                                                </div>
                                                                @endif
                                                        </td>

                                                        <td>{{ date('d F, h:i A', strtotime($transaction->created_at)) }}</td>

                                                    </tr>
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

        </div>
    </section>
@endsection
