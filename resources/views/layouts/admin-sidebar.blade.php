<!-- Sidebar -->
<aside 
       id="mobile-sidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-0 lg:z-auto">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 border-b border-gray-700">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3" onclick="closeMobileSidebar()">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-blog text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold">BlogPro</span>
        </a>
        <button type="button" 
                onclick="closeMobileSidebar(); event.stopPropagation();"
                class="lg:hidden text-gray-400 hover:text-white p-2 focus:outline-none transition-colors cursor-pointer">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-3 space-y-1 overflow-y-auto pb-20" style="max-height: calc(100vh - 4rem);">
        <a href="{{ route('admin.dashboard') }}" 
           onclick="closeMobileSidebar()"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-home w-5 mr-3"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.posts.index') }}" 
           onclick="closeMobileSidebar()"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.posts.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-newspaper w-5 mr-3"></i>
            Posts
            @if(request()->routeIs('admin.posts.*'))
                <span class="ml-auto w-2 h-2 bg-indigo-400 rounded-full"></span>
            @endif
        </a>

        <a href="{{ route('admin.categories.index') }}" 
           onclick="closeMobileSidebar()"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-folder w-5 mr-3"></i>
            Categories
        </a>

        <a href="{{ route('admin.tags.index') }}" 
           onclick="closeMobileSidebar()"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.tags.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-tags w-5 mr-3"></i>
            Tags
        </a>

        <a href="{{ route('admin.media.index') }}" 
           onclick="closeMobileSidebar()"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.media.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-images w-5 mr-3"></i>
            Media Library
        </a>

        <div class="pt-4 mt-4 border-t border-gray-700">
            <a href="{{ route('admin.profile.edit') }}" 
               onclick="closeMobileSidebar()"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.profile.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-user w-5 mr-3"></i>
                Profile
            </a>
        </div>
    </nav>

    <!-- User Info at Bottom -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700 bg-gray-800">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400 truncate hidden sm:block">{{ Auth::user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white transition-colors p-1" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

