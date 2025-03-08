@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Footer</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Edit Country</h4>

                  </div>
                  <div class="card-body">
                    <form action="{{route('admin.country-list.update', $country->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <select name="country_name" id="" class="form-control select2">
                              <option value="">Select</option>
                              @foreach (config('settings.country_list') as $countries)
                                  <option {{@$country->country_name == $countries ? 'selected' : ''}} value="{{$countries}}">{{$countries}}</option>
                              @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="inputState">Status</label>
                          <select id="inputState" class="form-control" name="status">
                            <option {{$country->status === 1 ? 'selected': ''}} value="1">Active</option>
                            <option {{$country->status === 0 ? 'selected': ''}} value="0">Inactive</option>
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
