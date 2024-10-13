<header id="header" class="fixed-top">
    <div class="container-fluid d-flex align-items-center md-between">
        <h1 class="logo"><a href="index.html"><img src="{{asset('master_assets/img/index-new-img/logo.svg') }}"></a></h1>

        <nav id="navbar" class="navbar order-last order-lg-0 mx-auto">
            <ul>
                <li><a class="nav-link scrollto active" href="{{route('front.home')}}">Home</a></li>
                <li><a class="nav-link scrollto" href="{{route('front.product')}}">Product</a></li>
                <li><a class="nav-link scrollto" href="{{route('front.partners')}}">Partners</a></li>
                <li><a class="nav-link scrollto" href="{{route('front.pricing')}}">Pricing</a></li>
                <li><a class="nav-link scrollto" href="javascript:;">Blog</a></li>
                <li><a class="nav-link scrollto" href="{{route('front.aboutus')}}">About us</a></li>
                <li><a class="nav-link scrollto" href="javascript:;">Contact us</a></li>
                <li><a href="#appointment" class="appointment-btn scrollto wd-get-inner-first-blog">GET STARTED</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>

        <a href="#" class="logine-menu">Log In</a>
        <button class="sidebar-toggle">
            <i class="fas fa-align-right"></i>
        </button>

        <aside class="sidebar">
            <div class="inner-res">
                <div class="sidebar-header">
                    <button class="close-btn"><i class="fas fa-times"></i></button>
                </div>
                <ul>
                    <li><a class="nav-link scrollto active" href="{{route('front.home')}}">Home</a></li>
                    <li><a class="nav-link scrollto" href="{{route('front.product')}}">Product</a></li>
                    <li><a class="nav-link scrollto" href="{{route('front.partners')}}">Partners</a></li>
                    <li><a class="nav-link scrollto" href="{{route('front.pricing')}}">Pricing</a></li>
                    <li><a class="nav-link scrollto" href="javascript:;">Blog</a></li>
                    <li><a class="nav-link scrollto" href="{{route('front.aboutus')}}">About us</a></li>
                    <li><a class="nav-link scrollto" href="javascript:;">Contact us</a></li>
                    <li><a href="#appointment" class="appointment-btn scrollto wd-get-inner-first-blog">GET STARTED</a></li>
                </ul>
            </div>
        </aside>
        <a href="#appointment" class="appointment-btn scrollto wd-get-inner-blog">GET STARTED</a>
    </div>
</header>
