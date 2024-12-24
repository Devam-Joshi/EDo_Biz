<?php

use Illuminate\Support\Facades\Route;

$currentPath = \Request::route()->getName();

?>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('translation.Menu')</li>

                @foreach (admin_modules() as $key => $value)
                    @if (isset($value['visible']) && $value['visible'])
                        {{-- Only show if visible --}}
                        @php
                            $have_child = count($value['child']);
                            $link = $value['route'] ?? 'javascript:;';
                            if ($have_child) {
                                $link = 'javascript:;';
                            }
                        @endphp

                        <li class="{{ is_active_module($value['all_routes']) }}">
                            <a href="{{ $link }}" class="waves-effect {{ $have_child ? 'has-arrow' : '' }}">
                                <i class="{{ $value['icon'] }}"></i>
                                <span key="t-projects">{{ $value['name'] }}</span>
                            </a>

                            @if ($have_child)
                                {{-- Check if have child items --}}
                                <ul class="sub-menu" aria-expanded="false">
                                    @foreach ($value['child'] as $val)
                                        @if (isset($val['visible']) && $val['visible'])
                                            {{-- Check visibility for child items --}}
                                            <li class="{{ is_active_module($val['all_routes']) }}">
                                                <a href="{{ $val['route'] }}" key="t-p-grid">
                                                    @if (!empty($val['icon']))
                                                        <i class="{{ $val['icon'] }}"></i>
                                                    @endif
                                                    {{ $val['name'] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <!-- Sidebar -->
    </div>


</div>
<!-- Left Sidebar End -->
