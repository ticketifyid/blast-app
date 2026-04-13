<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar .brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            font-size: 1rem;
        }

        .sidebar .nav-link {
            color: #495057;
            padding: .5rem 1.5rem;
            font-size: .9rem;
            border-radius: 0;
        }

        .sidebar .nav-link:hover {
            background: #f8f9fa;
            color: #000;
        }

        .sidebar .nav-link.active {
            background: #f8f9fa;
            color: #000;
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 20px;
        }

        .main-content {
            margin-left: 240px;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: none;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            font-weight: 500;
        }

        .table th {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 500;
            border-bottom: 1px solid #e9ecef;
        }

        .table td {
            font-size: .9rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 400;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="brand">Blast System</div>
        <nav class="nav flex-column mt-2">
            <a href="{{ route('campaigns.index') }}"
                class="nav-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                <i class="bi bi-send me-2"></i> Campaigns
            </a>
            <a href="{{ route('contact-groups.index') }}"
                class="nav-link {{ request()->routeIs('contact-groups.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Phonebook
            </a>
            <a href="{{ route('contacts.index') }}"
                class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                <i class="bi bi-person me-2"></i> Contacts
            </a>
            <a href="{{ route('templates.index') }}"
                class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                <i class="bi bi-file-text me-2"></i> Templates
            </a>
            <a href="{{ route('config.index') }}"
                class="nav-link {{ request()->routeIs('config.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i> Config
            </a>
        </nav>
    </div>

    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
