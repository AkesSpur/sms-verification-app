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
</div>
