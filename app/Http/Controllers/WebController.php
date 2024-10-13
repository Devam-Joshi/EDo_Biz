<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{

    function generate_switch($params = array())
    {
        return "<label for='primary".$params['id']."' class='switch switch--primary'>
        <input class='switch__input switch-status' type='checkbox' data-id='" . $params['id'] . "'  data-url='" . $params['url']['status'] ."'   " . $params['checked'] . "  id='primary".$params['id']."' />
        <div class='switch__box'>
            <span class='switch__box-inner'></span>
        </div>
    </label>";
    }

    function generate_custom_switch($id = 0, $url = "", $checked = "false")
    {
        return '<span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success"><label>
                                                            <input type="checkbox" data-id="' . $id . '"  data-url="' . $url . '" ' . $checked . ' class="switch-status">
                                                                <span></span></label></span>';
    }

    function generate_actions_buttons($params = array(), $extra = array(), $target = false)
    {
        $operation = '';
        $isEdit = isset($params['url']['edit']);
        $isView = isset($params['url']['view']);
        $isDelete = isset($params['url']['delete']);
        $target = ($target) ? 'target="_blank"' : "";
        if ($isView) {
            $operation .= '<a title="View" ' . $target . ' href="' . $params['url']['view'] . '" data-type="view" data-id="' . $params['id'] . '" class="btn btn-sm btn-clean btn-icon btn-icon-md btnView"> <i class="fas fa-eye"></i> </a>';
        }
        if ($isEdit) {
            $operation .= '<a ' . $target . ' title="Edit" href="' . $params['url']['edit'] . '" data-id="' . $params['id'] . '" class="btn btn-sm btn-clean btn-icon btn-icon-md btnEdit"><i class="fas fa-edit"></i></a>';
        }
        if ($isDelete) {
            $operation .= '<a ' . $target . ' title="Delete" href="' . $params['url']['delete'] . '" data-id="' . $params['id'] . '" class="btn btn-sm btn-clean btn-icon btn-icon-md btnDelete">
<i class="fas fa-trash"></i>
</a>';
        }
        if (!empty($extra)) {
            foreach ($extra as $key => $value) {
                $btn_id = "";
                $onclick = "";
                $data_type = "";
                $title = "";
                if (isset($value['title']) && !empty($value['title'])) {
                    $title = $value['title'];
                }
                if (!empty($value['btn_id'])) {
                    $btn_id = "id='" . $value['btn_id'] . "'";
                }
                if (!empty($value['onclick'])) {
                    $onclick = "onclick='" . $value['onclick'] . "'";
                }
                if (array_key_exists('data_type', $value) && !empty($value['data_type'])) {
                    $data_type = "data-type='" . $value['data_type'] . "'";
                }
                if(empty($value['btn_type'])) {
                    if (empty($value['btn_modal']))
                        $operation .= '<button  title="' . $title . '" data-url="' . $value['url'] . '" ' . $data_type . ' data-id="' . $value['data_id'] . '" ' . $btn_id . ' ' . $onclick . ' class="btn_space ' . $value['btn_class'] . ' btn-xs">' . $value['btn_name'] . '</button>';
                    else
                        $operation .= '<button type="button" class="btn_space ' . $value['btn_class'] . ' btn-xs" data-toggle="modal" ' . $btn_id . ' ' . $onclick . ' data-target="#' . $value['modal_id'] . '">' . $value['btn_name'] . '</button>';
                }else{
                    $operation .= '<a  title="' . $title . '" href="' .$value['url'] . '" data-id="' . $value['data_id'] . '" class="btn btn-sm btn-clean btn-icon btn-icon-md">
               '.$value['btn_icon'].'
            </a>';
                }
                //with icon button

            }
        }
        return $operation;
    }

    public function upload_file($file_name = "", $path = "")
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


}
