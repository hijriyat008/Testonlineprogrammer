<header class="bg-white border-b border-slate-200">
    <div class="px-4 md:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            {{-- tombol sidebar (mobile) --}}
            <button id="btnSidebar"
                class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 hover:bg-slate-50">
                ☰
            </button>

            <div>
                <div class="font-semibold leading-tight">@yield('page_title', 'Dashboard')</div>
                <div class="text-xs text-slate-500">@yield('page_subtitle', 'E-Learning Kampus')</div>
            </div>
        </div>

        @auth
            <div class="hidden sm:flex items-center gap-2">
                <span class="text-sm text-slate-600">Halo,</span>
                <span class="text-sm font-semibold">{{ auth()->user()->name }}</span>
            </div>
        @endauth
    </div>
</header>
