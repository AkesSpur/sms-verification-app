@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Product Image Gallery</h1>
          </div>
         <div class="mb-3">
            <a href="{{route('admin.products.index')}}" class="btn btn-primary">Back</a>
         </div>
          <div class="section-body">
            <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h4>Product: {{$product->name}}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.products-image-gallery.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="">Image <code>(Multiple image supported!)</code></label>
                                <input type="file" name="image[]" class="form-control" multiple>
                                <input type="hidden" name="product" value="{{$product->id}}">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>

                  </div>
                </div>
              </div>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>All Images</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12">
                          <table class="table table-striped dataTable no-footer" id="table-2" role="grid"
                              aria-describedby="table-2_info">
                              <thead>
                                  {{-- column title --}}
                                  <tr role="row">
                                      <th class="sorting" tabindex="0" aria-controls="table-2"
                                          rowspan="1" colspan="1"
                                          aria-label="Task Name: activate to sort column ascending">
                                          Id
                                        </th>
                                      <th class="sorting_disabled" rowspan="1" colspan="1"
                                          aria-label="Progress" >
                                          Image
                                        </th>
                                      <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                          rowspan="1" colspan="1"
                                          aria-label="Due Date: activate to sort column ascending">
                                          Action
                                        </th>
                                  </tr>
                              </thead>
                              <tbody>
                                {{-- each data --}}
                                @foreach ($images as $image)

                                  <tr role="row" class="odd">

                                      <td>{{$image->id}}</td>


                                      <td>
                                        <img width='200px' src='{{ asset($image->image) }}'></img>
                                      </td>

                                      <td>
                                          <a href="{{route('admin.products-image-gallery.destroy', $image->id)}}" class='btn btn-danger ml-2 delete-item'>
                                            <i class='far fa-trash-alt'></i>
                                          </a>
                                      </td>
                                  </tr>                                                  
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>

@endsection
