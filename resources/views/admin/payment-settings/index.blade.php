@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
        <section class="section">
          <div class="section-header">
            <h1>Settings</h1>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">

                    <div class="card-body">
                      <div class="row">
                        <div class="col-2">
                          <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab">Paystack</a>
                            <a class="list-group-item list-group-item-action" id="list-localbank-list" data-toggle="list" href="#list-localbank" role="tab">LocalBank</a>
                            <a class="list-group-item list-group-item-action" id="list-localbank2-list" data-toggle="list" href="#list-localbank2" role="tab">LocalBank2</a>
                          </div>
                        </div>
                        <div class="col-10">
                          <div class="tab-content" id="nav-tabContent">

                            @include('admin.payment-settings.sections.paystack-setting')

                            @include('admin.payment-settings.sections.localbank-setting')

                            @include('admin.payment-settings.sections.localbank2-setting')

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
