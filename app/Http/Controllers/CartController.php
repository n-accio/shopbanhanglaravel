<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Cart;
use App\Slider;
use App\Coupon;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
session_start();

class CartController extends Controller
{
    public function add_cart_ajax(Request $request){
        $data = $request->all();
        $session_id = substr(md5(microtime()),rand(0,26),5);
        $cart = Session::get('cart');
        if($cart == true){
            $is_avaiable = 0;
            foreach($cart as $key => $val){
                if($val['product_id']==$data['cart_product_id']){
                    $is_avaiable++;
                }
            }
            if($is_avaiable == 0){
                $cart[] = array(
                    'session_id' => $session_id,
                    'product_name' => $data['cart_product_name'],
                    'product_id' => $data['cart_product_id'],
                    'product_image' => $data['cart_product_image'],
                    'product_qty' => $data['cart_product_qty'],
                    'product_price' => $data['cart_product_price'],
                );
                Session::put('cart',$cart);               
            }
        }
        
        else{
            $cart[] = array(
                'session_id' => $session_id,
                'product_name' => $data['cart_product_name'],
                'product_id' => $data['cart_product_id'],
                'product_image' => $data['cart_product_image'],
                'product_qty' => $data['cart_product_qty'],
                'product_price' => $data['cart_product_price'],
            );
            Session::put('cart',$cart);
        }
        Session::save();
    }

    public function hien_thi_gio_hang(Request $request){

        // Seo
        $meta_desc = "Giỏ hàng của bạn";
        $meta_keywords = "Giỏ hàng Ajax";
        $url_canonical = $request->url();
        $meta_title = "Giỏ hàng Ajax";
        // --- Seo
        $slider = Slider::where('slider_status','1')->orderby('slider_id','DESC')->take(3)->get();
        
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        return view('pages.cart.cart_ajax')->with('cate_product',$cate_product)
        ->with('brand_product',$brand_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with(compact('slider'));
    }

    public function update_cart(Request $request){
        $data = $request->all();
        $cart = Session::get('cart');
        if($cart==true){
            foreach($data['cart_qty'] as $key => $qty){
                foreach($cart as $session=> $val){
                    if($val['session_id']== $key){
                        $cart[$session]['product_qty'] = $qty;
                    }
                }
            }
            Session::put('cart',$cart);
            return redirect()->back()->with('message','Cập nhật số lượng sản phẩm thành công');
        }else{
            return redirect()->back()->with('message','Cập nhật số lượng sản phẩm thất bại');
        }
    }

    public function del_product($session_id){
        $cart = Session::get('cart');
        if($cart==true){
            foreach($cart as $key => $val){
                if($val['session_id']==$session_id){
                    unset($cart[$key]);
                }
            }
            Session::put('cart',$cart);
            return redirect()->back()->with('message','Xóa sản phẩm thành công');
        }
        else{
            return redirect()->back()->with('message','Xóa sản phẩm thất bại');
        }
    }

    public function del_all_product(){
        $cart = Session::get('cart');
        if($cart==true){
            // Session::detroy(); //Xóa hết tất cả Session đang có
            Session::forget('cart');
            Session::forget('coupon');
            return redirect()->back()->with('message','Xóa tất cả sản phẩm thành công');
        }
        else{
            return redirect()->back()->with('message','Xóa tất cả sản phẩm thất bại');
        }
    }

    public function save_cart(Request $request){

        // $productId = $request->productid_hidden;
        // $quantity= $request->qty;
        // $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        // $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        // $product_info = DB::table('tbl_product')->where('product_id', $productId)->first();
     
        // $data['id'] = $product_info->product_id ;
        // $data['qty'] = $quantity;
        // $data['name'] = $product_info->product_name;
        // $data['price'] = $product_info->product_price;
        // $data['weight'] = '123';
        // $data['options']['image'] = $product_info->product_image;
        // Cart::add($data);
        
        // return Redirect::to('/show-cart');
        Cart::detroy();
    }
    public function show_cart(Request $request) {
        // Seo
        $meta_desc = "Giỏ hàng của bạn";
        $meta_keywords = "Giỏ hàng";
        $url_canonical = $request->url();
        $meta_title = "Giỏ hàng";
        // --- Seo
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        return view('pages.cart.show_cart')->with('cate_product',$cate_product)
        ->with('brand_product',$brand_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title);
    }
    public function delete_to_cart($rowId){
        Cart::update($rowId,0);
        return Redirect::to('/show-cart');
    }
    public function update_cart_quantity(Request $request){
        $rowId = $request->rowId_cart;
        $qty = $request->cart_quantity;
        Cart::update($rowId,$qty);
        return Redirect::to('/show-cart');
    }

    //Coupon
    public function check_coupon(Request $request){
        $data = $request->all();
        $coupon = Coupon::where('coupon_code',$data['coupon'])->first();
        if($coupon){
            // $count_coupon = $coupon->count();
            
            // echo($count_coupon);
           
            $coupon_session = Session::get('coupon');
            if($coupon_session == true){
                $is_avaiable = 0;
                if($is_avaiable==0){
                    $cou[] = array(
                        'coupon_code' => $coupon->coupon_code,
                        'coupon_condition' => $coupon->coupon_condition,
                        'coupon_number' => $coupon->coupon_number,
                    );
                    Session::put('coupon',$cou);
                }
            }else{
                $cou[] = array(
                    'coupon_code' => $coupon->coupon_code,
                    'coupon_condition' => $coupon->coupon_condition,
                    'coupon_number' => $coupon->coupon_number,
                );
                Session::put('coupon',$cou);
            }
            Session::save();
            return redirect()->back()->with('message','Thêm mã giảm giá thành công');
        }else{
            return redirect()->back()->with('error','Mã giảm giá không tồn tại');
        }
        
    }
    
}
