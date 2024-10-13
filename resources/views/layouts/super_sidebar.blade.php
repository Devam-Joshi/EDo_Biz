
<?php


use Illuminate\Support\Facades\Route;

$currentPath =  \Request::route()->getName();

?>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">


                <li class="menu-title" key="t-menu">@lang('translation.Menu')</li>

                <li>
                    <a href="{{route('superadmin.dashboard')}}" class="waves-effect">
                        <i class="bx bxs-widget"></i><span class="badge rounded-pill bg-info float-end"></span>
                        <span key="t-dashboards">@lang('translation.Dashboards')</span>
                    </a>

                </li>

                  <!-- <li>
                    <a href="{{route('superadmin.adminuser.index')}}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-crypto">Admin users</span>
                    </a>

                </li> -->
                <li  @if(in_array($currentPath,["superadmin.adminuser.edit","superadmin.adminuser.index","superadmin.adminuser.create"])) class="mm-active" @endif>
                    <a href="{{route('superadmin.adminuser.index')}}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-crypto">Admin users</span>
                    </a>

                </li>

                <li @if(in_array($currentPath,["superadmin.tmsuser.display","superadmin.trucks.truck_view","superadmin.trucks.edit","superadmin.trailer.trailer_view","superadmin.trailer.edit","superadmin.location.index","superadmin.location.edit","superadmin.loads.loads_view","superadmin.loads.detail"])) class="mm-active" @endif >
                    <a href="{{route('superadmin.tmsuser.index')}}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-crypto">{{__('translation.tms_user')}}</span>
                    </a>

                </li>
                <li  @if(in_array($currentPath,["superadmin.tmsuser.users","superadmin.tmsuser.create","superadmin.tmsuser.tmsedit"])) class="mm-active" @endif>
                    <a href="{{route('superadmin.tmsuser.users')}}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-crypto">Tms Users</span>
                    </a>

                </li>

                



                <li @if(in_array($currentPath,["superadmin.subscription.index","superadmin.subscription.edit"])) class="mm-active" @endif>
                    <a href="{{route('superadmin.subscription.index') }}" class="waves-effect">
                        <i class="fas fa-coins"></i>
                        <span key="t-crypto">Tms Subscriptions </span>
                    </a>

                </li>

                <!-- <li>
                    <a href="javascript:;" class="waves-effect">
                        <i class="fas fa-newspaper"></i>
                        <span key="t-crypto">Website content </span>
                    </a>

                </li> -->

                <!-- <li>
                    <a href="javascript:;" class="waves-effect">
                        <i class="fas fa-newspaper"></i>
                        <span key="t-crypto">Newsletter  </span>
                    </a>

                </li> -->


                <!-- <li>
                    <a href="javascript:;" class="waves-effect">
                        <i class="fas fa-bell"></i>
                        <span key="t-crypto">Notification</span>
                    </a>

                </li> -->

                <!-- <li>
                    <a href="javascript:;" class="waves-effect">
                        <i class="fas fa-file-invoice"></i>
                        <span key="t-crypto">Blog</span>
                    </a>

                </li> -->

                <li>
                    <a href="{{route('superadmin.driver.index')}}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-crypto">Drivers</span>
                    </a>

                </li>


                


                <!-- <li>
                    <a href="javascript:;" class="waves-effect">
                        <i class="fas fa-file-invoice"></i>
                        <span key="t-crypto">Inquiry</span>
                    </a>

                </li> -->
                <!-- <li>
                    <a href="{{route('superadmin.cms.index')}}" class="waves-effect">
                        <i class="fas fa-file-invoice"></i>
                        <span key="t-crypto">{{__('translation.cms_pages')}}</span>
                    </a>
                </li> -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-cogs"></i>
                        <span key="t-projects">Settings</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('superadmin.paytype.index')}}" key="t-p-grid">Pay Type</a></li>
                        <li><a href="{{route('superadmin.states.index')}}" key="t-p-grid">States</a></li>

                    </ul>
                </li>

                <li>
                    <a href="{{route('front.logout')}}" class="waves-effect">
                    <i class="fas fa-sign-out-alt"></i>
                        <span key="t-crypto">Logout</span>
                    </a>

                </li>









            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
