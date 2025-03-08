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
                    <h4> All Logs</h4>
                    <div class="card-header-action">
                        <a href="{{route('admin.product-log.create')}}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Log</a>
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
                                              Order id
                                            </th>
                                            <th class="sorting" tabindex="0" aria-controls="table-2"
                                            rowspan="1" colspan="1"
                                            aria-label="Task Name: activate to sort column ascending">
                                            order item id
                                          </th>
                                            <th class="sorting" tabindex="0" aria-controls="table-2"
                                            rowspan="1" colspan="1"
                                            aria-label="Task Name: activate to sort column ascending">
                                            Name
                                          </th>
                                            <th class="sorting" tabindex="0" aria-controls="table-2"
                                            rowspan="1" colspan="1"
                                            aria-label="Task Name: activate to sort column ascending">
                                            Log Info
                                          </th>
                                          <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                              rowspan="1" colspan="1"
                                              aria-label="Due Date: activate to sort column ascending">
                                              Stauts
                                            </th>
                                          <th class="sorting_disabled" tabindex="0" aria-controls="table-2"
                                          rowspan="1" colspan="1"
                                          aria-label="Status: activate to sort column ascending">
                                           Action
                                          </th>
                                          
                                      </tr>
                                  </thead>
                                  <tbody>
                                    {{-- each data --}}
                                    @foreach ($logs as $log)

                                      <tr role="row" class="odd">
                                          <td>{{$log->id}}</td>

                                          <td class="text-danger">
                                            {{$log->order_id == null ? 'Not sold yet' : $log->order_id}}
                                          </td>
                                          
                                          <td>{{$log->order_item_id}}</td>

                                          <td>{{$log->product->name}}</td>

                                          <td>{{ Str::limit(strip_tags($log->log_info, 20)) }}</td>
                                              
                                          <td>
                                            @if($log->status == 1)
                                            <div class="badge badge-success mt-2">
                                              Availble 
                                             </div>
                                            @else 
                                            <div class="badge badge-danger mt-2">
                                              Sold
                                             </div>
                                            @endif
                                          </td>

                                          <td> 
                                            <a href="{{route('admin.product-log.edit', $log->id)}}" class='btn btn-primary'><i class='far fa-edit'></i></a>
                                            <a href="{{route('admin.product-log.destroy', $log->id)}}" class='btn btn-danger ml-2 delete-item'><i class='far fa-trash-alt'></i></a>
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

