<div class="tab-pane fade show" id="list-localbank" role="tabpanel" aria-labelledby="list-localbank-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{route('admin.localbank-setting.update', 1)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Local Bank Status</label>
                    <select name="status" id="" class="form-control">
                        <option {{$localbankSetting->status === 1 ? 'selected' : ''}} value="1">Enable</option>
                        <option {{$localbankSetting->status === 0 ? 'selected' : ''}} value="0">Disable</option>
                    </select>
                </div>

                <div class="form-group">
                    <label> Account Name</label>
                    <input type="text" class="form-control" name="account_name" value="{{$localbankSetting->account_name}}">
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="number" class="form-control" name="account_number" value="{{$localbankSetting->account_number}}">
                </div>
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" name="bank_name" value="{{$localbankSetting->bank_name}}">
                </div>
                <div class="form-group">
                    <label>Additional Infomation</label>
                    <textarea name="extra_info" class="summernote">{!! $localbankSetting->extra_info !!}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
    </div>
