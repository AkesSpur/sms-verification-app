<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResellerProduct;
use App\Models\ResellerProductLog;
use App\Models\User;
use Illuminate\Http\Request;

class ResellerProductLogController extends Controller
{
    /**
     * Display logs.
     */
    public function index(Request $request)
    {
        $query = ResellerProductLog::with(['product']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(75);
        $products = ResellerProduct::active()->ordered()->get(['id', 'name']);

        return view('admin.reseller-product-log.index', compact('logs', 'products'));
    }

    public function create(Request $request)
    {
        $products = ResellerProduct::active()->ordered()->get();
        $selectedProductId = $request->get('product_id');
        $users = User::all();
        return view('admin.reseller-product-log.create', compact('products', 'selectedProductId', 'users'));
    }

    public function showAddLogsForm(Request $request)
    {
        $products = ResellerProduct::active()->ordered()->get();
        $selectedProductId = $request->get('product_id');
        return view('admin.reseller-product-log.add-logs', compact('products', 'selectedProductId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:reseller_products,id',
            'log_item' => 'required|string',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold',
            'sold_at' => 'nullable|date',
            'sold_to_user_id' => 'nullable|exists:users,id'
        ]);

        $data = $request->only(['product_id', 'log_item', 'details', 'status']);
        if ($data['status'] === 'sold') {
            $data['sold_at'] = $request->sold_at ? $request->sold_at : now();
            if ($request->filled('sold_to_user_id')) {
                $data['sold_to_user_id'] = $request->sold_to_user_id;
            }
        }

        $log = ResellerProductLog::create($data);
        $log->product->updateStock();

        toastr('Reseller product log created successfully!', 'success');
        return redirect()->route('admin.reseller-product-logs.index', ['product_id' => $data['product_id']]);
    }

    public function show(ResellerProductLog $resellerProductLog)
    {
        $resellerProductLog->load(['product', 'soldToUser']);
        return view('admin.reseller-product-log.show', compact('resellerProductLog'));
    }

    public function edit(ResellerProductLog $resellerProductLog)
    {
        $products = ResellerProduct::active()->ordered()->get(['id', 'name']);
        $users = User::all();
        return view('admin.reseller-product-log.edit', compact('resellerProductLog', 'products', 'users'));
    }

    public function update(Request $request, ResellerProductLog $resellerProductLog)
    {
        $request->validate([
            'product_id' => 'required|exists:reseller_products,id',
            'log_item' => 'required|string',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold',
            'sold_at' => 'nullable|date',
            'sold_to_user_id' => 'nullable|exists:users,id'
        ]);

        $data = $request->only(['product_id', 'log_item', 'details', 'status']);
        if ($data['status'] == 'sold') {
            $data['sold_at'] = $request->sold_at ? $request->sold_at : now();
            if ($request->filled('sold_to_user_id')) {
                $data['sold_to_user_id'] = $request->sold_to_user_id;
            }
        } elseif ($data['status'] == 'available') {
            $data['sold_at'] = null;
            $data['order_id'] = null;
            $data['sold_to_user_id'] = null;
        }

        $resellerProductLog->update($data);
        $resellerProductLog->product->updateStock();

        toastr('Reseller product log updated successfully!', 'success');
        return redirect()->route('admin.reseller-product-logs.index', ['product_id' => $data['product_id']]);
    }

    public function destroy(ResellerProductLog $resellerProductLog)
    {
        if ($resellerProductLog->status === 'sold') {
            toastr('Cannot delete sold product log! The order it belongs to should be deleted instead.', 'error');
            return redirect()->back();
        }

        $productId = $resellerProductLog->product_id;
        $product = $resellerProductLog->product;

        $resellerProductLog->delete();
        $product->updateStock();

        toastr('Reseller product log deleted successfully!', 'success');
        return redirect()->route('admin.reseller-product-logs.index', ['product_id' => $productId]);
    }

    public function getByProduct(ResellerProduct $product)
    {
        $logs = $product->logs()->orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }

    public function addLogs(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:reseller_products,id',
            'log_items' => 'required|array|min:1',
            'log_items.*' => 'nullable|string|max:10000',
            'details' => 'nullable|string',
            'status' => 'required|in:available,sold'
        ]);

        $product = ResellerProduct::findOrFail($request->product_id);
        // Filter out empty Summernote items (allow some blank inputs)
        $logItems = array_filter($request->log_items, function($item) {
            $clean = trim(strip_tags($item));
            return $clean !== '' && $clean !== '&nbsp;';
        });
        if (empty($logItems)) {
            return back()->withErrors(['log_items' => 'Please enter at least one log item.'])->withInput();
        }

        $createdCount = 0;
        foreach ($logItems as $logItem) {
            ResellerProductLog::create([
                'product_id' => $product->id,
                'log_item' => $logItem, // preserve HTML from Summernote
                'details' => $request->details,
                'status' => $request->status
            ]);
            $createdCount++;
        }

        $product->updateStock();
        toastr($createdCount . ' logs added successfully!', 'success');
        return redirect()->route('admin.reseller-product-logs.index', ['product_id' => $product->id]);
    }
}