@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Gift Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.gifts.index') }}">Gifts</a></div>
            <div class="breadcrumb-item">{{ $gift->name }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $gift->name }}</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.gifts.edit', $gift->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit"></i> Edit Gift
                            </a>
                            <a href="{{ route('admin.gifts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>{{ $gift->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Slug:</strong></td>
                                                <td><code>{{ $gift->slug }}</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Price:</strong></td>
                                                <td>
                                                    <span class="h5 text-success">{{ $settings->currency_icon }}{{ number_format($gift->price, 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Customizable:</strong></td>
                                                <td>
                                                    @if($gift->customizable)
                                                        <span class="badge badge-success">Yes</span>
                                                    @else
                                                        <span class="badge badge-secondary">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($gift->customizable && $gift->customization_cost)
                                            <tr>
                                                <td><strong>Customization Cost:</strong></td>
                                                <td>
                                                    <span class="text-info">{{ $settings->currency_icon }}{{ number_format($gift->customization_cost, 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Price (with custom):</strong></td>
                                                <td>
                                                    <span class="h5 text-primary">{{ $settings->currency_icon }}{{ number_format($gift->total_price, 2) }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if($gift->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sort Order:</strong></td>
                                                <td>{{ $gift->sort_order }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Created:</strong></td>
                                                <td>{{ $gift->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Updated:</strong></td>
                                                <td>{{ $gift->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gallery Images:</strong></td>
                                                <td>
                                                    <span class="badge badge-info">{{ $gift->images->count() }} images</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($gift->description)
                                <div class="mt-4">
                                    <h5>Description</h5>
                                    <div class="border p-3 rounded">
                                        {!! $gift->description !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                @if($gift->featured_image)
                                <div class="mb-4">
                                    <h6>Featured Image</h6>
                                    <img src="{{ asset($gift->featured_image) }}" alt="{{ $gift->name }}" 
                                         class="img-fluid rounded shadow-sm">
                                </div>
                                @endif
                                
                                @if($gift->main_image && $gift->main_image !== $gift->featured_image)
                                <div class="mb-4">
                                    <h6>Main Gallery Image</h6>
                                    <img src="{{ asset($gift->main_image) }}" alt="{{ $gift->name }}" 
                                         class="img-fluid rounded shadow-sm">
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($gift->images->count() > 0)
                        <div class="mt-5">
                            <h5>Image Gallery ({{ $gift->images->count() }} images)</h5>
                            <div class="row">
                                @foreach($gift->images as $image)
                                <div class="col-md-3 col-sm-4 col-6 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ asset($image->image_path) }}" class="card-img-top" 
                                             style="height: 200px; object-fit: cover;" alt="Gallery image">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Order: {{ $image->sort_order }}</small>
                                                @if($image->is_featured)
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-star"></i> Featured
                                                    </span>
                                                @endif
                                            </div>
                                            @if($image->alt_text)
                                            <small class="text-muted d-block mt-1">{{ $image->alt_text }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add click event to gallery images for modal view
        $('.card img').click(function() {
            const src = $(this).attr('src');
            const alt = $(this).attr('alt');
            
            // Create modal for image preview
            const modal = `
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${alt}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="${src}" class="img-fluid" alt="${alt}">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal and add new one
            $('#imageModal').remove();
            $('body').append(modal);
            $('#imageModal').modal('show');
            
            // Remove modal after hiding
            $('#imageModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        });
    });
</script>
@endpush