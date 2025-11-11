@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Admin List</h1>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>All Admins</h4>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-4 mb-1">
                                    <input type="text" class="form-control" id="adminSearchFilter" value="{{ request('search') }}" placeholder="Search name or email">
                                </div>
                                <div class="col-md-3 mb-1">
                                    <select class="form-control select2" id="adminResellerFilter">
                                        <option value="">All Users</option>
                                        <option value="yes" {{ request('reseller')==='yes' ? 'selected' : '' }}>Resellers</option>
                                        <option value="no" {{ request('reseller')==='no' ? 'selected' : '' }}>Regular Users</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-1">
                                    <button type="button" class="btn btn-primary w-100" id="adminApplyFiltersBtn">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-secondary w-100" id="adminClearFilters">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-striped" role="grid"
                                            aria-describedby="table-2_info">
                                            <thead>
                                                {{-- column title --}}
                                                <tr role="row">
                                                    <th>Id</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th class="text-center">Verified?</th>
                                                    <th class="text-center">Reseller?</th>
                                                    <th class="text-center">Balance</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              {{-- each data --}}
                                              @foreach ($admins as $admin)

                                                <tr role="row" class="odd">
                                                    <td>{{$admin->id}}</td>

                                                    <td>{{$admin->name}}</td>

                                                    <td>
                                                        <a href="mailto:{{$admin->email}}">{{$admin->email}}</a>
                                                        <button class="btn btn-sm btn-info edit-email-btn" data-id="{{$admin->id}}" data-email="{{$admin->email}}">Edit</button>
                                                    </td>


                                                    <td class="text-center">
                                                        @if ($admin->email_verified_at)
                                                            <i class="fas fa-check-circle text-success" title="Email Verified"></i>
                                                        @else
                                                            <i class="fas fa-times-circle text-muted" title="Email Not Verified"></i>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">
                                                        @if ($admin->is_reseller)
                                                            <i class="fas fa-user-tag text-success" title="Reseller"></i>
                                                        @else
                                                            <i class="fas fa-user text-muted" title="Regular User"></i>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">
                                                        {{$admin->balance}}
                                                    </td>

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
                                                        @if (Auth::user()->id == 1)
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-dark dropdown-toggle" type="button"
                                                                    id="dropdownMenuButton{{ $admin->id }}" data-toggle="dropdown"
                                                                    aria-haspopup="true" aria-expanded="false">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $admin->id }}">
                                                    
                                                                    <a class="dropdown-item" href="{{ route('admin.add-fund.index', $admin->id) }}">
                                                                        <i class="fas fa-plus mr-1"></i> Add Funds
                                                                    </a>
                                                    
                                                                    <a class="dropdown-item" href="{{ route('admin.withdraw-fund.index', $admin->id) }}">
                                                                        <i class="fas fa-wallet mr-1"></i> Withdraw Funds
                                                                    </a>
                                                    
                                                                    @if ($admin->email_verified_at == null)
                                                                        <button class="dropdown-item verify-email-btn" data-id="{{ $admin->id }}">
                                                                            <i class="fas fa-check-circle mt-1 mr-1 text-success"></i> 
                                                                            <span>
                                                                                Verify Email
                                                                            </span>
                                                                        </button>
                                                                    @endif
                                                    
                                                                    <button class="dropdown-item send-reset-link-btn" data-id="{{ $admin->id }}">
                                                                        <i class="fas fa-key mt-1 mr-1 text-warning"></i>
                                                                        <span>
                                                                            Reset Password
                                                                        </span>
                                                                    </button>

                                                                    @if ($admin->id != 1)
                                                                        @if (!$admin->is_reseller)
                                                                            <form action="{{ route('admin.admins.make-reseller', $admin->id) }}" method="POST" class="dropdown-item p-0">
                                                                                @csrf
                                                                                <button type="submit" class="dropdown-item">
                                                                                    <i class="fas fa-user-plus text-success mr-2"></i> Make Reseller
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <form action="{{ route('admin.admins.remove-reseller', $admin->id) }}" method="POST" class="dropdown-item p-0">
                                                                                @csrf
                                                                                <button type="submit" class="dropdown-item">
                                                                                    <i class="fas fa-user-minus text-danger mr-2"></i> Remove Reseller
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    @endif

                                                                    @if ($admin->id != 1)
                                                                    <a class="dropdown-item text-danger delete-item" href="{{ route('admin.admin-list.destroy', $admin->id) }}">
                                                                        <i class="far fa-trash-alt mr-1"></i> Delete
                                                                    </a>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    
                                                </tr>                                                  
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div>
                                            {{ $admins->appends(request()->query())->links() }}
                                        </div>
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

    <!-- Email Edit Modal -->
    <div class="modal fade" id="emailEditModal" tabindex="-1" role="dialog" aria-labelledby="emailEditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailEditModalLabel">Edit Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="emailEditForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="admin_id" id="admin_id">
                        <div class="form-group">
                            <label for="admin_email">Email Address</label>
                            <input type="email" class="form-control" id="admin_email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profiles Modal -->
    <div class="modal fade" id="profilesModal" tabindex="-1" role="dialog" aria-labelledby="profilesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilesModalLabel">Profiles for <span id="userName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="profilesTableBody">
                                <!-- Profiles will be loaded here by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
   

    <script>
        $(document).ready(function(){
            // Initialize select2 for filters
            if ($.fn.select2) {
                $('.select2').select2({ width: '100%' });
            }

            function adminApplyFilters() {
                const baseUrl = "{{ route('admin.admin-list.index') }}";
                const params = new URLSearchParams();
                const search = $('#adminSearchFilter').val().trim();
                const reseller = $('#adminResellerFilter').val();
                if (search) params.set('search', search);
                if (reseller) params.set('reseller', reseller);
                const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
                window.location.href = url;
            }

            $('#adminResellerFilter').on('change', adminApplyFilters);
            $('#adminSearchFilter').on('keypress', function(e){
                if (e.which === 13) adminApplyFilters();
            });
            $('#adminApplyFiltersBtn').on('click', adminApplyFilters);
            $('#adminClearFilters').on('click', function(){
                window.location.href = "{{ route('admin.admin-list.index') }}";
            });

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

            // Handle Edit Email button click
            $('.edit-email-btn').on('click', function(){
                let adminId = $(this).data('id');
                let adminEmail = $(this).data('email');
                $('#admin_id').val(adminId);
                $('#admin_email').val(adminEmail);
                $('#emailEditModal').modal('show');
            });

            // Handle Email Edit Form submission
            $('#emailEditForm').on('submit', function(e){
                e.preventDefault();
                let adminId = $('#admin_id').val();
                let newEmail = $('#admin_email').val();

                $.ajax({
                    url: "/admin/admin-list/" + adminId + "/update-email",
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        email: newEmail
                    },
                    success: function(data){
                        if(data.status == 'success'){
                            toastr.success(data.message);
                            $('#emailEditModal').modal('hide');
                            // Optionally, refresh the page or update the email in the table
                            location.reload(); 
                        } else {
                            toastr.error('An error occurred.');
                        }
                    },
                    error: function(xhr, status, error){
                        let errors = xhr.responseJSON.errors;
                        if(errors && errors.email){
                            toastr.error(errors.email[0]);
                        } else {
                            toastr.error('An error occurred: ' + error);
                        }
                    }
                });
            });



            // Handle Verify Email button click
            $('.verify-email-btn').on('click', function(){
                let adminId = $(this).data('id');
                $.ajax({
                    url: "/admin/admins/" + adminId + "/verify-email",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data.status == 'success'){
                            toastr.success(data.message);
                            location.reload(); 
                        } else {
                            toastr.error(data.message || 'Произошла ошибка.');
                        }
                    },
                    error: function(xhr, status, error){
                        toastr.error('Произошла ошибка: ' + error);
                    }
                });
            });

            // Handle Send Reset Link button click
            $('.send-reset-link-btn').on('click', function(){
                let adminId = $(this).data('id');
                $.ajax({
                    url: "/admin/admins/" + adminId + "/send-reset-link",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data.status == 'success'){
                            let message = 'Ссылка для сброса пароля успешно отправлена';
                            toastr.success(message);
                        } else {
                            toastr.error(data.message || 'Произошла ошибка.');
                        }
                    },
                    error: function(xhr, status, error){
                        let response = xhr.responseJSON;
                        toastr.error(response.message || 'Произошла ошибка: ' + error);
                    }
                });
            });
        })
    </script>
@endpush
