@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Add New Country</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Create Support Info</h4>

                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.country-list.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                          <label>Country Name</label>
                          <select name="country_name" id="" class="form-control select2">
                              <option value="">Select</option>
                              @foreach (config('settings.country_list') as $country)
                                  <option value="{{$country}}">{{$country}}</option>
                              @endforeach
          
                          </select>
                      </div>
                        <div class="form-group">
                            <label for="inputState">Status</label>
                            <select id="inputState" class="form-control" name="status">
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                            </select>
                        </div>
                        <button type="submmit" class="btn btn-primary">Create</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>

@endsection
