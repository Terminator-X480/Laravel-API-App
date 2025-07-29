<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- for intl-tel-input -->
    <link rel="stylesheet" href='https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css'>

    <!-- JavaScript for intl-tel-input -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js'></script>
        
    <!-- JavaScript for intl-tel-input utils -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.min.js'></script>   

    <!-- load select2 style -->
    <link rel="stylesheet" href='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'>

    <!-- Load Select2 JS -->
    <script src='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href='https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'>
    <script src='https://cdn.jsdelivr.net/npm/flatpickr'></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        #sidebar {
            transform: translateX(0); /* visible */
            transition: transform 0.3s ease; /* animate transform */
            z-index: 1000;
        }
        #sidebar.slide-out {
            transform: translateX(-100%); /* move out of view */
        }
        main{
            margin-left:200px;
        }
        .user-hover-card{
            display: none;
        }
        .user-hover-area:hover .user-hover-card {
            display: block;
        }

        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.slide-out {
                transform: translateX(0);
            }
            main{
                margin-left:0;
            }
        }
    </style>
</head>
<body class="overflow-x-hidden">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
             <!-- mobile topbar -->
             <div class="p-2 d-md-none w-100 bg-dark text-white d-flex  justify-content-between align-items-center">
                <span id="toggleSidebar" style="cursor:pointer">
                    <i class="fas fa-bars"></i>
                </span>
                <span id="toggleUser" style="cursor:pointer">
                <i class="fa-solid fa-user"></i>
                </span>
            </div>
            <div class="d-md-flex justify-content-between align-items-center w-100 gap-4 d-none" id="userInfo">
                <a class="navbar-brand" href="#">Madtrek Adventures</a>
                <div class="flex-md-row gap-md-0 d-flex flex-column gap-4 ">
                    <div>
                        <span class="text-white me-3">Hey, {{ session('leads_username') }} ({{ session('leads_role') }})</span>
                        <a href="{{ route('leads.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </nav>
    <!-- Sidebar + Main Layout -->
    <div class="w-100">
        <!-- Sidebar -->
        <nav id="sidebar" class="d-md-block sidebar bg-dark text-white pt-4 px-2 position-fixed h-100 " style="min-width: 200px;">
            <ul class="nav flex-column">
                <li class="nav-item {{ request()->routeIs('leads.dashboard') ? 'bg-primary text-white border-start border-3 border-white' : '' }}">
                    <a class="nav-link text-white" href="{{ route('leads.dashboard') }}">
                        <i class="fa-solid fa-users me-2"></i> Leads
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('payments') ? 'bg-primary text-white border-start border-3 border-white' : '' }}">
                    <a class="nav-link text-white" href="{{ route('payments') }}">
                        <i class="fa-solid fa-comments-dollar me-2"></i> Payments
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('vendors') ? 'bg-primary text-white border-start border-3 border-white' : '' }}">
                    <a class="nav-link text-white" href="{{ route('vendors') }}">
                    <i class="fa-solid fa-user-tie me-2"></i> Vendors
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('status') ? 'bg-primary text-white border-start border-3 border-white' : '' }}">
                    <a class="nav-link text-white" href="{{ route('status') }}">
                    <i class="fa-solid fa-list-check me-2"></i> Status
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Content Area -->
        <div id="content" class="flex-grow-1" >
            <main class="md:px-4 md:pl-4 flex-grow-1 px-3 py-4 ">
                @yield('content')
                @stack('scripts')
                @stack('style')
            </main>
        </div>
    </div>
<script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleBtn = document.getElementById('toggleSidebar');

    const toggleUser = document.getElementById('toggleUser');
    const userInfo = document.getElementById('userInfo');

    toggleBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('slide-out');
    });
    toggleUser?.addEventListener('click', () => {
        userInfo.classList.toggle('d-none');
    });
</script>
<style>
</style>
</body>
</html>

