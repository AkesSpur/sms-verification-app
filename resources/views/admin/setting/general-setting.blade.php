<div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
<div class="card border">
    <div class="card-body">
        <form action="{{route('admin.general-setting-update')}}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Site Name</label>
                <input type="text" class="form-control" name="site_name" value="{{@$generalSettings->site_name}}">
            </div>
            <div class="form-group">
                <label>Contact Email</label>
                <input type="text" class="form-control" name="contact_email" value="{{@$generalSettings->contact_email}}">
            </div>
            <div class="form-group">
                <label>Contact Phone</label>
                <input type="text" class="form-control" name="contact_phone" value="{{@$generalSettings->contact_phone}}">
            </div>
            <div class="form-group">
                <label>Contact Address</label>
                <input type="text" class="form-control" name="contact_address" value="{{@$generalSettings->contact_address}}">
            </div>
            <hr>
            <div class="form-group">
                <label>Default Currecy Name</label>
                <select name="currency_name" id="" class="form-control select2">
                    <option value="">Select</option>
                    @foreach (config('settings.currency_list', ['USD', 'EUR', 'RUB', 'NGN']) as $currency)
                    <option {{@$generalSettings->currency_name == $currency ? 'selected' : ''}} value="{{$currency}}">{{$currency}}</option>
                @endforeach

                </select>
            </div>
            <div class="form-group">
                <label>Currency Icon</label>
                <input type="text" class="form-control" name="currency_icon" value="{{@$generalSettings->currency_icon}}">
            </div>
            <div class="form-group">
                <label>Naira to Dollar Exchange Rate</label>
                <input type="number" class="form-control" name="naira_to_dollar_rate" 
                       value="{{@$generalSettings->naira_to_dollar_rate}}" 
                       step="0.01" min="0" placeholder="e.g. 1500.00">
                <small class="form-text text-muted">Current exchange rate from Naira to Dollar (1 USD = X NGN).</small>
            </div>

            <div class="form-group">
                <label>Real-time USD to NGN Exchange Rate</label>
                <div class="input-group">
                    <input type="text" class="form-control" 
                           value="{{@$generalSettings->usd_to_ngn_rate ? '1 USD = ' . number_format($generalSettings->usd_to_ngn_rate, 4) . ' NGN' : 'Not set'}}" 
                           readonly>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="updateExchangeRate">
                            <i class="fas fa-sync-alt"></i> Update Rate
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">
                    Last updated: {{@$generalSettings->exchange_rate_updated_at ? $generalSettings->exchange_rate_updated_at->format('M d, Y H:i:s') : 'Never'}}
                    <br>Current Markup: {{@$generalSettings->exchange_rate_markup_percentage ?? config('services.exchange_rate.markup_percentage', 0)}}%
                </small>
            </div>
            <div class="form-group">
                <label>Exchange Rate Markup Percentage (%)</label>
                <input type="number" class="form-control" name="exchange_rate_markup_percentage" 
                       value="{{@$generalSettings->exchange_rate_markup_percentage ?? config('services.exchange_rate.markup_percentage', 0)}}" 
                       min="0" max="100" step="0.01"
                       placeholder="e.g. 5.00">
                <small class="form-text text-muted">Percentage markup to apply to the base exchange rate (0-100%).</small>
            </div>

            <hr>
            <h5>Support Links</h5>
            <div class="form-group">
                <label>WhatsApp Support Link</label>
                <input type="url" class="form-control" name="whatsapp_support_link" 
                       value="{{@$generalSettings->whatsapp_support_link}}" 
                       placeholder="e.g. https://wa.me/+2347011780974">
                <small class="form-text text-muted">WhatsApp support link for customer assistance.</small>
            </div>
            <div class="form-group">
                <label>Telegram Support Link</label>
                <input type="url" class="form-control" name="telegram_support_link" 
                       value="{{@$generalSettings->telegram_support_link}}" 
                       placeholder="e.g. https://t.me/blizzsms">
                <small class="form-text text-muted">Telegram support link for customer assistance.</small>
            </div>
            <hr>
            <h5>Pricing Settings</h5>
            <div class="form-group">
                <label>API Price Markup Percentage (%)</label>
                <input type="number" class="form-control" name="api_price_markup_percentage" 
                       value="{{@$generalSettings->api_price_markup_percentage ?? 20.00}}" 
                       step="0.01" min="0" max="100">
                <small class="form-text text-muted">Percentage to add to API prices when no custom pricing is set.</small>
            </div>
            <div class="form-group">
                <label>Global Order Fee (₦)</label>
                <input type="number" class="form-control" name="global_order_fee"
                       value="{{@$generalSettings->global_order_fee ?? 1000}}"
                       step="0.01" min="0" placeholder="e.g. 1000.00">
                <small class="form-text text-muted">Flat fee in Naira added to every All Countries (SmsBower) order on top of the converted USD cost.</small>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="enable_dynamic_pricing"
                           name="enable_dynamic_pricing" value="1" 
                           {{@$generalSettings->enable_dynamic_pricing ? 'checked' : ''}}>
                    <label class="custom-control-label" for="enable_dynamic_pricing">Enable Dynamic Pricing</label>
                </div>
                <small class="form-text text-muted">When enabled, prices will be fetched from API and markup applied. When disabled, base service prices will be used.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#updateExchangeRate').on('click', function() {
        const button = $(this);
        const originalText = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: '{{ route("admin.update-exchange-rate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update the display
                    const rateInput = button.closest('.input-group').find('input[readonly]');
                    rateInput.val(response.data.formatted_rate);
                    
                    // Update the last updated text
                    const lastUpdatedText = button.closest('.form-group').find('.form-text');
                    lastUpdatedText.html(
                        'Last updated: ' + response.data.updated_at + 
                        '<br>Markup: ' + response.data.markup_percentage + '%'
                    );
                    
                    // Show success message
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update exchange rate';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
            },
            complete: function() {
                // Re-enable button and restore original text
                button.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush

</div>
