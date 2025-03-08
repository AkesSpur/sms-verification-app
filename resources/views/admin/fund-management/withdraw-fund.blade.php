@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Withdraw Funds From User</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">

                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.withdraw-user-fund',$userId)}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" type="number" name='amount' >
                        </div>

                        <button type="submmit" class="btn btn-primary">Withdraw</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>

@endsection
