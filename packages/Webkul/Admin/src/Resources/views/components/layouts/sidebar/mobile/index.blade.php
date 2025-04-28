<v-sidebar-drawer>
    <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
</v-sidebar-drawer>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-sidebar-drawer-template"
    >
        <x-admin::drawer
            position="left"
            width="280px"
            class="lg:hidden [&>:nth-child(3)]:!m-0 [&>:nth-child(3)]:!rounded-l-none [&>:nth-child(3)]:max-sm:!w-[80%]"
        >
            <x-slot:toggle>
                <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
            </x-slot>

            <x-slot:header>
                @if ($logo = core()->getConfigData('general.design.admin_logo.logo_image'))
                    <img
                        class="h-10"
                        src="{{ Storage::url($logo) }}"
                        alt="{{ config('app.name') }}"
                    />
                @else
                    <img
                        class="h-10"
                        src="{{ request()->cookie('dark_mode') ? asset('override/logo/dark-logo.png') : asset('override/logo/logo.png') }}"
                        id="logo-image"
                        alt="{{ config('app.name') }}"
                    />
                @endif
            </x-slot>

            <x-slot:content class="p-4">
                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                    <nav class="grid w-full gap-2">
                        @foreach (menu()->getItems('admin') as $menuItem)
                            @php
                                $hasActiveChild = $menuItem->haveChildren() && collect($menuItem->getChildren())->contains(fn($child) => $child->isActive());

                                $isMenuActive = $menuItem->isActive() == 'active' || $hasActiveChild;

                                $menuKey = $menuItem->getKey();
                            @endphp

                            <div
                                class="relative menu-item"
                                data-menu-key="{{ $menuKey }}"
                            >
                                <a
                                    href="{{ ! in_array($menuItem->getKey(), ['settings', 'configuration']) && $menuItem->haveChildren() ? 'javascript:void(0)' : $menuItem->getUrl() }}"
                                    class="flex items-center justify-between p-2 transition-colors duration-200 rounded-lg menu-link"
                                    @if ($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration']))
                                        @click.prevent="toggleMenu('{{ $menuKey }}')"
                                    @endif
                                    :class="{ 'bg-brandColor text-white': activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-950': !(activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}) }"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="{{ $menuItem->getIcon() }} text-2xl"></span>

                                        <p class="font-semibold whitespace-nowrap">{{ $menuItem->getName() }}</p>
                                    </div>

                                    @if ($menuItem->haveChildren())
                                        <span
                                            class="text-lg transition-transform duration-300 transform"
                                            :class="{ 'icon-arrow-up': activeMenu === '{{ $menuKey }}', 'icon-arrow-down': activeMenu !== '{{ $menuKey }}' }"
                                        ></span>
                                    @endif
                                </a>

                                @if ($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration']))
                                    <div
                                        class="mt-1 ml-1 overflow-hidden transition-all duration-300 border-l-2 rounded-b-lg submenu dark:border-gray-700"
                                        :class="{ 'max-h-[500px] py-2 border-l-brandColor bg-gray-50 dark:bg-gray-900': activeMenu === '{{ $menuKey }}' || {{ $hasActiveChild ? 'true' : 'false' }}, 'max-h-0 py-0 border-transparent bg-transparent': activeMenu !== '{{ $menuKey }}' && !{{ $hasActiveChild ? 'true' : 'false' }} }"
                                    >
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <a
                                                href="{{ $subMenuItem->getUrl() }}"
                                                class="block p-2 pl-10 text-sm transition-colors duration-200 submenu-link whitespace-nowrap"
                                                :class="{ 'text-brandColor font-medium bg-gray-100 dark:bg-gray-800': '{{ $subMenuItem->isActive() }}' === '1', 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800': '{{ $subMenuItem->isActive() }}' !== '1' }">
                                                {{ $subMenuItem->getName() }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>
                </div>
            </x-slot>
        </x-admin::drawer>
    </script>

    <script type="module">
        app.component('v-sidebar-drawer', {
            template: '#v-sidebar-drawer-template',

            data() {
                return { activeMenu: null };
            },

            mounted() {
                const activeElement = document.querySelector('.menu-item .menu-link.bg-brandColor');

                if (activeElement) {
                    this.activeMenu = activeElement.closest('.menu-item').getAttribute('data-menu-key');
                }
            },

            methods: {
                toggleMenu(menuKey) {
                    this.activeMenu = this.activeMenu === menuKey ? null : menuKey;
                }
            },
        });
    </script>
@endPushOnce
