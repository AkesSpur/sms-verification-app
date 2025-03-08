@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Product</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>All Products</h4>
                    <div class="card-header-action">
                        <a href="{{route('admin.products.create')}}" class="btn btn-primary"><i class="fas fa-plus"></i> Create New</a>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <div id="table-2_wrapper"
                          class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                      </div>
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
                                          <th class="sorting" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Task Name: activate to sort column ascending">
                                              Image
                                            </th>
                                          <th class="sorting" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Task Name: activate to sort column ascending">
                                              Name
                                            </th>
                                          <th class="sorting" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Task Name: activate to sort column ascending">
                                              Category
                                            </th>
                                          <th class="sorting" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Task Name: activate to sort column ascending">
                                              Type
                                            </th>
                                          <th class="sorting" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Task Name: activate to sort column ascending">
                                              price                                              
                                            </th>
                                          <th class="sorting_disabled" rowspan="1" colspan="1"
                                              aria-label="Progress" >
                                              Stock Qty
                                          </th>
                                          <th class="sorting_disabled" rowspan="1" colspan="1"
                                            aria-label="Progress" >
                                            customizable
                                          </th>    
                                          <th class="sorting_disabled" rowspan="1" colspan="1"
                                            aria-label="Progress" >
                                            Status
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
                                    @foreach ($products as $product)

                                      <tr role="row" class="odd">

                                          <td>
                                            <div class="mt-2">
                                              {{$product->id}}
                                            </div>
                                          </td>

                                          <td>
                                            <img width='70px' src='{{ asset($product->thumb_image) }}'></img>
                                          </td>

                                          <td class="text-capitalize">
                                            <div class="mt-2">
                                              {{$product->name}}
                                            </div>
                                          </td>

                                          <td class="text-capitalize">
                                            <div class="mt-2">
                                              {{$product->category->name}}
                                            </div>
                                          </td>
                                            
                                          <td class="text-capitalize">
                                            @if ($product->type == 'pdf')
                                            <div class="badge badge-danger mt-2">
                                              {{$product->type}}
                                             </div>
                                             @elseif ($product->type == 'gift')
                                             <div class="badge badge-primary mt-2">
                                               {{$product->type}}
                                              </div>
                                            @else
                                            <div class="badge badge-info mt-2">
                                              {{$product->type}}
                                             </div>
                                            @endif
                                          </td>

                                          <td>
                                            <div class="mt-2">
                                              {{$product->price}}
                                            </div>
                                          </td>
                                          
                                          <td>
                                            <div class="mt-2">
                                              {{$product->qty}}
                                            </div>
                                          </td>


                                          <td>
                                            @if($product->customizable == '1')
                                              <div class="badge badge-success">
                                                Yes
                                              </div>
                                            @else 
                                            <div class="badge badge-danger">
                                              No
                                            </div>
                                            @endif
                                          </td>
                                          <td>
                                            @if($product->status == '1')
                                              <label class="custom-switch mt-2">
                                                  <input type="checkbox" checked name="custom-switch-checkbox" data-id="{{$product->id}}" class="custom-switch-input change-status" >
                                                  <span class="custom-switch-indicator"></span>
                                              </label>
                                            @else 
                                              <label class="custom-switch mt-2">
                                                  <input type="checkbox" name="custom-switch-checkbox" data-id="{{$product->id}}" class="custom-switch-input change-status">
                                                  <span class="custom-switch-indicator"></span>
                                              </label>
                                            @endif
                                          </td>

                                          <td> 
                                            <div class="dropdown d-inline">
                                              <button class="btn btn-dark dropdown-toggle" type="button"
                                                  id="dropdownMenuButton2" data-toggle="dropdown"
                                                  aria-haspopup="true" aria-expanded="false">
                                                  <i class='far fa-edit'></i>
                                              </button>
                                              <div class="dropdown-menu" x-placement="bottom-start"
                                                  style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                  <a class="dropdown-item has-icon" href="{{route('admin.products.edit', $product->id)}}" class='btn btn-primary'>
                                                    <i class='far fa-edit'></i> Edit Product</a>
                                                  <a class="dropdown-item has-icon" href="{{route('admin.products-image-gallery.index', ['product'=>$product->id])}}" class='btn btn-primary'>
                                                    <i class='far fa-image'></i>Image Gallery</a>
                                                  <a class="dropdown-item has-icon" href="{{route('admin.product-price', $product->id)}}" class='btn btn-primary'>
                                                    <i class='fa fa-money-bill'></i>Country Prices</a>
                                              </div>
                                          </div>
                                            <a href="{{route('admin.products.destroy', $product->id)}}" class='btn btn-danger ml-2 delete-item'><i class='far fa-trash-alt'></i></a>
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

          </div>
        </section>

@endsection

@push('scripts')

    <script>
        $(document).ready(function(){
            $('body').on('click', '.change-status', function(){
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');

                $.ajax({
                    url: "{{route('admin.product.change-status')}}",
                    method: 'PUT',
                    data: {
                        status: isChecked,
                        id: id
                    },
                    success: function(data){
                        toastr.success(data.message)
                    },
                    error: function(xhr, status, error){
                        console.log(error);
                    }
                })

            })
        })
    </script>
@endpush
