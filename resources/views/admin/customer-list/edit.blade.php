@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>User Wallet</h1>

          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Edit User Wallet Balance</h4>
                  </div>
                  <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                          <label>Amount</label>
                          <input type="number" class="form-control" name="amount" value="" required>
                        </div>
                        <button type="submmit" class="btn btn-primary">Update</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>
@endsection
