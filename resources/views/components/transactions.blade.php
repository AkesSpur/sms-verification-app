@extends('layouts.users')

@section('content')
<div>
    <h2>Transactions</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4">No transactions found</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection