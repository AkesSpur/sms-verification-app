<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Verify Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('d-none');
        }
    </script>
    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
        }
        .nav-link {
            color: #000;
            font-weight: 500;
        }
        .nav-link:hover {
            background-color: #e9ecef;
        }
        .logout {
            margin-top: auto;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar span.d-md-inline {
                display: none !important;
            }
        }
    </style>
</head>
<body class="d-flex vh-100">
    
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar d-flex flex-column p-3 border-end">
        <h1 class="fs-5 fw-bold text-center">SMS</h1>
        <nav class="nav flex-column flex-grow-1">
            <a href="{{route('dashboard')}}" class="nav-link d-flex align-items-center py-2"><span class="me-2">📊</span> <span class="d-md-inline">Dashboard</span></a>
            <a href="{{route('user.number')}}" class="nav-link d-flex align-items-center py-2"><span class="me-2">📞</span> <span class="d-md-inline">Numbers</span></a>
            <a href="{{route('user.transaction')}}" class="nav-link d-flex align-items-center py-2"><span class="me-2">📜</span> <span class="d-md-inline">Transactions</span></a>
            <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2">⚙️</span> <span class="d-md-inline">Settings</span></a>
        </nav>
        <a href="{{route('logout')}}" class="nav-link text-danger logout d-flex align-items-center py-2"><span class="me-2">🚪</span> <span class="d-md-inline">Logout</span></a>
    </div>
    
    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <button class="btn btn-light d-md-none mb-3" onclick="toggleSidebar()">☰</button>
      <div>
         @yield('content')
      </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    
    @stack('scripts')
   </body>

</html>
