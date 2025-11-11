<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResellerRequest;
use App\Models\User;
use App\Models\DigitalProductOrder;
use App\Models\GiftOrder;
use App\Models\Order;
use App\Models\SocialMediaOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResellerRequestController extends Controller
{
    /**
     * List all reseller requests with user stats.
     */
    public function index(Request $request)
    {
        $requests = ResellerRequest::with('user')->orderBy('created_at', 'desc')->paginate(20);

        // Compute total orders for each user
        $userStats = [];
        foreach ($requests as $req) {
            $userId = $req->user_id;
            $totalDigital = DigitalProductOrder::where('user_id', $userId)->count();
            $totalGift = class_exists(GiftOrder::class) ? GiftOrder::where('user_id', $userId)->count() : 0;
            $totalSms = class_exists(Order::class) ? Order::where('user_id', $userId)->count() : 0;
            $totalSocial = class_exists(SocialMediaOrder::class) ? SocialMediaOrder::where('user_id', $userId)->count() : 0;
            $userStats[$userId] = $totalDigital + $totalGift + $totalSms + $totalSocial;
        }

        return view('admin.reseller-requests.index', compact('requests', 'userStats'));
    }

    /**
     * Approve a reseller request.
     */
    public function approve(ResellerRequest $resellerRequest)
    {
        DB::transaction(function () use ($resellerRequest) {
            $resellerRequest->update([
                'status' => 'approved',
                'processed_at' => now(),
                'admin_id' => auth()->id()
            ]);
            $user = $resellerRequest->user;
            $user->update(['is_reseller' => true]);
        });

        toastr('Reseller request approved!', 'success');
        return redirect()->route('admin.reseller-requests.index');
    }

    /**
     * Reject a reseller request.
     */
    public function reject(ResellerRequest $resellerRequest)
    {
        DB::transaction(function () use ($resellerRequest) {
            $resellerRequest->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'admin_id' => auth()->id()
            ]);
            // No change to is_reseller on reject
        });

        toastr('Reseller request rejected!', 'success');
        return redirect()->route('admin.reseller-requests.index');
    }
}