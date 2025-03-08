@extends('layouts.users')

@section('content')
<div class="container mt-5">
   <h2 class="fw-bold">Transactions</h2>
   <table class="table table-hover">
       <thead class="table-light">
           <tr>
               <th>Transaction ID</th>
               <th>Amount</th>
               <th>Type</th>
               <th>Date</th>
           </tr>
       </thead>
       <tbody>
           <tr>
               <td>#001</td>
               <td>$5.00</td>
               <td>Purchase</td>
               <td>2025-02-25</td>
           </tr>
           <tr>
               <td>#002</td>
               <td>$10.00</td>
               <td>Deposit</td>
               <td>2025-02-24</td>
           </tr>
       </tbody>
   </table>
</div>

@endsection

