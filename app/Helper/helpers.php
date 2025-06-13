<?php 

// set side bar item active

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
