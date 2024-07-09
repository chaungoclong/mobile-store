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
    <script src="{{ asset('plugins/alpinejs/alpine3.min.js') }}" defer></script>

    @yield('vendor-styles')

    @yield('custom-styles')
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
<!-- Navbar -->
@include('admin.layouts.partials.navbar')

<div class="flex flex-1 pt-16">
    <!-- Sidebar -->
    @include('admin.layouts.partials.sidebar')

    <!-- Main content -->
    <main class="flex-1 pt-6 px-20 ml-64">
        <!-- Breadcrumbs -->

        <!-- Product Management -->
        <div class="bg-white p-6 rounded-lg shadow min-h-[calc(100vh-200px)] max-w-7xl mx-auto">
            @include('admin.layouts.partials.breadcrumbs')
            @yield('content')
        </div>

        <!-- Footer -->
        @include('admin.layouts.partials.footer')
    </main>
</div>

<script src="{{ asset('plugins/htmx/htmx.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
</script>

@foreach (['error', 'success', 'warning'] as $messageStatus)
    @if(session($messageStatus))
        <script>
           document.addEventListener('DOMContentLoaded', function() {
               Toast.fire(
                   {
                       'title': `{{ session($messageStatus )}}`,
                       'icon': `{{ $messageStatus }}`
                   }
               );
           })
        </script>
    @endif
@endforeach
@yield('vendor-scripts')

@yield('custom-scripts')
</body>
</html>
