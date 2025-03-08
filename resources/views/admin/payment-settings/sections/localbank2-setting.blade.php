<div class="tab-pane fade show" id="list-localbank2" role="tabpanel" aria-labelledby="list-localbank2-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{route('admin.localbank2-setting.update', 1)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Local Bank Status</label>
                    <select name="status" id="" class="form-control">
                        <option {{$localbank2Setting->status === 1 ? 'selected' : ''}} value="1">Enable</option>
                        <option {{$localbank2Setting->status === 0 ? 'selected' : ''}} value="0">Disable</option>
                    </select>
                </div>

                <div class="form-group">
                    <label> Account Name</label>
                    <input type="text" class="form-control" name="account_name" value="{{$localbank2Setting->account_name}}">
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="number" class="form-control" name="account_number" value="{{$localbank2Setting->account_number}}">
                </div>
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" name="bank_name" value="{{$localbank2Setting->bank_name}}">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
    </div>
