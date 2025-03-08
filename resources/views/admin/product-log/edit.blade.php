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
                    <h4>Update Log Info</h4>
                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.product-log.update', $log->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                          <label for="inputState">Product</label>
                          <select id="inputState" class="form-control main-category" name="productId">
                            <option value="">Select</option>
                            @foreach ($products as $product)
                              <option  {{ $product->id == $log->product_id ? 'selected' : '' }}
                              value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                          </select>
                      </div>

                      <div class="form-group">
                        <label>Log Info</label>
                        <textarea name="logInfo" class="summernote">{!! $log->log_info !!}</textarea>
                      </div>

                        <div class="form-group">
                            <label for="inputState">Status</label>
                            <select id="inputState" class="form-control" name="status">
                                <option {{ $log->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                <option {{ $log->status == 0 ? 'selected' : '' }} value="0">Inactive</option>
                            </select>
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
