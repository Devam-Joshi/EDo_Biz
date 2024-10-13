<?php

namespace App\Http\Requests\Admin\General;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'opassword' => ['required'],
            'npassword' => ['required'],
            'cpassword' => ['required', 'same:npassword'],
        ];
    }

    public function messages()
    {
        return [
            'opassword.exists' => __('admin.change_password_not_match'),
            'cpassword.same' => __('admin.change_password_not_same'),
        ];
    }

    public function update_pass()
    {
        $user_data = $this->user();
        if (Hash::check($this->opassword, $user_data->getAuthPassword())) {
            $is_update = $user_data->update(['password' => $this->npassword]);
            if ($is_update) {
                success_session(__('admin.chang_password_updated'));
            } else {
                error_session(__('admin.chang_fail_to_update'));
            }
        } else {
            error_session(__('admin.change_password_not_match'));
        }
    }


}
