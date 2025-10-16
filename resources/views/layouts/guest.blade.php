<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $settings->site_name ?? 'Blizzlogspot' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
                
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            html, body {
    height: auto;
    min-height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
}

            .auth-bg {
                background-color: #ffffff;
                position: relative;
                overflow: hidden;
            }
            .auth-bg::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.03) 0%, transparent 50%),
                            radial-gradient(circle at 80% 20%, rgba(255, 206, 84, 0.03) 0%, transparent 50%),
                            radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.02) 0%, transparent 50%);
                pointer-events: none;
            }
            .auth-card {
                background: #ffffff;
                border-radius: 1rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                border: 1px solid #f1f5f9;
            }
            .gradient-text {
                background: linear-gradient(135deg, #1e293b 0%, #4f46e5 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-fill-color: transparent;
            }
            .auth-input {
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
                background-color: #ffffff;
                transition: all 0.3s ease;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .auth-input:focus {
                outline: none;
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }
            .auth-button {
                background: linear-gradient(135deg, #1e293b 0%, #4f46e5 100%);
                color: white;
                padding: 0.75rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .auth-button:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .auth-link {
                color: #4f46e5;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }
            .auth-link:hover {
                color: #1e293b;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased auth-bg min-h-screen ">
        <div class="flex flex-col items-center min-h-screen pt-6 pb-6 relative z-10 px-4 ">
            <div class="mb-8">
                <a href="/" class="flex items-center justify-center">
                    <h1 class="text-3xl font-bold gradient-text">
                        <i class="fas fa-mobile-alt mr-2"></i>{{$settings->site_name}}
                    </h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md auth-card px-8 py-8 mb-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
