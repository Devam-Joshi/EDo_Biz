


  <!-- ======= Top Bar ======= -->
  <div id="topbar" class="d-flex align-items-center fixed-top">
    <div class="container d-flex justify-content-between">
        <div class="d-none d-lg-flex social-links align-items-center">
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>

            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-google"></i></i></a>
          </div>
        <div class="contact-info d-flex align-items-center">
            <i class="fas fa-phone-alt me-2"></i><a href="tel:888.507.8113">888.507.8113</a> 
            <i class="fas fa-envelope me-2"></i><a href="mailto:contact@example.com">hello@flextms.com</a>
            <!-- <div class="">
              <a href="login.html" class="login-btn"><i class="fas fa-user me-2"></i>Login</a>
            </div>  -->         
        </div>
    </div>
  </div>
 
 <!-- ======= Header ======= -->
 <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center md-between">
      <h1 class="logo"><a href="{{route('front.home')}}">FlexTMS    </a></h1>
      <!-- <h1 class="logo me-auto"><a href="index.html">FlexTMS    </a></h1> -->
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

      <nav id="navbar" class="navbar order-last order-lg-0 mx-auto">
        <ul>
          <li><a class="nav-link scrollto {{ is_active_module_front(['front.home'])}}" href="{{route('front.home')}}">Home</a></li>
          <li><a class="nav-link scrollto {{ is_active_module_front(['front.product'])}}" href="{{route('front.product')}}">Product</a></li>
          <li><a class="nav-link scrollto {{ is_active_module_front(['front.pricing']) }}" href="{{route('front.pricing')}}">Pricing</a></li>
          <li class="dropdown "><a class="" href="#"><span>Company</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="{{route('front.blog')}}">Blog</a></li>
              <li><a href="{{route('front.partner')}}">Partners</a></li>
              <li><a href="{{route('front.success_story')}}">Success Stories</a></li>
               <li><a href="{{route('front.aboutUs')}}">About Us</a></li>
                <li><a href="{{route('front.contact_us')}}">Contact</a></li>
            </ul>
          </li> 
              @if(Auth::Check())

              <li><a class="nav-link scrollto" href="{{route('front.logout')}}">Logout</a></li>
              @else
              <li><a class="nav-link scrollto" href="{{route('front.login')}}">Login</a></li>
              @endif
              


          <!-- <li><a class="nav-link scrollto" href="#contact">Contact</a></li> -->
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->
      @if( check_pay_route(['front.pricing','front.pricing_pay'])  != 'true')
        <a href="{{route('front.pricing')}}" class="appointment-btn scrollto">Start Free Trial</a>
      @endif
      

    </div>
  </header><!-- End Header -->