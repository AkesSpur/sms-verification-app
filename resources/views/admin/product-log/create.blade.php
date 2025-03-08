@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Loggs</h1>

          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Add New Log Info</h4>
                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.product-log.store')}}" method="POST">
                        @csrf

                        <div class="form-group">
                          <label for="inputState">Product</label>
                          <select id="inputState" class="form-control main-category" name="productId">
                            <option value="">Select</option>
                            @foreach ($products as $product)
                              <option {{old('productId') == $product->id ? 'selected' : '' }} 
                               value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                          </select>
                      </div>

                        <div class="form-group">
                          <label>Log Info</label>
                          <textarea name="logInfo" value="{{old('logInfo')}}" class="summernote"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="inputState">Status</label>
                            <select id="inputState" class="form-control" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <button type="submmit" class="btn btn-primary">Add Log</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>
@endsection
