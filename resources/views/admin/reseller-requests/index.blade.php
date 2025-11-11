@extends('admin.layouts.master')

@section('title', 'Reseller Requests')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Reseller Access Requests</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Reseller Requests</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Requests</h4>
                    </div>
                    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="table-1">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total Orders</th>
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $req->user->name ?? 'N/A' }}</td>
                            <td>{{ $req->user->email ?? 'N/A' }}</td>
                            <td>
                                @if($req->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($req->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $userStats[$req->user_id] ?? 0 }}</td>
                            <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($req->status == 'pending')
                                <form method="POST" action="{{ route('admin.reseller-requests.approve', $req) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.reseller-requests.reject', $req) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No reseller requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $requests->links() }}
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
        // Initialize DataTable if needed
        if ($.fn.DataTable.isDataTable('#table-1')) {
            $('#table-1').DataTable().destroy();
        }
        
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [6] } // Disable sorting on Action column
            ],
            "order": [[ 0, "asc" ]] // Sort by first column (iteration)
        });
    });
</script>
@endpush