@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Cost Of Gift Customization</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">

                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.customization.update')}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Price</label>
                            <input class="form-control" type="number" name='price' value="{{@$customization->price}}">
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
