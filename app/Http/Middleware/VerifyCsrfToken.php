<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * PaymentPoint webhook is an external POST and must bypass CSRF.
     */
    protected $except = [
        // Use patterns without leading slash; add wildcards to catch variations
        'webhook/paymentpoint',
        'webhook/paymentpoint/*',
        'api/webhook/paymentpoint',
        'api/webhook/paymentpoint/*',
    ];
}