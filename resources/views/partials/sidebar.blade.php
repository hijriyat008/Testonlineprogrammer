<aside id="sidebar"
    class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200
         transform -translate-x-full md:translate-x-0 transition-transform duration-200">

    <div class="p-6">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-slate-900 text-white grid place-items-center font-bold">EL</div>
            <div>
                <div class="font-semibold leading-tight">E-Learning</div>
                <div class="text-xs text-slate-500">Kampus App</div>
            </div>
        </div>

        @auth
            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                <span
                    class="mt-3 inline-flex items-center rounded-xl bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
            </div>

            <nav class="mt-6 space-y-1">
                <a href="/dashboard"
                    class="flex items-center gap-2 rounded-xl px-3 py-2 hover:bg-slate-100 transition
                  {{ request()->is('dashboard') ? 'bg-slate-100 font-semibold' : '' }}">
                    <span>📚</span>
                    <span>Courses</span>
                </a>

                @if (auth()->user()->role === 'lecturer')
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-2 rounded-xl px-3 py-2 hover:bg-slate-100 transition
                  {{ request()->is('dashboard') ? 'bg-slate-100 font-semibold' : '' }}">
                        <span>📚</span>
                        <span>Reports</span>
                    </a>
                @endif

                <div class="pt-4">
                    <form method="POST" action="/logout">
                        @csrf
                        <button
                            class="w-full flex items-center gap-2 rounded-xl px-3 py-2 text-red-600 hover:bg-red-50 transition">
                            <span>🚪</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        @endauth
    </div>
</aside>

{{-- overlay mobile --}}
<div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/30 z-40 md:hidden"></div>
