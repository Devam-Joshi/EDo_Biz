@extends('layouts.mail.app')
@section('content')
    <tr>
        <td style="padding: 50px 45px 25px 45px; font-family: arial; font-size: 15px; color: #333; line-height: normal;"
            width="550">Dear <strong>{{$user->name}},</strong></td>
    </tr>
    <tr>
        <td style="padding: 0 45px 5px; font-family: arial; font-size: 14px; color: #333; line-height: normal;"
            width="550">You Recently Request to Reset Your Password at {{site_name}} Account.
        </td>
    </tr>
    <tr>
        <td style="padding: 0 45px;" width="550"><a href="{{route('front.forgot_password_view',$user->reset_token)}}"
                                                    style="font-size: 14px; font-family: arial; color: #ff5673; font-weight: bold; text-decoration: none; line-height: normal;">Click
                Here to Reset Your Password</a></td>
    </tr>
@endsection
