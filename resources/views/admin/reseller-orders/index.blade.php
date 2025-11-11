@extends('admin.layouts.master')

@section('title', 'Reseller Orders')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Reseller Orders</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4>All Reseller Orders</h4>
        <form method="GET" class="w-100">
          <div class="row mb-0">
            <div class="col-md-4 mb-1">
              <input type="text" name="search" class="form-control" placeholder="Search name/email/product/order#" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 mb-1">
              <select name="status" class="form-control select2">
                <option value="">All Status</option>
                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status')==='failed' ? 'selected' : '' }}>Failed</option>
              </select>
            </div>
            <div class="col-md-2 mb-1">
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Search
              </button>
            </div>
            <div class="col-md-3 mb-1">
              <a href="{{ route('admin.reseller-orders.index') }}" class="btn btn-secondary w-100">
                <i class="fas fa-times"></i> Clear Filters
              </a>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Order #</th>
                <th>User</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
              <tr>
                <td>#RPO{{ $order->id }}</td>
                <td>{{ $order->user->name }} <small class="text-muted">{{ $order->user->email }}</small></td>
                <td>{{ $order->product->name ?? 'N/A' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>₦{{ number_format($order->total_amount, 2) }}</td>
                <td>
                  @if($order->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @elseif($order->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @else
                    <span class="badge badge-danger">Failed</span>
                  @endif
                </td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <button class="btn btn-info btn-sm" onclick="showResellerOrder({{ $order->id }})">
                    <i class="fas fa-eye"></i> Show
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div>
          {{ $orders->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal -->
<div class="modal fade" id="resellerOrderModal" tabindex="-1" role="dialog" aria-labelledby="resellerOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resellerOrderModalLabel">Reseller Order Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Order #:</strong> <span id="adminOrderNumber"></span>
          <span class="ml-3"><strong>User:</strong> <span id="adminOrderUser"></span></span>
        </div>
        <div class="mb-3">
          <strong>Product:</strong> <span id="adminOrderProduct"></span>
          <span class="ml-3"><strong>Status:</strong> <span id="adminOrderStatus"></span></span>
        </div>
        <div class="mb-2"><strong>Logs</strong></div>
        <div id="adminOrderLogs" class="list-group"></div>
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
function showResellerOrder(orderId) {
  $('#resellerOrderModal').modal('show');
  $('#adminOrderLogs').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div>');
  fetch(`{{ url('/admin/reseller-orders') }}/${orderId}`)
    .then(res => res.json())
    .then(json => {
      if (!json.success) return;
      const order = json.data;
      document.getElementById('adminOrderNumber').textContent = `#RPO${order.order_number}`;
      document.getElementById('adminOrderUser').textContent = `${order.user?.name || ''} (${order.user?.email || ''})`;
      document.getElementById('adminOrderProduct').textContent = order.product?.name || '';
      document.getElementById('adminOrderStatus').textContent = order.status;
      const container = document.getElementById('adminOrderLogs');
      container.innerHTML = '';
      if (!order.logs || order.logs.length === 0) {
        container.innerHTML = '<div class="list-group-item">No logs linked to this order.</div>';
      } else {
        order.logs.forEach(log => {
          const a = document.createElement('a');
          a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
          a.innerHTML = `
            <div>
              <div><strong>Log #${log.id}</strong> <small class="text-muted">${log.status}</small></div>
              <div class="text-muted small">${(log.sold_at ? new Date(log.sold_at).toLocaleString() : '')}</div>
            </div>
            <div>
              <a href="{{ url('/admin/reseller-product-logs') }}/${log.id}/edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
            </div>`;
          container.appendChild(a);
        });
      }
    })
    .catch(err => {
      document.getElementById('adminOrderLogs').innerHTML = '<div class="list-group-item text-danger">Failed to load order details.</div>';
      console.error('Failed to load admin order details', err);
    });
}
</script>
@endpush