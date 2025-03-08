<div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
<div class="card border">
    <div class="card-body">
        <form action="{{route('admin.paystack-setting.update', 1)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Paystack Status</label>
                <select name="status" id="" class="form-control">
                    <option {{$paystackSetting->status === 1 ? 'selected' : ''}} value="1">Enable</option>
                    <option {{$paystackSetting->status === 0 ? 'selected' : ''}} value="0">Disable</option>
                </select>
            </div>


            <div class="form-group">
                <label>Country Name</label>
                <select name="country_name" id="" class="form-control select2">
                    <option value="">Select</option>
                    @foreach (config('settings.country_list') as $country)
                        <option {{$country === $paystackSetting->country_name ? 'selected' : ''}} value="{{$country}}">{{$country}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Currency Name</label>
                <select name="currency_name" id="" class="form-control select2">
                    <option value="">Select</option>
                    @foreach (config('settings.currecy_list') as $key => $currency)
                        <option {{$currency === $paystackSetting->currency_name ? 'selected' : ''}} value="{{$currency}}">{{$key}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Paystack Public Key</label>
                <input type="text" class="form-control" name="public_key" value="{{$paystackSetting->public_key}}">
            </div>
            <div class="form-group">
                <label>Paystack Secret Key</label>
                <input type="text" class="form-control" name="secret_key" value="{{$paystackSetting->secret_key}}">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
</div>
