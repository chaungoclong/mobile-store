<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Admin Dashboard - Product Management</title>
{{--    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/AdminLTE.min.css') }}">--}}
    <script src="{{ asset('plugins/tailwindcss/tailwindcss.min.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}"/>
    <script src="{{ asset('plugins/alpinejs/alpine.min.js') }}" defer></script>

    @yield('styles')
</head>
<body class="bg-gray-100 flex">

<!-- Sidebar -->
@include('admin.layouts.includes.sidebar')

<!-- Main Content -->
<div class="flex-1 flex flex-col">
    <!-- Navbar -->
    @include('admin.layouts.includes.header')

    <!-- Breadcrumbs -->
    <nav class="bg-gray border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <ol class="flex items-center space-x-4">
                @yield('breadcrumbs')
            </ol>
        </div>
    </nav>

    <!-- Main Section -->
    <main class="flex-1 bg-gray-100">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('admin.layouts.includes.footer')
</div>
<script src="{{ asset('plugins/htmx/htmx.js') }}"></script>
@yield('scripts')
</body>
</html>
