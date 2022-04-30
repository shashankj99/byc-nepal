<aside class="main-sidebar sidebar-light-dark elevation-4">
    <a href="{{ url("/") }}" class="brand-link">
        <img src="{{ asset("images/img/logo.png") }}" alt="BYC Logo" class="brand-image elevation-3"
             style="height: auto; max-height: 40px; margin-top: 0.18rem;">
    </a>
    <div class="sidebar">
        <nav class="mt-5">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link @if(request()->is('*dashboard')) active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @if(auth()->user()->hasRole("Driver"))
                    <li class="nav-item">
                        <a href="{{ route('driver.pickup') }}" class="nav-link @if(request()->is('driver*')) active @endif">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Pickups</p>
                        </a>
                    </li>
                @endif
                @if(!auth()->user()->hasRole("Driver"))
                <li class="nav-item">
                    <a href="{{ route('location') }}" class="nav-link @if(request()->is('location*')) active @endif">
                        <i class="nav-icon fas fa-map"></i>
                        <p>Locations</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasRole("Customer"))
                    <li class="nav-item">
                        <a href="{{ route('customer.account') }}"
                           class="nav-link @if(request()->is('*account*')) active @endif">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>My Accounts</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link @if(request()->is('*order*') || request()->is("*pickup*")) active @endif">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                My Orders
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('customer.order.history') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bin Orders</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("customer.pickup.view") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pickup Orders</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customer.notifications') }}"
                           class="nav-link @if(request()->is('*notification*') && !request()->is("*notification/admin")) active @endif">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Pickup Notifications</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="tel:+610420355085" class="nav-link">
                            <i class="nav-icon fas fa-phone"></i>
                            <p>Give Us A Call</p>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasRole("Admin"))
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link @if(request()->is('orders*')) active @endif">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Orders
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route("orders") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bin Orders</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("pickup") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pickup Orders</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('role') }}" class="nav-link @if(request()->is('role*')) active @endif">
                            <i class="nav-icon fas fa-user-secret"></i>
                            <p>Roles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('subscription') }}"
                           class="nav-link @if(request()->is('subscription*')) active @endif">
                            <i class="nav-icon fas fa-book-open"></i>
                            <p>Subscriptions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('charity') }}"
                           class="nav-link @if(request()->is('charity*')) active @endif">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Charities</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('announcement') }}"
                           class="nav-link @if(request()->is('announcement*')) active @endif">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>Announcements</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link @if(request()->is('*notification*')) active @endif">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Notifications
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route("notification") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Customer Pickup</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("admin.notification") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>General</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('bin') }}"
                           class="nav-link @if(request()->is('bin*')) active @endif">
                            <i class="nav-icon fas fa-trash"></i>
                            <p>Bin Management</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customer') }}"
                           class="nav-link @if(request()->is('customer*')) active @endif">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Customer Management</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('account') }}"
                           class="nav-link @if(request()->is('account*')) active @endif">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Customer Accounts</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                           class="nav-link @if(request()->is('*driver*')) active @endif">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                Driver Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route("driver") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>List All Drivers</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("driver.pickup") }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pickups</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tools') }}"
                           class="nav-link @if(request()->is('tools*')) active @endif">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Tools</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
