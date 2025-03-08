@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Product</h1>
        </div>
        <div class="mb-3">
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Back</a>
        </div>
        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Product</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label>Preview</label>
                                    <br>
                                    <img src="{{ asset($product->thumb_image) }}" style="width:200px" alt="">
                                </div>
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" class="form-control" name="image">
                                </div>

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $product->name }}">
                                </div>

                                <div class="form-group">
                                    <label>Starting Price</label>
                                    <input type="number" class="form-control" name="price" value="{{ $product->price }}">
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control summernote">{!! $product->description !!}</textarea>
                                </div>


                                <div class="form-group">
                                    <label for="country_prices">Country-Specific Prices</label>
                                    <div class="countries-list" id="country_prices">
                                        @foreach ($countries as $country)
                                            @foreach ($countryPrices as $countryPrice)
                                                @if ($country->id == $countryPrice->country_id)
                                                    @php
                                                        $status = true;
                                                        $price = [
                                                            'id' => $countryPrice->country_id,
                                                            'price' => $countryPrice->price,
                                                        ];
                                                        break;
                                                    @endphp
                                                @endif
                                            @endforeach

                                            @if ($status == true)
                                            <div class="country-item row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            class="form-check-input country-checkbox"
                                                            id="country_{{ $country->id }}"
                                                            name="countries[{{ $country->id }}][available]"
                                                            value="1" checked>
                                                        <label class="form-check-label"
                                                            for="country_{{ $country->id }}">{{ $country->country_name }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control country-price-input"
                                                        name="countries[{{ $country->id }}][price]"
                                                        placeholder="Price" value="{{ $price['price'] }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="country-item row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            class="form-check-input country-checkbox"
                                                            id="country_{{ $country->id }}"
                                                            name="countries[{{ $country->id }}][available]"
                                                            value="1">
                                                        <label class="form-check-label"
                                                            for="country_{{ $country->id }}">{{ $country->country_name }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control country-price-input"
                                                        name="countries[{{ $country->id }}][price]"
                                                        placeholder="Price" disabled>
                                                </div>
                                            </div>
                                        @endif

                                        @php
                                        $status = null;
                                        $price = null;
                                        @endphp
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="customizable">Customizable</label>
                                    <select id="customizable" class="form-control" name="customizable">
                                        <option {{ $product->customizable == 1 ? 'selected' : '' }} value="1">Yes
                                        </option>
                                        <option {{ $product->customizable == 0 ? 'selected' : '' }} value="0">NO
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="inputState">Status</label>
                                    <select id="inputState" class="form-control" name="status">
                                        <option {{ $product->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $product->status == 0 ? 'selected' : '' }} value="0">Inactive
                                        </option>
                                    </select>
                                </div>
                                <button type="submmit" class="btn btn-primary">Save Edit</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countryCheckboxes = document.querySelectorAll('.country-checkbox');
            const countryPriceInputs = document.querySelectorAll('.country-price-input');

            countryCheckboxes.forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    const priceInput = countryPriceInputs[index];
                    priceInput.disabled = !this.checked;
                });
            });
        });
    </script>
@endpush
