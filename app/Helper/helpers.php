<?php 

// set side bar item active

use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

function setActive(array $route){
   if(is_array($route)){
      foreach($route as $r){
         if(Request::routeIs($r)){
            return 'active';
         }
      }
   }
}

// Check if current route matches and return active class for navigation
function isActiveRoute($routeName, $slug = null) {
   $targetUrl = $slug ? route($routeName, $slug) : route($routeName);
   $currentUrl = url()->current();

   if ($currentUrl === $targetUrl) {
       return 'text-indigo-600 border-indigo-600 font-bold scale-105 transition-all duration-300';
   }

   return 'border-transparent hover:text-indigo-400 hover:border-indigo-400 transition-all duration-300';
}

function limitText($text, $limit = 100, $end = '...') {
   return mb_strimwidth($text, 0, $limit, $end);
}

function diffForHumans($date)
{
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    return Carbon::parse($date)->translatedFormat($format);
}

// Generate unique transaction reference
function getTrx($length = 12) {
    $characters = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $trx = '';
    for ($i = 0; $i < $length; $i++) {
        $trx .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $trx;
}

// Format amount for display with currency symbol
function showAmount($amount, $currency = '₦') {
    return $currency . number_format($amount, 2);
}

                            function formatPhoneNumber($phone) {
                                // Remove any non-digit characters
                                $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                
                                // If it starts with 1 and has 11 digits, format as US number
                                if (strlen($cleaned) == 11 && substr($cleaned, 0, 1) == '1') {
                                    $area = substr($cleaned, 1, 3);
                                    $exchange = substr($cleaned, 4, 3);
                                    $number = substr($cleaned, 7, 4);
                                    return "+1 ({$area}) {$exchange}-{$number}";
                                }
                                // If it has 10 digits, assume US number without country code
                                elseif (strlen($cleaned) == 10) {
                                    $area = substr($cleaned, 0, 3);
                                    $exchange = substr($cleaned, 3, 3);
                                    $number = substr($cleaned, 6, 4);
                                    return "+1 ({$area}) {$exchange}-{$number}";
                                }
                                // Otherwise return as-is with + prefix if not present
                                else {
                                    return strpos($phone, '+') === 0 ? $phone : '+' . $phone;
                                }
                            }

