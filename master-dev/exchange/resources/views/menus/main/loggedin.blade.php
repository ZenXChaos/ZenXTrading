<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    @include('menus.main.base_nav')
    
    <ul class="nav navbar-nav navbar-right">
        <li><a href="{{ url('/auth/login') }}">Login</a></li>
        <li><a href="{{ url('/auth/register') }}">Register</a></li>
    
    </ul>
</div>