<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
session_start();

class SendMailController extends Controller
{
    public function send_mail(){
        //send mail
        $to_name = "Shop Bán Hàng Laravel";
        $to_email = "truongthihongnhi1998@gmail.com";//send to this email

        $data = array("name"=>"Về vấn đề vận chuyển","body"=>"Mail về vấn đề giải quyết vấn đề nội dung vận chuyện trong vòng 7 ngày"); //body of mail.blade.php
    
        Mail::send('pages.send_mail',$data,function($message) use ($to_name,$to_email){
            $message->to($to_email)->subject('test mail');//send this mail with subject
            $message->from($to_email,$to_name);//send from this mail
        });
        //--send mail
        return Redirect::to('/');
    }
}
