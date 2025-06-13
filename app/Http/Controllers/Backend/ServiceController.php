<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index()
    {
        $services = Service::orderBy('name')->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name',
            'code' => 'required|string|max:50|unique:services,code',
            'price' => 'required|numeric|min:0',
            'allow_refunds' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Service::create([
            'name' => $request->name,
            'code' => $request->code,
            'price' => $request->price,
            'allow_refunds' => $request->has('allow_refunds'),
            'status' => $request->status
        ]);

        toastr()->success('Service created successfully!');
        return redirect()->route('admin.services.index');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'code' => 'required|string|max:50|unique:services,code,' . $service->id,
            'price' => 'required|numeric|min:0',
            'allow_refunds' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service->update([
            'name' => $request->name,
            'code' => $request->code,
            'price' => $request->price,
            'allow_refunds' => $request->has('allow_refunds'),
            'status' => $request->status
        ]);

        toastr()->success('Service updated successfully!');
        return redirect()->route('admin.services.index');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        // Check if service has any orders or country-service relationships
        if ($service->orders()->exists() || $service->countries()->exists()) {
            toastr()->error('Cannot delete service. It has associated orders or country pricing.');
            return redirect()->back();
        }

        $service->delete();
        toastr()->success('Service deleted successfully!');
        return redirect()->route('admin.services.index');
    }

    /**
     * Toggle service status.
     */
    public function toggleStatus(Service $service)
    {
        $service->update([
            'status' => $service->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $service->status === 'active' ? 'activated' : 'deactivated';
        
        // Check if it's an AJAX request
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Service {$status} successfully!"
            ]);
        }
        
        toastr()->success("Service {$status} successfully!");
        return redirect()->back();
    }

    /**
     * Bulk update services.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            toastr()->error('Invalid bulk action request.');
            return redirect()->back();
        }

        $services = Service::whereIn('id', $request->service_ids);
        $count = $services->count();

        switch ($request->action) {
            case 'activate':
                $services->update(['status' => 'active']);
                toastr()->success("{$count} services activated successfully!");
                break;
            
            case 'deactivate':
                $services->update(['status' => 'inactive']);
                toastr()->success("{$count} services deactivated successfully!");
                break;
            
            case 'delete':
                // Check if any service has orders or country relationships
                $servicesWithRelations = $services->whereHas('orders')
                    ->orWhereHas('countries')
                    ->count();
                
                if ($servicesWithRelations > 0) {
                    toastr()->error('Some services cannot be deleted as they have associated orders or country pricing.');
                    return redirect()->back();
                }
                
                $services->delete();
                toastr()->success("{$count} services deleted successfully!");
                break;
        }

        return redirect()->back();
    }
}