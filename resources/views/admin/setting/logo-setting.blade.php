<div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{route('admin.logo-setting-update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    @if(@$logoSetting->logo)
                        <img src="{{asset(@$logoSetting->logo)}}" width="150px" alt="Логотип" class="mb-2">
                        <br>
                    @endif
                    <label>Logo</label>
                    <input type="file" class="form-control" name="logo" accept="image/*">
                    <input type="hidden" class="form-control" name="old_logo" value="{{@$logoSetting->logo}}">
                    <small class="text-muted">Recommended size: 200x50px. Formats: JPG, PNG, SVG</small>
                </div>

                <div class="form-group">
                    @if(@$logoSetting->favicon)
                        <img src="{{asset(@$logoSetting->favicon)}}" width="32px" alt="Favicon" class="mb-2">
                        <br>
                    @endif
                    <label>Favicon</label>
                    <input type="file" class="form-control" name="favicon" accept="image/*">
                    <input type="hidden" class="form-control" name="old_favicon" value="{{@$logoSetting->favicon}}">
                    <small class="text-muted">Recommended size: 32x32px. Formats: ICO, PNG</small>
                </div>



                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
