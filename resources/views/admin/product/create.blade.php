@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Product</h1>
        </div>
        <div class="mb-3">
            <a href="{{route('admin.products.index')}}" class="btn btn-primary">Back</a>
         </div>
         
        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create Product</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
                                </div>
                            
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            
                                <div class="form-group">
                                    <label for="price">Starting Price</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control summernote">{{ old('description') }}</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="country_prices">Country-Specific Prices</label>
                                    <div class="countries-list" id="country_prices">
                                        @foreach($countries as $country)
                                            <div class="country-item row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input country-checkbox" id="country_{{ $country->id }}" name="countries[{{ $country->id }}][available]" value="1">
                                                        <label class="form-check-label" for="country_{{ $country->id }}">{{ $country->country_name }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control country-price-input" name="countries[{{ $country->id }}][price]" placeholder="Price" disabled>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="customizable">Customizable</label>
                                    <select id="customizable" class="form-control" name="customizable">
                                        <option value="1">Yes</option>
                                        <option value="0" selected>N0</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            
                                <button type="submit" class="btn btn-primary">Create</button>
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
    document.addEventListener('DOMContentLoaded', function () {
        const countryCheckboxes = document.querySelectorAll('.country-checkbox');
        const countryPriceInputs = document.querySelectorAll('.country-price-input');

        countryCheckboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function () {
                const priceInput = countryPriceInputs[index];
                priceInput.disabled = !this.checked;
                if (!this.checked) {
                    priceInput.value = ''; // Clear the price if disabled
                }
            });
        });
    });
</script>

@endpush
