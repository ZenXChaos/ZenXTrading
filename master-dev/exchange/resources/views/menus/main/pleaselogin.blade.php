<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    @include('menus.main.base_nav')

    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
            </ul>
        </li>
    </ul>
</div>