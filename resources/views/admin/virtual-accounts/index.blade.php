@extends('admin.layouts.master')

@section('title', 'Virtual Accounts')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Virtual Accounts</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Virtual Accounts</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>All Virtual Accounts</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.virtual-accounts.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search account number, name, bank, provider, user">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('admin.virtual-accounts.index') }}" class="btn btn-secondary w-100"><i class="fas fa-times"></i> Clear</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-virtual-accounts">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Account Number</th>
                                        <th>Account Name</th>
                                        <th>Bank</th>
                                        <th>Provider</th>
                                        <th>User</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($accounts as $index => $account)
                                        <tr>
                                            <td class="text-center">{{ $accounts->firstItem() + $index }}</td>
                                            <td>{{ $account->account_number ?? '-' }}</td>
                                            <td>{{ $account->account_name ?? '-' }}</td>
                                            <td>
                                                @if($account->bank_name)
                                                    {{ $account->bank_name }}
                                                @elseif($account->bank_code)
                                                    {{ $account->bank_code }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($account->provider)
                                                    <span class="badge badge-info">{{ $account->provider }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($account->user)
                                                    <div>{{ $account->user->name }}</div>
                                                    <a href="mailto:{{ $account->user->email }}">{{ $account->user->email }}</a>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($account->created_at)->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No virtual accounts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $accounts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection