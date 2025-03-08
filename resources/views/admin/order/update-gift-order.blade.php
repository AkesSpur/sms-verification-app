@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Update Gift Tracking Id</h1>
          </div>
          <div class="mb-3">
            <a href="{{route('admin.order.index')}}" class="btn btn-primary">Back</a>
         </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">

                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.update-gift-tracking-id',$orderItem->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Tracking Info</label>
                            <textarea name="trackingInfo" class="summernote">{!!@$orderItem->gift_tracking_id!!}</textarea>
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
