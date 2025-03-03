<?php

use App\DeviceToken;
use App\PushLog;
use App\User;
use App\Models\SerialNo;
use App\Models\Account;
use App\Models\Payment;
//use db;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

if (!function_exists('send_response')) {

    function send_response($Status, $Message = "", $ResponseData = NULL, $extraData = NULL, $null_remove = null)
    {
        $data = [];
        $valid_status = [412, 200, 401];
        if (is_array($ResponseData)) {
            $data["status"] = $Status;
            $data["message"] = $Message;
            $data["data"] = $ResponseData;
        } else if (!empty($ResponseData)) {
            $data["status"] = $Status;
            $data["message"] = $Message;
            $data["data"] = $ResponseData;
        } else {
            $data["status"] = $Status;
            $data["message"] = $Message;
            $data["data"] = new stdClass();
        }
        if (!empty($extraData) && is_array($extraData)) {
            foreach ($extraData as $key => $val) {
                $data[$key] = $val;
            }
        }
//        if ($null_remove) {
//            null_remover($data['data']);
//        }
        $header_status = in_array($data['status'], $valid_status) ? $data['status'] : 412;
        response()->json($data, $header_status)->header('Content-Type', 'application/json')->send();
        die(0);
    }
}


//function null_remover($response, $ignore = [])
//{
//    array_walk_recursive($response, function (&$item) {
//        if (is_null($item)) {
//            $item = strval($item);
//        }
//    });
//    return $response;
//}

function token_generator()
{
    return genUniqueStr('', 100, 'device_tokens', 'token', true);
}

function get_header_auth_token()
{
    $full_token = request()->header('Authorization');
    return (substr($full_token, 0, 7) === 'Bearer ') ? substr($full_token, 7) : null;
}

if (!function_exists('un_link_file')) {
    function un_link_file($image_name = "")
    {
        $pass = true;
        if (!empty($image_name)) {
            try {
                $default_url = URL::to('/');
                $get_default_images = config('constants.default');
                $file_name = str_replace($default_url, '', $image_name);
                $default_image_list = is_array($get_default_images) ? str_replace($default_url, '', array_values($get_default_images)) : [];
                if (!in_array($file_name, $default_image_list)) {
                    Storage::disk(get_constants('upload_type'))->delete($file_name);
                }
            } catch (Exception $exception) {
                $pass = $exception;
            }
        } else {
            $pass = 'Empty Field Name';
        }
        return $pass;
    }
}


function get_asset($val = "", $file_exits_check = true, $no_image_available = null)
{
    $no_image_available = ($no_image_available ?? asset(get_constants('default.no_image_available')));
    if ($val) {
        if ($file_exits_check) {
            return (file_exists(public_path($val))) ? asset($val) : $no_image_available;
        } else {
            return asset($val);
        }
    } else {
        return asset($no_image_available);
    }
}

function print_title($title)
{
    return ucfirst($title);
}

function get_constants($name)
{
    return config('constants.' . $name);
}

function calculate_percentage($amount = 0, $discount = 0)
{
    return ($amount && $discount) ? (($amount * $discount) / 100) : 0;
}

function flash_session($name = "", $value = "")
{
    session()->flash($name, $value);
}

function success_session($value = "")
{
    session()->flash('success', ucfirst($value));
}

function error_session($value = "")
{
    session()->flash('error', ucfirst($value));
}

function getDashboardRouteName()
{
    $name = 'front.dashboard';
    $user_data = Auth::user();
    if ($user_data) {
        if (in_array($user_data->type, ["admin", "local_admin",'user'])) {
            $name = 'admin.dashboard';
        }
    }
    return $name;
}

