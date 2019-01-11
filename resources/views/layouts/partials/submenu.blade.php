@php
    $user = auth()->user();
@endphp
<nav class="navbar submenu">
    <div class="navbar-end">
        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                <div class="submenu-user">
                    <i class="fas fa-user"></i>
                </div>
            </a>
            <div class="navbar-dropdown is-right">
                <div class="navbar-item">
                    {{ $user->name }}
                </div>
                <div class="navbar-item">
                    <b>Rol:</b> Administrador
                </div>
                <div class="navbar-item">
                    <b>Email:</b> {{ $user->email }}
                </div>
                <hr class="navbar-divider">
                <a class="navbar-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span uk-icon="icon: sign-out; ratio: 1"></span> @lang('app.buttons.logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</nav>