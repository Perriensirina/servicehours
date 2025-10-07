<header>
    <div class="glassy-header">
        <img src="{{ asset('images/IDlogo.png') }}" alt="Logo" width="70px">
        <!-- <div class="hamburger-menu">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div> -->
            <div class="nav-links">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="this.closest('form').submit();" class="btn btn-outline-light btn-sm">Log Out</a>
                </form>
                <a href="/account" class="btn btn-outline-light btn-sm">Account</a>
                <a href="/" class="btn btn-outline-light btn-sm">Home</a>
            </div>
    </div>
</header>