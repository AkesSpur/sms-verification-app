<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProduct;
use App\Models\DigitalProductLog;
use App\Models\User;
use Illuminate\Http\Request;

class DigitalProductLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DigitalProductLog::with(['product.subcategory.category']);

        // Filter by product if specified
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by status if specified
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(100);
        $products = DigitalProduct::active()->ordered()->get(['id', 'name']);

        return view('admin.digital-product-log.index', compact('logs', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = DigitalProduct::with('subcategory.category')->active()->ordered()->get();
        $selectedProductId = $request->get('product_id');

        $users = User::all();
        
        return view('admin.digital-product-log.create', compact('products', 'selectedProductId', 'users'));
    }

    /**
     * Show the form for adding multiple logs.
     */
    public function showAddLogsForm(Request $request)
    {
        $products = DigitalProduct::with('subcategory.category')->active()->ordered()->get();
        $selectedProductId = $request->get('product_id');

        return view('admin.digital-product-log.add-logs', compact('products', 'selectedProductId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:digital_products,id',
            'log_item' => 'required|string',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold',
            'sold_at' => 'nullable|date',
            'sold_to_user_id' => 'nullable|exists:users,id'
        ]);

        $data = $request->only(['product_id', 'log_item', 'details', 'status']);

        // Handle sold status fields
        if ($data['status'] == 'sold') {
            // Use provided sold_at or current time if not provided
            $data['sold_at'] = $request->sold_at ? $request->sold_at : now();
            // Set sold_to_user_id if provided
            if ($request->filled('sold_to_user_id')) {
                $data['sold_to_user_id'] = $request->sold_to_user_id;
            }
        }

        $log = DigitalProductLog::create($data);
        
        // Update product stock
        $log->product->updateStock();

        toastr('Product log created successfully!', 'success');
        return redirect()->route('admin.digital-product-logs.index', ['product_id' => $data['product_id']]);
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalProductLog $digitalProductLog)
    {
        $digitalProductLog->load(['product.subcategory.category', 'soldToUser']);
        return view('admin.digital-product-log.show', compact('digitalProductLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalProductLog $digitalProductLog)
    {
        $products = DigitalProduct::active()->ordered()->get(['id', 'name']);
        $users = User::all();

        return view('admin.digital-product-log.edit', compact('digitalProductLog', 'products', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalProductLog $digitalProductLog)
    {
        $request->validate([
            'product_id' => 'required|exists:digital_products,id',
            'log_item' => 'required|string',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold',
            'sold_at' => 'nullable|date',
            'sold_to_user_id' => 'nullable|exists:users,id'
        ]);

        $data = $request->only(['product_id', 'log_item', 'details', 'status']);

        // Handle sold status fields
        if ($data['status'] == 'sold') {
            // Use provided sold_at or current time if not provided
            $data['sold_at'] = $request->sold_at ? $request->sold_at : now();
            // Set sold_to_user_id if provided
            if ($request->filled('sold_to_user_id')) {
                $data['sold_to_user_id'] = $request->sold_to_user_id;
            }
        } elseif ($data['status'] == 'available') {
            // Clear sold fields when marking as available
            $data['sold_at'] = null;
            $data['sold_to_user_id'] = null;
        }

        $digitalProductLog->update($data);
        
        // Update product stock
        $digitalProductLog->product->updateStock();

        toastr('Product log updated successfully!', 'success');
        return redirect()->route('admin.digital-product-logs.index', ['product_id' => $data['product_id']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalProductLog $digitalProductLog)
    {
        // Check if the log is sold
        if ($digitalProductLog->status == 'sold') {
            toastr('Cannot delete sold product log! The order it belongs to should be deleted instead.', 'error');
            return redirect()->back();
        }
        
        $productId = $digitalProductLog->product_id;
        $product = $digitalProductLog->product;
        
        $digitalProductLog->delete();
        
        // Update product stock
        $product->updateStock();

        toastr('Product log deleted successfully!', 'success');
        return redirect()->route('admin.digital-product-logs.index', ['product_id' => $productId]);
    }

    /**
     * Get logs for a specific product (AJAX).
     */
    public function getByProduct(DigitalProduct $product)
    {
        $logs = $product->logs()->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }

    /**
     * Add multiple logs to a product.
     */
    public function addLogs(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:digital_products,id',
            'log_items' => 'required|array|min:1',
            'log_items.*' => 'required|string',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold'
        ]);
    
        $product = DigitalProduct::findOrFail($request->product_id);
        
        // Filter out empty log items
        $logItems = array_filter($request->log_items, function($item) {
            return !empty(trim($item));
        });
        
        if (empty($logItems)) {
            return back()->withErrors(['log_items' => 'Please enter at least one log item.'])->withInput();
        }
        
        $createdCount = 0;
        foreach ($logItems as $logItem) {
            DigitalProductLog::create([
                'product_id' => $product->id,
                'log_item' => trim($logItem),
                'details' => $request->details,
                'status' => $request->status
            ]);
            $createdCount++;
        }
        
        $product->updateStock();
        toastr($createdCount . ' logs added successfully!', 'success');
        return redirect()->route('admin.digital-product-logs.index', ['product_id' => $product->id]);
    }

    /**
     * Mark a log as available.
     */
    public function markAsAvailable(DigitalProductLog $digitalProductLog)
    {
        $digitalProductLog->markAsAvailable();
        
        toastr('Log marked as available successfully!', 'success');
        return redirect()->back();
    }
}