@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Admin list</h1>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Admin</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="table-2_wrapper"
                                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
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
                                                        Name
                                                      </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                        aria-label="Progress" >
                                                        Email
                                                      </th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1"
                                                      aria-label="Progress" >
                                                      Acct. Bal
                                                    </th>  
                                                    <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                                        rowspan="1" colspan="1"
                                                        aria-label="Due Date: activate to sort column ascending">
                                                        Stauts
                                                      </th>
                                                    <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Status: activate to sort column ascending">
                                                     Action
                                                    </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                              {{-- each data --}}
                                              @foreach ($admins as $admin)

                                                <tr role="row" class="odd">
                                                    <td>{{$admin->id}}</td>

                                                    <td>{{$admin->name}}</td>

                                                    <td>{{$admin->email}}</td>

                                                    <td>{{$admin->wallet->balance}}</td>

                                                    <td>
                                                    @if ($admin->id != 1)
                                                        
                                                      @if($admin->status == 'active')
                                                        <label class="custom-switch mt-2">
                                                            <input type="checkbox" checked name="custom-switch-checkbox" data-id="{{$admin->id}}" class="custom-switch-input change-status" >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                      @else 
                                                        <label class="custom-switch mt-2">
                                                            <input type="checkbox" name="custom-switch-checkbox" data-id="{{$admin->id}}" class="custom-switch-input change-status">
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                      @endif

                                                    @endif
                                                    </td>

                                                    <td>
                                                        @if ($admin->id != 1)
                                                        <a href="{{route('admin.admin-list.destroy', $admin->id)}}" class='btn btn-danger ml-2 delete-item'>
                                                          <i class='far fa-trash-alt'></i>
                                                        </a>
                                                        @endif
                                                        @if (Auth::user()->id == 1)
                                                        <div class="dropdown d-inline">
                                                            <button class="btn btn-dark dropdown-toggle" type="button"
                                                                id="dropdownMenuButton2" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <i class='far fa-edit'></i>
                                                            </button>
                                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                                style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                <a class="dropdown-item has-icon"
                                                                href="{{ route('admin.add-fund.index', $admin->id) }}">
                                                                <i class="fas fa-plus"></i>
                                                                 Add funds
                                                              </a>
                                                              <a class="dropdown-item has-icon"
                                                                  href="{{ route('admin.withdraw-fund.index', $admin->id) }}">
                                                                  <i class="fas fa-wallet"></i> 
                                                                 Withdraw funds
                                                              </a>
                                                            </div>
                                                        </div>                                                            
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

        </div>
    </section>
@endsection

@push('scripts')
    {{-- {{ $dataTable->scripts(attributes: ['type' => 'module']) }} --}}

    <script>
        $(document).ready(function(){
            $('body').on('click', '.change-status', function(){
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');

                $.ajax({
                    url: "{{route('admin.admin-list.status-change')}}",
                    method: 'PUT',
                    data: {
                        status: isChecked,
                        id: id
                    },
                    success: function(data){
                        toastr.success(data.message)
                    },
                    error: function(xhr, status, error){
                        console.log(error);
                    }
                })

            })
        })
    </script>
@endpush
