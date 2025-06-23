<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Transaction Information</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td class="fw-medium">Transaction ID:</td>
                <td><span class="badge bg-light text-dark">#{{ $transaction->transaction_id }}</span></td>
            </tr>
            <tr>
                <td class="fw-medium">Type:</td>
                <td>
                    <span class="badge {{ $transaction->type === 'credit' ? 'bg-success' : 'bg-danger' }}">
                        <i class="fas fa-{{ $transaction->type === 'credit' ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ strtoupper($transaction->type) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="fw-medium">Category:</td>
                <td>{{ ucwords(str_replace('_', ' ', $transaction->category)) }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Amount:</td>
                <td class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                    {{ $transaction->type === 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                </td>
            </tr>
            <tr>
                <td class="fw-medium">Status:</td>
                <td>
                    <span class="badge 
                        @if($transaction->status === 'completed') bg-success
                        @elseif($transaction->status === 'pending') bg-warning
                        @else bg-danger
                        @endif">
                        {{ strtoupper($transaction->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="fw-medium">Date:</td>
                <td>{{ $transaction->created_at->format('M d, Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">User Information</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td class="fw-medium">Name:</td>
                <td>{{ $transaction->user->name }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Email:</td>
                <td>{{ $transaction->user->email }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Balance Before:</td>
                <td>₦{{ number_format($transaction->balance_before, 2) }}</td>
            </tr>
            <tr>
                <td class="fw-medium">Balance After:</td>
                <td>₦{{ number_format($transaction->balance_after, 2) }}</td>
            </tr>
            @if($transaction->admin)
            <tr>
                <td class="fw-medium">Processed By:</td>
                <td>{{ $transaction->admin->name }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

@if($transaction->description)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold mb-2">Description</h6>
        <div class="bg-light p-3 rounded">
            {{ $transaction->description }}
        </div>
    </div>
</div>
@endif

@if($transaction->metadata && count($transaction->metadata) > 0)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold mb-2">Additional Information</h6>
        <div class="bg-light p-3 rounded">
            @foreach($transaction->metadata as $key => $value)
                <div class="row mb-1">
                    <div class="col-4 fw-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</div>
                    <div class="col-8">{{ is_array($value) ? json_encode($value) : $value }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if($transaction->reference)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold mb-2">Related Record</h6>
        <div class="bg-light p-3 rounded">
            <div class="row">
                <div class="col-4 fw-medium">Type:</div>
                <div class="col-8">{{ class_basename($transaction->reference_type) }}</div>
            </div>
            <div class="row">
                <div class="col-4 fw-medium">ID:</div>
                <div class="col-8">#{{ $transaction->reference_id }}</div>
            </div>
            @if($transaction->reference && method_exists($transaction->reference, 'toArray'))
                @php
                    $referenceData = $transaction->reference->toArray();
                    $displayFields = ['id', 'status', 'total_amount', 'created_at'];
                @endphp
                @foreach($displayFields as $field)
                    @if(isset($referenceData[$field]))
                        <div class="row">
                            <div class="col-4 fw-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</div>
                            <div class="col-8">
                                @if($field === 'created_at')
                                    {{ \Carbon\Carbon::parse($referenceData[$field])->format('M d, Y H:i:s') }}
                                @elseif($field === 'total_amount')
                                    ₦{{ number_format($referenceData[$field], 2) }}
                                @else
                                    {{ $referenceData[$field] }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endif