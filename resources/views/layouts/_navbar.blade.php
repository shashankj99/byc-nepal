<nav class="main-header navbar navbar-expand navbar-light bg-white">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        @if(auth()->user()->hasRole("Customer"))
            <li class="nav-item">
                <a href="{{ route("customer.admin.notification") }}" class="nav-link text-dark mt-2">
                    <span class="fas fa-bell fw-bold fs-4"></span>
                    @if(auth()->user()->countUnseenNotifications() > 0)
                        <span class="badge-pill badge-danger navbar-badge">
                            {{ auth()->user()->countUnseenNotifications() }}
                        </span>
                    @endif
                </a>
            </li>
        @endif
        <li class="nav-item dropdown">
            <div class="navbar-header">
                <a id="navbarDropdown" class="navbar-brand dropdown-toggle" href="#" role="button"
                   data-bs-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <span>
                        <img
                            src="https://ui-avatars.com/api/?name={{auth()->user()->first_name}}+{{ auth()->user()->last_name }}&rounded=true&size=35"
                            class="img-circle elevation-2 mr-1 mb-1" alt="User Image">
                    </span>
                    {{ auth()->user()->full_name }}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <a href="{{ route('user.show', auth()->id()) }}" class="dropdown-item">
                        View Profile
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </li>
    </ul>
</nav>
