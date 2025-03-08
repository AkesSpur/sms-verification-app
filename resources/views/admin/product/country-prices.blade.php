@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Country Prices</h1>
        </div>
        <div class="mb-3">
          <a href="{{route('admin.products.index')}}" class="btn btn-primary">Back</a>
       </div>
        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Prices</h4>
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
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Country
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Price
                                                    </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress">
                                                        Status
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- each data --}}
                                                @foreach ($prices as $price)
                                                    <tr role="row" class="odd">

                                                        <td>{{ $price->id }}</td>

                                                        <td>{{ $price->country->country_name }}</td>

                                                        <td>{{ $price->price }}</td>

                                                        <td>
                                                          @if($price->status == '1')
                                                            <label class="custom-switch mt-2">
                                                                <input type="checkbox" checked name="custom-switch-checkbox" data-id="{{$price->id}}" class="custom-switch-input change-status" >
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                          @else 
                                                            <label class="custom-switch mt-2">
                                                                <input type="checkbox" name="custom-switch-checkbox" data-id="{{$price->id}}" class="custom-switch-input change-status">
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                          @endif
                                                        </td>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').on('click', '.change-status', function() {
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.product-price.change-status') }}",
                    method: 'PUT',
                    data: {
                        status: isChecked,
                        id: id
                    },
                    success: function(data) {
                        toastr.success(data.message)
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                })

            })
        })
    </script>
@endpush
