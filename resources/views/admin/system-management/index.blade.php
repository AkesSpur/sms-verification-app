@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>System Management</h1>
        </div>

        <div class="section-body">

            {{-- Cache Management --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-broom mr-2"></i>Cache Management</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">Clear various application caches. Use "Clear All" to reset everything at once.</p>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-database fa-2x text-primary mb-3"></i>
                                            <h6 class="font-weight-bold">Application Cache</h6>
                                            <p class="text-muted small mb-3">Clears all cached data stored by the app.</p>
                                            <button class="btn btn-outline-primary btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.clear-cache') }}"
                                                    data-label="Application Cache">
                                                <i class="fas fa-trash-alt mr-1"></i> Clear Cache
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-sliders-h fa-2x text-warning mb-3"></i>
                                            <h6 class="font-weight-bold">Config Cache</h6>
                                            <p class="text-muted small mb-3">Clears the configuration cache file.</p>
                                            <button class="btn btn-outline-warning btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.clear-config') }}"
                                                    data-label="Config Cache">
                                                <i class="fas fa-trash-alt mr-1"></i> Clear Config
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-eye fa-2x text-info mb-3"></i>
                                            <h6 class="font-weight-bold">View Cache</h6>
                                            <p class="text-muted small mb-3">Clears all compiled Blade view files.</p>
                                            <button class="btn btn-outline-info btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.clear-view') }}"
                                                    data-label="View Cache">
                                                <i class="fas fa-trash-alt mr-1"></i> Clear Views
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-route fa-2x text-secondary mb-3"></i>
                                            <h6 class="font-weight-bold">Route Cache</h6>
                                            <p class="text-muted small mb-3">Clears the route cache file.</p>
                                            <button class="btn btn-outline-secondary btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.clear-route') }}"
                                                    data-label="Route Cache">
                                                <i class="fas fa-trash-alt mr-1"></i> Clear Routes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border border-danger shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-fire-alt fa-2x text-danger mb-3"></i>
                                            <h6 class="font-weight-bold">Clear All Caches</h6>
                                            <p class="text-muted small mb-3">Clears application, config, view, and route caches all at once.</p>
                                            <button class="btn btn-danger btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.clear-all') }}"
                                                    data-label="All Caches"
                                                    data-confirm="true">
                                                <i class="fas fa-trash mr-1"></i> Clear All Caches
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border border-success shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-rocket fa-2x text-success mb-3"></i>
                                            <h6 class="font-weight-bold">Optimize Application</h6>
                                            <p class="text-muted small mb-3">Caches configuration and routes for faster performance.</p>
                                            <button class="btn btn-success btn-sm btn-cache-action"
                                                    data-url="{{ route('admin.system-management.optimize') }}"
                                                    data-label="Optimization">
                                                <i class="fas fa-cog mr-1"></i> Optimize
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Log Management --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-alt mr-2"></i>Log Files</h4>
                            <div class="card-header-action">
                                <small class="text-muted">Logs are automatically cleared daily at midnight.</small>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(count($logFiles) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>Size</th>
                                                <th>Last Modified</th>
                                                <th class="text-center" style="width: 160px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logFiles as $log)
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-file-alt text-muted mr-2"></i>
                                                        {{ $log['name'] }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light border js-log-size" data-file="{{ $log['name'] }}">
                                                            {{ $log['size'] }}
                                                        </span>
                                                    </td>
                                                    <td class="text-muted small">{{ $log['modified'] }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-outline-info btn-xs btn-view-log mr-1"
                                                                data-file="{{ $log['name'] }}"
                                                                data-url="{{ route('admin.system-management.read-log') }}">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-xs btn-clear-log"
                                                                data-file="{{ $log['name'] }}"
                                                                data-url="{{ route('admin.system-management.clear-log') }}">
                                                            <i class="fas fa-eraser"></i> Clear
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="mb-0">No log files found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Log Viewer Modal --}}
    <div class="modal fade" id="logViewerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        <span id="logModalTitle">Log Viewer</span>
                        <small class="ml-3 text-muted" id="logModalMeta"></small>
                    </h5>
                    <div class="ml-auto d-flex align-items-center">
                        <div class="input-group input-group-sm mr-3" style="width: 140px;">
                            <input type="number" id="logLinesInput" class="form-control form-control-sm"
                                   value="200" min="10" max="2000" title="Number of lines to load">
                            <div class="input-group-append">
                                <span class="input-group-text">lines</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light mr-2" id="btnRefreshLog">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div id="logLoading" class="text-center py-5" style="display:none;">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">Loading log content...</p>
                    </div>
                    <pre id="logContent"
                         style="background:#1e1e1e; color:#d4d4d4; font-size:12px; line-height:1.5;
                                max-height:70vh; overflow-y:auto; margin:0; padding:16px;
                                white-space:pre-wrap; word-break:break-all;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let activeLogFile = null;
    const readLogUrl  = '{{ route("admin.system-management.read-log") }}';

    // ─── Load log content into modal ────────────────────────────────────────
    function loadLog(file, lines) {
        $('#logLoading').show();
        $('#logContent').hide().text('');

        $.ajax({
            url: readLogUrl,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', file: file, lines: lines },
            success: function (response) {
                if (response.success) {
                    $('#logModalTitle').text(response.file);
                    $('#logModalMeta').text(response.size + ' — ' + response.modified);
                    $('#logContent').text(response.content);
                    // Auto-scroll to bottom
                    const pre = document.getElementById('logContent');
                    pre.scrollTop = pre.scrollHeight;
                } else {
                    $('#logContent').text('Error: ' + response.message);
                }
            },
            error: function (xhr) {
                $('#logContent').text('Error: ' + (xhr.responseJSON?.message || 'Failed to load log.'));
            },
            complete: function () {
                $('#logLoading').hide();
                $('#logContent').show();
            }
        });
    }

    // ─── View log button ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-view-log', function () {
        activeLogFile = $(this).data('file');
        const lines = parseInt($('#logLinesInput').val()) || 200;

        $('#logViewerModal').modal('show');
        loadLog(activeLogFile, lines);
    });

    // ─── Refresh button inside modal ─────────────────────────────────────────
    $('#btnRefreshLog').on('click', function () {
        if (!activeLogFile) return;
        loadLog(activeLogFile, parseInt($('#logLinesInput').val()) || 200);
    });

    // ─── Generic cache action buttons ────────────────────────────────────────
    $(document).on('click', '.btn-cache-action', function () {
        const btn           = $(this);
        const url           = btn.data('url');
        const label         = btn.data('label');
        const requireConfirm = btn.data('confirm') === true || btn.data('confirm') === 'true';

        const proceed = () => {
            const original = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                },
                complete: function () {
                    btn.prop('disabled', false).html(original);
                }
            });
        };

        if (requireConfirm) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will clear ' + label + '. Continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, clear it!'
            }).then((result) => {
                if (result.isConfirmed) proceed();
            });
        } else {
            proceed();
        }
    });

    // ─── Clear individual log file ───────────────────────────────────────────
    $(document).on('click', '.btn-clear-log', function () {
        const btn  = $(this);
        const file = btn.data('file');
        const url  = btn.data('url');

        Swal.fire({
            title: 'Clear log file?',
            text: file + ' will be emptied.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const original = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', file: file },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        btn.closest('tr').find('.js-log-size').text('0 B');

                        // Refresh modal content if that file is currently open
                        if (activeLogFile === file && $('#logViewerModal').hasClass('show')) {
                            loadLog(file, parseInt($('#logLinesInput').val()) || 200);
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to clear log.');
                },
                complete: function () {
                    btn.prop('disabled', false).html(original);
                }
            });
        });
    });

});
</script>
@endpush
