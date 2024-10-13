<?php

namespace App\Mail\General;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class User_Password_Reset_Mail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }


    public function build()
    {
        return $this->view('mail.General.user_password_reset', [
            'user' => $this->user,
        ])->subject('Email with reset password link');
    }
}
