<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    <div id="kt_aside_menu"
         class="kt-aside-menu "
         data-ktmenu-vertical="1"
         data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
        <ul class="kt-menu__nav ">
            @foreach(user_modules() as $key=>$value)
                @php
                    $have_child=count($value['child']);
                @endphp
                <li class="kt-menu__item {{$have_child?'kt-menu__item--submenu':""}} {{is_active_module($value['all_routes'])}}"
                    aria-haspopup="true" {{$have_child?'data-ktmenu-submenu-toggle="hover"':""}}>
                    <a href="{{$value['route']??"javascript:;"}}"
                       class="kt-menu__link {{$have_child?"kt-menu__toggle":""}}">
                        <!-- <i class="{{$value['icon']}}"></i> -->
                        {!! $value['icon'] !!}
                        <span class="kt-menu__link-text">{{$value['name']}}</span>
                        @if($have_child)
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        @endif
                    </a>
                    @if($have_child)
                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                @foreach($value['child'] as $val)
                                    <li class="kt-menu__item {{is_active_module($val['all_routes'])}}"
                                        aria-haspopup="true">
                                        <a href="{{$val['route']}}" class="kt-menu__link ">
                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                                            <span class="kt-menu__link-text">{{$val['name']}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
