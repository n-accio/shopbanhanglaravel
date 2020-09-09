<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slider;
use DB;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class SliderController extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    public function manage_slider(){
        $all_slide = Slider::orderBy('slider_id','DESC')->get();
        return view('admin.slider.list_slider')->with(compact('all_slide'));
    }
    public function add_slider(){
        $this->AuthLogin();
        return view('admin.slider.add_slider');
    }
    public function insert_slider(Request $request){
        $data = $request->all();

        $get_image = $request->file('slider_image');
        if($get_image){
            $get_name_image = $get_image->getClientOriginalName();
            $name_image = current(explode('.',$get_name_image));
            $new_image = $name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();
            $get_image->move('public/uploads/slider',$new_image);
            
            $slide = new Slider();
            $slide->slider_name = $data['slider_name'];
            $slide->slider_image = $new_image;
            $slide->slider_status = $data['slider_status'];
            $slide->slider_desc = $data['slider_desc'];
            $slide->save();
            Session::put('message','Thêm slide thành công');
            return Redirect::to('add-slider');  
        }
        else {
            Session::put('message','Vui lòng chọn hình ảnh');
            return Redirect::to('add-slider');   
        }
    }
    public function unactive_slide($slider_id){
        $slide = Slider::where('slider_id',$slider_id)->first();
        $slide->slider_status = 1;
        $slide->save();
       
        Session::put('message','Kích hoạt slide thành công');
        return Redirect::to('manage-slider'); 
    }
    public function active_slide($slider_id){
        DB::table('tbl_slider')->where('slider_id',$slider_id)->update(['slider_status' => 0]);
        Session::put('message','Ẩn thương slide thành công');
        return Redirect::to('manage-slider');   
    }
}
