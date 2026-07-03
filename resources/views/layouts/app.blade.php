<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ auth()->check() ? (auth()->user()->theme ?: 'light') : 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="/logo.jpeg" />
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

    @if(request()->routeIs('login'))
        {{ $slot }}
    @else
        {{-- NAVBAR mobile only --}}
        <x-nav sticky class="lg:hidden">
            <x-slot:brand>
                <x-app-brand />
            </x-slot:brand>
            <x-slot:actions>
                <label for="main-drawer" class="lg:hidden me-3">
                    <x-icon name="o-bars-3" class="cursor-pointer" />
                </label>
            </x-slot:actions>
        </x-nav>

        {{-- MAIN --}}
        <x-main full-width>
            {{-- SIDEBAR --}}
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

                {{-- BRAND --}}
                <x-app-brand class="px-5 pt-4" />

                {{-- MENU --}}
                <x-menu activate-by-route>

                    {{-- User --}}
                    @if($user = auth()->user())
                        <x-menu-separator />

                        <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                            <x-slot:actions>
                                <livewire:theme-toggle />
                                <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" />
                            </x-slot:actions>
                        </x-list-item>

                        <x-menu-separator />
                    @endif

                    <x-menu-item title="Dashboard" icon="o-home" link="/" />

                    @can('manage-blogs')
                        <x-menu-separator />
                        <span class="text-xs text-base-content/40 px-4 pt-2 font-bold uppercase tracking-wider block">Publications</span>
                        <x-menu-item title="Blog Categories" icon="o-folder" link="/categories" />
                        <x-menu-item title="Blog Posts" icon="o-document-text" link="/posts" />
                    @endcan

                    @can('manage-content')
                        <x-menu-separator />
                        <span class="text-xs text-base-content/40 px-4 pt-2 font-bold uppercase tracking-wider block">Clinical Content</span>
                        <x-menu-item title="FAQs" icon="o-question-mark-circle" link="/faqs" />
                        <x-menu-item title="Locations" icon="o-map-pin" link="/locations" />
                        <x-menu-item title="Policy Pages" icon="o-shield-check" link="/policies" />
                    @endcan

                    @if(auth()->user() && auth()->user()->hasAnyRole(['superadmin', 'leads_management', 'support']))
                        <x-menu-separator />
                        <span class="text-xs text-base-content/40 px-4 pt-2 font-bold uppercase tracking-wider block">Leads & Support</span>
                        @can('manage-leads')
                            <x-menu-item title="Form Submissions" icon="o-inbox" link="/submissions" />
                            <x-menu-item title="Leads Pipeline" icon="o-user-group" link="/leads" />
                        @endcan
                        @can('manage-tickets')
                            <x-menu-item title="Support Tickets" icon="o-ticket" link="/tickets" />
                        @endcan
                    @endif

                    <x-menu-separator />
                    <span class="text-xs text-base-content/40 px-4 pt-2 font-bold uppercase tracking-wider block">Administration</span>
                    @can('manage-staff')
                        <x-menu-item title="Staff Management" icon="o-users" link="/staff" />
                    @endcan
                    <x-menu-item title="Account Settings" icon="o-cog-6-tooth" link="/password" />
                </x-menu>
            </x-slot:sidebar>

            {{-- The `$slot` goes here --}}
            <x-slot:content>
                {{ $slot }}
            </x-slot:content>
        </x-main>
    @endif

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