function admin_modules()
{
    return [
        [
            'route' => route('admin.dashboard'),
            'name' => __('Dashboard'),
            'icon' => 'kt-menu__link-icon fa fa-home',
            'child' => [],
            'all_routes' => [
                'admin.dashboard',
            ],
            'visible' => true
        ],
        [
            'route' => route('admin.user.index'),
            'name' => __('User'),
            'icon' => 'kt-menu__link-icon fas fa-users',
            'child' => [],
            'all_routes' => [
                'admin.user.index',
                'admin.user.show',
                'admin.user.add',
            ],
            'permission' => 'user_view', // Define permission for this menu item
            'visible' => Auth::user()->hasPermissionTo('user_view') // Check if the user has permission
        ],
        [
            'route' => route('admin.role.index'),
            'name' => __('Roles'),
            'icon' => 'kt-menu__link-icon fa fa-tasks',
            'child' => [],
            'all_routes' => [
                'admin.role.index',
                'admin.role.show',
                'admin.role.add',
            ],
            'visible' => Auth::user()->hasPermissionTo('role_view')
        ],
        [
            'route' => route('admin.permission.index'),
            'name' => __('Permission'),
            'icon' => 'kt-menu__link-icon fa fa-tasks',
            'child' => [],
            'all_routes' => [
                'admin.permission.index',
                'admin.permission.show',

                'admin.permission.add',
            ],
            'visible' => Auth::user()->hasPermissionTo(permission: 'permission_view')
        ],
        [
            'route' => route('admin.branch.index'),
            'name' => __('Branch'),
            'icon' => 'kt-menu__link-icon fa fa-tasks',
            'child' => [],
            'all_routes' => [
                'admin.branch.index',
                'admin.branch.show',

                'admin.branch.add',
            ],
            'visible' => Auth::user()->hasPermissionTo(permission: 'role_view')
        ],
        [
            'route' => 'javascript:;',
            'name' => __('Account'),
            'icon' => 'kt-menu__link-iconfas  fas fa-atom',
            'all_routes' => [
                // 'admin.account.index',
                // 'admin.account.show',
                // 'admin.account.add',
            ],
            'visible' => true,
            'child' => [
                [
                    'route' =>route('admin.account.index'),
                    'name' => __('Account'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-atom',
                    'visible' => true,
                    'all_routes' => [
                        'admin.account.index',
                        'admin.account.show',
                        'admin.account.add',
                    ],
                ],

                [
                    'route' => route('admin.account-group.index'),
                    'name' => __('Ac Group'),
                    'icon' => 'kt-menu__link-iconfas fa fa-indent',
                    'visible' => true,
                    'all_routes' => [
                        'admin.account-group.index',
                        'admin.account-group.show',
                        'admin.account-group.add',
                    ],
                ],
                [
                    'route' =>route('admin.city.index'),
                    'name' => __('Cities'),
                    'visible' => true,
                    'icon' => 'kt-menu__link-iconfas fa fa-bullseye',
                    'all_routes' => [
                        'admin.city.index',
                        'admin.city.add',
                    ],
                ],
            ],
        ],
        [
            'route' => 'javascript:;',
            'name' => __('Inquery'),
            'icon' => 'kt-menu__link-icon far fa-comments',
            'child' => [
                [
                    'route'=>route('admin.inquiry-new.index'),
                    'name' => __('Fresh Leads'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-angle-double-down',
                    'visible' => true,
                    'all_routes' => [
                        'admin.inquiry-new.index',
                        'admin.inquiry-new.show',
                        'admin.inquiry-new.add',
                    ],
                ],
                [
                    'route' =>route('admin.inquiry.index'),
                    'name' => __('Customer Inquery'),
                    'icon' => 'kt-menu__link-iconfas  fa fa-question-circle',
                    'visible' => true,
                    'all_routes' => [
                        'admin.inquiry.index',
                        'admin.inquiry.show',
                        'admin.inquiry.add',
                    ],
                ],
            ],
            'visible' => true,
            'all_routes' => [
                // 'admin.inquery.index',
                // 'admin.inquery.show',

                // 'admin.inquery.add',
            ],
        ],
        [
            'route' => 'javascript:;',
            'name' => __('Billing'),
            'icon' => 'kt-menu__link-iconfas  fas fa-atom',
            'all_routes' => [

            ],
            'visible' => true,
            'child' => [
                [
                    'route' =>route('admin.sale-order.index'),
                    'name' => __('Sale Order'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-atom',

                    'visible' => true,
                    'all_routes' => [
                        'admin.sale-order.index',
                        'admin.sale-order.show',
                        'admin.sale-order.add',
                    ],
                ],
                [
                    'route' => route('admin.sale.index'),
                    'name' => __('Sale Bill'),
                    'icon' => 'kt-menu__link-iconfas fa fa-indent',
                    'visible' => true,
                    'all_routes' => [
                        'admin.sale.index',
                        'admin.sale.show',
                        'admin.sale.add',
                    ],
                ],
                [
                    'route' =>route('admin.purchase-order.index'),
                    'name' => __('Purchase Order'),
                    'icon' => 'kt-menu__link-iconfas fa fa-bullseye',
                    'visible' => true,
                    'all_routes' => [
                        'admin.purchase-order.index',
                        'admin.purchase-order.add',
                        'admin.purchase-order.show',
                    ],
                ],
                [
                    'route' =>route('admin.purchase.index'),
                    'name' => __('Purchase'),
                    'icon' => 'kt-menu__link-iconfas fa fa-bullseye',
                    'visible' => true,
                    'all_routes' => [
                        'admin.purchase.index',
                        'admin.purchase.add',
                        'admin.purchase.show',
                    ],
                ],
            ],
        ],
        [
            'route' => 'javascript:;',
            'name' => __('Payments'),
            'icon' => 'kt-menu__link-iconfas  fas fa-rupee-sign',
            'all_routes' => [
                // 'admin.account.index',
                // 'admin.account.show',
                // 'admin.account.add',
            ],
            'visible' => true,
            'child' => [
                [
                    'route' =>route('admin.payment.inward.index'),
                    'name' => __('Inward'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-angle-double-down',
                    'visible' => true,
                    'all_routes' => [
                        'admin.payment.inward.index',
                        'admin.payment.inward.show',
                        'admin.payment.inward.add',
                    ],
                ],
                [
                    'route' =>route('admin.payment.outward.index'),
                    'name' => __('Outward'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-angle-double-up',
                    'visible' => true,
                    'all_routes' => [
                        'admin.payment.outward.index',
                        'admin.payment.outward.show',
                        'admin.payment.outward.add',
                    ],
                ],
                [
                    'route' => route('admin.payment.transfer.index'),
                    'visible' => true,
                    'name' => __('Transfer'),
                    'icon' => 'kt-menu__link-iconfas fa fa-exchange-alt',
                    'all_routes' => [
                        'admin.payment.transfer.index',
                        'admin.payment.transfer.show',
                        'admin.payment.transfer.add',
                    ],
                ],
            ],
        ],
        [
            'route' => 'javascript:;',
            'name' => __('Product'),
            'icon' => 'kt-menu__link-iconfas  fas fa-atom',
            'all_routes' => [
                // 'admin.user.index',
                // 'admin.user.show',
                // 'admin.user.add',
            ],
            'visible' => true,
            'child' => [
                [
                    'route' =>route('admin.product.index'),
                    'name' => __('Products'),
                    'icon' => 'kt-menu__link-iconfas  fas fa-atom',
                    'visible' => true,
                    'all_routes' => [
                        'admin.product.index',
                        'admin.product.show',
                        'admin.product.add',
                    ],
                ],

                [
                    'route' => route('admin.category.index'),
                    'name' => __('Categories'),
                    'icon' => 'kt-menu__link-iconfas fa fa-indent',
                    'visible' => true,
                    'all_routes' => [
                        'admin.category.index',
                        'admin.category.show',
                        'admin.category.add',
                    ],
                ],
                [
                    'route' =>route('admin.color.index'),
                    'name' => __('Colors'),
                    'icon' => 'kt-menu__link-iconfas fa fa-bullseye',
                    'visible' => true,
                    'all_routes' => [
                        'admin.color.index',
                    ],
                ],
                [
                    'route' => 'javascript:;',
                    'name' => __('GST'),
                    'icon' => 'kt-menu__link-iconfas fas fa-money-bill',
                    'visible' => true,
                    'all_routes' => [

                    ],
                ],
                [
                    'route' => 'javascript:;',
                    'name' => __('Stocks'),
                    'icon' => 'kt-menu__link-iconfas fas fa-shapes',
                    'visible' => true,
                    'all_routes' => [

                    ],
                ],
                [
                    'route' => 'javascript:;',
                    'name' => __('Price Update'),
                    'icon' => 'kt-menu__link-iconfas far fa-life-ring',
                    'visible' => true,
                    'all_routes' => [

                    ],
                ],
                [
                    'route' => 'javascript:;',
                    'name' => __('Stock Status'),
                    'icon' => 'kt-menu__link-iconfas fas fa-archive',
                    'visible' => true,
                    'all_routes' => [

                    ],
                ],

            ],
        ],

        [
            'route' => 'javascript:;',
            'name' => __('General Settings'),
            'icon' => 'kt-menu__link-icon fa fa-home',
            'all_routes' => [
                'admin.get_update_password',
                'admin.get_site_settings',
            ],
            'visible' => true,
            'child' => [
                [
                    'route' => route('admin.get_update_password'),
                    'name' => 'Change Password',
                    'visible' => true,
                    'icon' => '',
                    'all_routes' => [
                        'admin.get_update_password',
                    ],
                ],
                [
                    'route' => route('admin.get_site_settings'),
                    'name' => 'Site Settings',
                    'visible' => true,
                    'icon' => '',
                    'all_routes' => [
                        'admin.get_site_settings',
                    ],
                ],
                [
                    'route' => route('admin.generate_stock_qrcode'),
                    'name' => 'Generate Stock Qrcode',
                    'icon' => '',
                    'visible' => true,
                    'all_routes' => [
                        'admin.generate_stock_qrcode',
                    ],
                ]
            ],
        ],
        [
            'route' => route('front.logout'),
            'name' => __('Logout'),
            'icon' => 'kt-menu__link-icon fas fa-sign-out-alt',
            'child' => [],
            'all_routes' => [],
            'visible' => true
        ],
    ];
}


function get_error_html($error)
{
    $content = "";
    if ($error->any() !== null && $error->any()) {
        foreach ($error->all() as $value) {
            $content .= '<div class="alert alert-danger alert-dismissible mb-1" role="alert">';
            $content .= '<div class="alert-text">' . $value . '</div>';
            $content .= '<div class="alert-close"><i class="flaticon2-cross kt-icon-sm" data-dismiss="alert"></i></div></div>';
        }
    }
    return $content;
}


function breadcrumb($aBradcrumb = array())
{
    $i = 0;
    $content = '';
    $is_login = Auth::user();
    if ($is_login) {
        if ($is_login->type == "admin") {
            $aBradcrumb = array_merge(['Home' => route('admin.dashboard')], $aBradcrumb);
        } elseif ($is_login->type == "vendor") {
            $aBradcrumb = array_merge(['Home' => route('vendor.dashboard')], $aBradcrumb);
        }
    }
    if (is_array($aBradcrumb) && count($aBradcrumb) > 0) {
        $total_bread_crumbs = count($aBradcrumb);
        foreach ($aBradcrumb as $key => $link) {
            $i += 1;
            $link = (!empty($link)) ? $link : 'javascript:void(0)';

            $content .=  '<li class="breadcrumb-item"> <a href="'.$link.'">'. ucfirst($key).'</a>';


            // $content .= "<a href='" . $link . "' class='kt-subheader__breadcrumbs-link'>" . ucfirst($key) . "</a>";
            // if ($total_bread_crumbs != $i) {
            //     $content .= "<span class='kt-subheader__breadcrumbs-separator'></span>";
            // }
        }
    }
    return $content;
}

function success_error_view_generator()
{
    $content = "";
    if (session()->has('error')) {
        $content = '<div class="alert alert-danger alert-dismissible" role="alert">
                                        <div class="alert-text">' . session('error') . '</div>
                                        <div class="alert-close"><i class="flaticon2-cross kt-icon-sm"
                                                                    data-dismiss="alert"></i></div></div>';
    } elseif (session()->has('success')) {
        $content = '<div class="alert alert-success alert-dismissible" role="alert">
                                        <div class="alert-text">' . session('success') . '</div>
                                        <div class="alert-close"><i class="flaticon2-cross kt-icon-sm"
                                                                    data-dismiss="alert"></i></div></div>';
    }
    return $content;
}

function datatable_filters()
{
    $post = request()->all();
    return array(
        'offset' => isset($post['start']) ? intval($post['start']) : 0,
        'limit' => isset($post['length']) ? intval($post['length']) : 25,
        'sort' => isset($post['columns'][(isset($post["order"][0]['column'])) ? $post["order"][0]['column'] : 0]['data']) ? $post['columns'][(isset($post["order"][0]['column'])) ? $post["order"][0]['column'] : 0]['data'] : 'created_at',
        'order' => isset($post["order"][0]['dir']) ? $post["order"][0]['dir'] : 'DESC',
        'search' => isset($post["search"]['value']) ? $post["search"]['value'] : '',
        'sEcho' => isset($post['sEcho']) ? $post['sEcho'] : 1,
    );
}

function send_push($user_id = 0, $data = [], $notification_entry = false)
{
//    $sample_data = [
//        'push_type' => 0,
//        'push_message' => 0,
//        'from_user_id' => 0,
//        'push_title' => 0,
//////        'push_from' => 0,
//        'object_id' => 0,
//        'extra' => [
//            'jack' => 1
//        ],
//    ];


    $pem_secret = '';
    $bundle_id = 'com.zb.project.Bambaron';
    $pem_file = base_path('storage/app/uploads/user.pem');
    $main_name = defined('site_name') ? site_name : env('APP_NAME');
    $push_data = [
        'user_id' => $user_id,
        'from_user_id' => $data['from_user_id'] ?? null,
        'sound' => 'defualt',
        'push_type' => $data['push_type'] ?? 0,
        'push_title' => $data['push_title'] ?? $main_name,
        'push_message' => $data['push_message'] ?? "",
        'object_id' => $data['object_id'] ?? null,
    ];
    if ($push_data['user_id'] !== $push_data['from_user_id']) {
//        $to_user_data = User::find($user_id);
//        if ($to_user_data) {
        $get_user_tokens = DeviceToken::get_user_tokens($user_id);
        $fire_base_header = ["Authorization: key=" . config('constants.firebase_server_key'), "Content-Type: application/json"];
        if (count($get_user_tokens)) {
            foreach ($get_user_tokens as $value) {
                $curl_extra = [];
                $push_status = "Sent";
                $value->update(['badge'=>$value->badge+1]);
                try {
                    $device_token = $value['push_token'];
                    $device_type = strtolower($value['type']);
                    if ($device_token) {
                        if ($device_type == "ios") {
                            $headers = ["apns-topic: $bundle_id"];
                            $url = "https://api.development.push.apple.com/3/device/$device_token";
                            $payload_data = [
                                'aps' => [
                                    'badge' => $value->badge,
                                    'alert' => $push_data['push_message'],
                                    'sound' => 'default',
                                    'push_type' => $push_data['push_type']
                                ],
                                'payload' => [
                                    'to' => $value['push_token'],
                                    'notification' => ['title' => $push_data['push_title'], 'body' => $push_data['push_message'], "sound" => "default", "badge" => $value->badge],
                                    'data' => $push_data,
                                    'priority' => 'high'
                                ]
                            ];
                            $curl_extra = [
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                                CURLOPT_SSLCERT => $pem_file,
                                CURLOPT_SSLCERTPASSWD => $pem_secret,
                            ];
                        } else {
                            $headers = $fire_base_header;
                            $url = "https://fcm.googleapis.com/fcm/send";
                            $payload_data = [
                                'to' => $value['push_token'],
                                'data' => $push_data,
                                'priority' => 'high'
                            ];
                        }
                        $ch = curl_init($url);
                        curl_setopt_array($ch, array_merge([
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POSTFIELDS => json_encode($payload_data),
                            CURLOPT_POST => 1,
                            CURLOPT_HTTPHEADER => $headers,
                        ], $curl_extra));
                        $result = curl_exec($ch);
//                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if (curl_errno($ch)) {
                            $push_status = 'Curl Error:' . curl_error($ch);
                        }
                        curl_close($ch);
                        if (config('constants.push_log')) {
                            PushLog::add_log($user_id, $push_data['from_user_id'], $push_data['push_type'], $push_status, json_encode($push_data), $result);
                        }
                    } else {
                        PushLog::add_log($user_id, $push_data['from_user_id'], $push_data['push_type'], "Token Is empty");
                    }
                } catch (Exception $e) {
                    if (config('constants.push_log')) {
                        PushLog::add_log($user_id, $push_data['from_user_id'], $push_data['push_type'], $e->getMessage());
                    }
                }
            }
        } else {
            if (config('constants.push_log')) {
                PushLog::add_log($user_id, $push_data['from_user_id'], $push_data['push_type'], "Users Is not A Login With app");
            }
        }
//            if ($notification_entry) {
//                Notification::create([
//                    'push_type' => $push_data['push_type'],
//                    'user_id' => $push_data['user_id'],
//                    'from_user_id' => $push_data['from_user_id'],
//                    'push_title' => $push_data['push_title'],
//                    'push_message' => $push_data['push_message'],
//                    'object_id' => $push_data['object_id'],
//                    'extra' => (isset($data['extra']) && !empty($data['extra'])) ? json_encode($data['extra']) : json_encode([]),
//                    'country_id' => $push_data['country_id'],
//                ]);
//            }

//        }
    } else {
        if (config('constants.push_log')) {
            PushLog::add_log($user_id, $push_data['from_user_id'], $push_data['push_type'], "User Cant Sent Push To Own Profile.");
        }
    }
}

function number_to_dec($number = "", $show_number = 2, $separated = '.', $thousand_separator = "")
{
    return number_format($number, $show_number, $separated, $thousand_separator);
}

function genUniqueStr($prefix = '', $length = 10, $table, $field, $isAlphaNum = false)
{
    $arr = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
    if ($isAlphaNum) {
        $arr = array_merge($arr, array(
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z'));
    }
    $token = $prefix;
    $maxLen = max(($length - strlen($prefix)), 0);
    for ($i = 0; $i < $maxLen; $i++) {
        $index = rand(0, count($arr) - 1);
        $token .= $arr[$index];
    }
    if (isTokenExist($token, $table, $field)) {
        return genUniqueStr($prefix, $length, $table, $field, $isAlphaNum);
    } else {
        return $token;
    }
}

function isTokenExist($token, $table, $field)
{
    if ($token != '') {
        $isExist = DB::table($table)->where($field, $token)->count();
        if ($isExist > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function get_fancy_box_html($path = "", $class = "img_75")
{
    return '<a data-fancybox="gallery" href="' . $path . '"><img class="' . $class . '" src="' . $path . '" alt="image" width=40 height=40></a>';
}

function general_date($date)
{
    return date('Y-m-d', strtotime($date));
}

function current_route_is_same($name = "")
{
    return $name == request()->route()->getName();
}

function is_selected_blade($id = 0, $id2 = "")
{
    return ($id == $id2) ? "selected" : "";
}

function clean_number($number)
{
    return preg_replace('/[^0-9]/', '', $number);
}

function print_query($builder)
{
    $addSlashes = str_replace('?', "'?'", $builder->toSql());
    return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
}

function user_status($status = "", $is_delete_at = false)
{
    if ($is_delete_at) {
        $status = "<span class='badge badge-danger'>Deleted</span>";
    } elseif ($status == "inactive") {
        $status = "<span class='badge badge-warning'>" . ucfirst($status) . "</span>";
    } else {
        $status = "<span class='badge badge-success'>" . ucfirst($status) . "</span>";
    }
    return $status;
}


function is_active_module($names = [])
{
    $current_route = request()->route()->getName();
    return in_array($current_route, $names) ? "kt-menu__item--active kt-menu__item--open" : "";
}

function echo_extra_for_site_setting($extra = "")
{
    $string = "";
    $extra = json_decode($extra);
    if (!empty($extra) && (is_array($extra) || is_object($extra))) {
        foreach ($extra as $key => $item) {
            $string .= $key . '="' . $item . '" ';
        }
    }
    return $string;
}

function upload_file($file_name = "", $path = null)
{
    $file = "";
    $request = \request();
    if ($request->hasFile($file_name) && $path) {
        $path = config('constants.upload_paths.' . $path);
        $file = $request->file($file_name)->store($path, config('constants.upload_type'));
    } else {
        echo 'Provide Valid Const from web controller';
        die();
    }
    return $file;
}

function upload_base_64_img($base64 = "", $path = "uploads/product/")
{
    $file = null;
    if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
        $data = substr($base64, strpos($base64, ',') + 1);
        $up_file = rtrim($path, '/') . '/' . md5(uniqid()) . '.png';
        $img = Storage::disk('local')->put($up_file, base64_decode($data));
        if ($img) {
            $file = $up_file;
        }
    }
    return $file;
}

function getNewSerialNo($type){
    $billNo=SerialNo::where('name','=',$type)
                    ->select('prefix','length','financialYear','next_number')
                    ->first();
        $next_number=str_pad($billNo->next_number, $billNo->length, "0", STR_PAD_LEFT);
        return $billNo->prefix.$billNo->financialYear.$next_number;
}

function increaseSerialNo($type){
    SerialNo::where('name','=',$type)->increment('next_number',1);
}


function partyCalculateClosing($id,$from=null,$to=null)
{
    $account=Account::where('id',$id)->first();
    $april1=$_ENV['APP_YEAR'];
    $fromDate=$april1;
    $toDate=date('Y-m-d');

    if(isset($from,$to) && !empty($from) && !empty($to)){
        $fromDate=$from;
        $toDate=$to;
    }

    $prevDate=date('Y-m-d',strtotime($fromDate.'-1 day'));
    $totalDebit=Payment::where('party_id',$id)
                    ->where('txn_type','debit')
                    ->where('status',1)
                    ->where('txn_date','>=',$april1)
                    ->where('txn_date','<=',$toDate)
                    ->sum('txn_amount');

    $totalCredit=Payment::where('party_id',$id)
                    ->where('txn_type','credit')
                    ->where('status',1)
                    ->where('txn_date','>=',$april1)
                    ->where('txn_date','<=',$toDate)
                    ->sum('txn_amount');
    $acc= Account::where('id',$id)->select('openingBalance','opening_type')->first();

    //=======New Calculation ====

    if(!empty($acc->openingBalance)){ $opBal=$acc->openingBalance; }else{ $opBal=0;	}
    if($acc->opening_type=='Dr'){
        $closingBalance_new = $opBal + ($totalDebit-$totalCredit);
    }else{
        $closingBalance_new = $opBal - ($totalDebit-$totalCredit);
    }

    if($closingBalance_new >= 0 ){
        $closingType='Dr';

    }else{
        $closingType='Cr';
    }

    $a['opening']=$acc->openingBalance;
    $a['opening_type']=$acc->opening_type;
    $a['debitTotal']=$totalDebit;
    $a['creditTotal']=$totalCredit;
    $a['closing']=$closingBalance_new;
    $a['closing_type']=$closingType;
    $a['showbalance']=accountBalanceView($closingBalance_new,$closingType);

    return $a;
}

function accountBalanceView($amt,$type){
    $amt=number_to_dec($amt);
    if($type=='Dr'){
        $html='<span class="text-success p-2 w-100">'.$amt.' '.$type.' <span class="badge badge-success">&darr;<span> </span>';
    }else{
        $html='<span class="text-danger p-2 w-100">'.$amt.' '.$type.' <span class="badge badge-danger">&uarr;<span><span>';
    }
    return $html;
}

function myDateFormat($datestring){
    return date('d-M-Y',strtotime($datestring));
};

function stockInfo($idqr){
    $st=DB::table('tbl_products_stock AS st')
            ->join('tbl_products_master AS p',function($join)
            {
              $join->on('p.id', '=', 'st.product_id');
            })
            ->join('tbl_categories AS pc', function($join)
            {
              $join->on('pc.id', '=', 'st.category_id');
            })
            ->join('tbl_color AS atr', function($join)
            {
              $join->on('atr.id', '=', 'st.attribute_id');
            })
          ->where('id',$idqr)
          ->orWhere('qrcode',$idqr)
          ->select('st.*','p.code','p.name As prodName','atr.name as attributeName','pc.name AS catName')
          ->first();
}

function stockCatAllVariant($idqr){
    $st=DB::table('tbl_products_stock AS st')
    ->where('id',$idqr)
    ->orWhere('qrcode',$idqr)
    ->first();
    $allcat='';
    if($st)
    {
        $allcat=DB::table('tbl_products_stock AS st')
            ->join('tbl_products_master AS p',function($join)
            {
              $join->on('p.id', '=', 'st.product_id');
            })
            ->join('tbl_categories AS pc', function($join)
            {
              $join->on('pc.id', '=', 'st.category_id');
            })
            ->join('tbl_color AS atr', function($join)
            {
              $join->on('atr.id', '=', 'st.attribute_id');
            })
          ->where('st.product_id',$st->product_id)
          ->where('st.category_id',$st->category_id)
          ->where('st.status','1')
          ->select('st.*','p.code','p.name As prodName','atr.name as attributeName','pc.name AS catName')
          ->get();

    }
    return $$allcat;
}


//====Qrcode function =====
function createQrcodeForStock($stockId, $qrcode) {
    // Create a new record in the tbl_stock_qrcode table
    \App\Models\QrCode::create([
        'stock_id' => $stockId,
        'qrcode' => $qrcode,
    ]);
}

function getQrcodeByStockId($stockId) {
    // Retrieve a QR code by its stock_id
    $qrcode = \App\Models\QrCode::where('stock_id', $stockId)->first();

    if ($qrcode) {
        return $qrcode->qrcode;  // Return the QR code string
    }

    return null;  // Return null if no QR code is found
}

function generateQRCodesForStock()
{
    $products =\App\Models\StockModel::where('qr_generated',0)
    ->where('current_stock','>',0)->chunk(10,function($stock){
        foreach ($stock as $st) {

            // Generate a unique QR code for each stock item
            for($n=1;$n<=$st->current_stock;$n++){
                $qrcodeString = generateUniqueQRCode($st);

                // Store the unique QR code string in the database
                \App\Models\QrCode::create([
                    'stock_id' => $st->id,
                    'qrcode' => $qrcodeString,
                ]);
            }
            // Mark the product as having its QR code generated
            $st->update(['qr_generated' => 1]);
        }
    });


}

function base62_encode($number) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($characters);
    $result = '';

    while ($number > 0) {
        $remainder = $number % $base;
        $result = $characters[$remainder] . $result;
        $number = intdiv($number, $base);
    }

    return $result;
}

/**
 * Generate a unique QR code string based on product attributes and a unique stock ID.
 */
function generateUniqueQRCode($stock)
{
    // Combine the key fields into a single string. Include stock_id, product_id, category_id, attribute_id, and timestamp.

    $data = implode('|', [
        $stock->id,               // stock_id
        $stock->product_id,       // product_id
        $stock->category_id,      // category_id
        $stock->attribute_id,     // attribute_id
        time()                       // Add current timestamp for uniqueness
    ]);

    // Create a unique hash of the combined string using crc32 (or any other hash function)
    $hash = crc32($data);

    // Convert the hash to a Base62 string for a compact representation
    $encodedString = base62_encode($hash);

    // Ensure the generated QR code is unique in the database
    while (\App\Models\QrCode::where('qrcode', $encodedString)->exists()) {
        // If a collision occurs, re-hash with an additional random number and re-encode
        $hash = crc32($data . rand());
        $encodedString = base62_encode($hash);
    }

    return $encodedString;
}
