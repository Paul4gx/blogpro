<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'BlogPro') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex h-screen overflow-hidden">
            <!-- Mobile Sidebar Overlay -->
            <div onclick="closeMobileSidebar()"
                 class="sidebar-overlay fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
                 style="display: none;"></div>

            <!-- Sidebar -->
            @include('layouts.admin-sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
                <!-- Top Navigation -->
                @include('layouts.admin-topbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 lg:!overflow-y-auto">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                         class="bg-green-50 border-l-4 border-green-400 p-4 m-4 rounded-r-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                            <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                         class="bg-red-50 border-l-4 border-red-400 p-4 m-4 rounded-r-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Page Header -->
                    @hasSection('header')
                        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4">
                            <div class="max-w-7xl mx-auto">
                                @yield('header')
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="p-4 sm:p-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <script>
            // Simple sidebar toggle function that works immediately
            function toggleMobileSidebar() {
                const sidebar = document.querySelector('aside');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (!sidebar) return;
                
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                
                if (isOpen) {
                    // Close sidebar
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    if (overlay) overlay.style.display = 'none';
                } else {
                    // Open sidebar
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (overlay) overlay.style.display = 'block';
                }
                
                // Update Alpine store if available
                if (window.Alpine && window.Alpine.store && window.Alpine.store('sidebar')) {
                    window.Alpine.store('sidebar').open = !isOpen;
                }
            }
            
            function closeMobileSidebar() {
                const sidebar = document.querySelector('aside');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (sidebar) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                }
                if (overlay) overlay.style.display = 'none';
                
                // Update Alpine store if available
                if (window.Alpine && window.Alpine.store && window.Alpine.store('sidebar')) {
                    window.Alpine.store('sidebar').open = false;
                }
            }
            
            // Initialize Alpine store when ready
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    open: false,
                    toggle() {
                        toggleMobileSidebar();
                    },
                    close() {
                        closeMobileSidebar();
                    }
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
        @yield('scripts')
    </body>
</html>
