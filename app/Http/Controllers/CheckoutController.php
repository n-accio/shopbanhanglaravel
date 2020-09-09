<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Cart;
use App\City;
use App\Province;
use App\Wards;
use App\Feeship;
use Session;

use App\Slider;

use App\Shipping;
use App\Order;
use App\OrderDetails;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
session_start();

class CheckoutController extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    //

    public function login_checkout(Request $request){
        $slider = Slider::where('slider_status','1')->orderby('slider_id','DESC')->take(4)->get();
        // Seo
        
        $meta_desc = "Đăng nhập để thanh toán";
        $meta_keywords = "Đăng nhập để thanh toán";
        $url_canonical = $request->url();
        $meta_title = "Đăng nhập để thanh toán";
        // --- Seo
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.login_checkout')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
        ->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with(compact('slider'));
    }
    public function add_customer(Request $request){
        $data = array();
        $data['customer_name'] = $request->customer_name ;
        $data['customer_email'] = $request->customer_email ;
        $data['customer_password'] = md5($request->customer_password);
        $data['customer_phone'] = $request->customer_phone ;

        $customer_id = DB::table('tbl_customers')->insertGetId($data);
        
        Session::put('customer_id',$customer_id);
        Session::put('customer_name',$request->customer_name );
        return Redirect::to('/checkout');
    }
    public function checkout(Request $request){
        // Seo
        $meta_desc = "Điền thông tin thanh toán";
        $meta_keywords = "Điền thông tin thanh toán";
        $url_canonical = $request->url();
        $meta_title = "Điền thông tin thanh toán";
        // --- Seo

        $slider = Slider::where('slider_status','1')->orderby('slider_id','DESC')->take(3)->get();

        $city = City::orderby('matp','ASC')->get();

        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.show_checkout')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
        ->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with(compact('city','slider'));
    }
    public function save_checkout_customer(Request $request){
        $data = array();
        $data['shipping_email'] = $request->shipping_email ;
        $data['shipping_name'] = $request->shipping_name ;
        $data['shipping_address'] = $request->shipping_address ;
        $data['shipping_phone'] = $request->shipping_phone ;
        $data['shipping_notes'] = $request->shipping_notes ;

        $shipping_id = DB::table('tbl_shipping')->insertGetId($data);
        
        Session::put('shipping_id',$shipping_id);
        return Redirect::to('/payment');
    }
    public function payment(Request $request){
        // Seo
        $meta_desc = "Chọn hình thức thanh toán";
        $meta_keywords = "Chọn hình thức thanh toán";
        $url_canonical = $request->url();
        $meta_title = "Chọn hình thức thanh toán";
        // --- Seo
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.payment')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
        ->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title);
    }
    public function order_place(Request $request){
        // Seo
        $meta_desc = "Thanh toán";
        $meta_keywords = "Thanh toán";
        $url_canonical = $request->url();
        $meta_title = "Thanh toán";
        // --- Seo
        //insert payment method
        $data = array();
        $data['payment_method'] = $request->payment_option ;
        $data['payment_status'] = 'Đơn hàng đang chờ xử lý';
        $payment_id  = DB::table('tbl_payment')->insertGetId($data);
        
        //insert order
        $order_data = array();
        $order_data['customer_id'] = Session::get('customer_id') ;
        $order_data['shipping_id'] = Session::get('shipping_id') ;
        $order_data['payment_id'] = $payment_id ;
        $order_data['order_total'] = Cart::total();
        $order_data['order_status'] = 'Đơn hàng đang chờ xử lý';
        $order_id  = DB::table('tbl_order')->insertGetId($order_data);
        
        //insert order details
        $content = Cart::content();
        foreach ($content as $v_content){
            $order_d_data = array();
            $order_d_data['order_id'] = $order_id;
            $order_d_data['product_id'] = $v_content->id ;
            $order_d_data['product_name'] = $v_content->name ;
            $order_d_data['product_price'] = $v_content->price;
            $order_d_data['product_sales_quantity'] = $v_content->qty;
            DB::table('tbl_order_details')->insert($order_d_data);
        }
        if($data['payment_method']==1)
            echo 'Thanh toán bằng ATM';
        else {
            Cart::destroy();
            $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
            $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
            
            return view('pages.checkout.handcash')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
            ->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
            ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title);
        }
        
    }
    
    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }
    public function login_customer(Request $request){
   
        $email = $request->email_account ;
        $password = md5($request->password_account) ;
        $result = DB::table('tbl_customers')->where('customer_email',$email)->where('customer_password',$password)->first();
        if($result){
            Session::put('customer_id',$result->customer_id);
            return Redirect::to('/checkout');
        }else
        {
            return Redirect::to('/login-checkout');
        }
        
    }
    
    // public function manage_order(){
    //     $this->AuthLogin();
    //     $all_order = DB::table('tbl_order')
    //     ->join('tbl_customers','tbl_order.customer_id','=','tbl_customers.customer_id')
    //     ->select('tbl_order.*','tbl_customers.customer_name')
    //     ->orderby('tbl_order.order_id','desc')->get();
    //     $manage_order = view('admin.manage_order')->with('all_order',$all_order);
    //     return view('admin_layout')->with('admin.manage_order',$manage_order);
      
    // }
    // public function view_order($orderId){
    //     $this->AuthLogin();
    //     $order_by_id = DB::table('tbl_order')
    //     ->join('tbl_customers','tbl_order.customer_id','=','tbl_customers.customer_id')
    //     ->join('tbl_order_details','tbl_order.order_id','=','tbl_order_details.order_id')
    //     ->join('tbl_shipping','tbl_order.shipping_id','=','tbl_shipping.shipping_id')
    //     ->select('tbl_order.*','tbl_customers.*','tbl_order_details.*','tbl_shipping.*')
    //     ->where('tbl_order.order_id',$orderId)->get();
        
    //     $manage_order_by_id = view('admin.view_order')->with('order_by_id',$order_by_id);
    //     return view('admin_layout')->with('admin.view_order',$manage_order_by_id);
    // }

    // ----Hiển thị, tính và xóa phí vận chuyển-----
    public function select_delivery_home(Request $request){
        $data = $request->all();
        if($data['action']){
            $output = '';
            if($data['action']=="city"){
                $select_province = Province::where('matp',$data['ma_id'])->orderby('maqh','ASC')->get();
                    $output .= '<option>---Chọn quận huyện---</option>';
                foreach($select_province as $key => $province){
                    $output .='<option value="'.$province->maqh.'">'.$province->name_quanhuyen.'</option>';
                }
            }else{
                $select_wards = Wards::where('maqh',$data['ma_id'])->orderby('xaid','ASC')->get();
                    $output .= '<option>---Chọn xã phường---</option>';
                    foreach($select_wards as $key => $ward){
                        $output .= '<option value="'.$ward->xaid.'">'.$ward->name_xaphuong.'</option>';
                    }
            }
            echo $output;
        }
    }
    public function caculate_fee(Request $request){
        $data = $request->all();
        if($data['matp']){
            $feeship = Feeship::where('fee_matp', $data['matp'])->where('fee_maqh', $data['maqh'])->where('fee_xaid', $data['xaid'])->get();    
            if($feeship){
                $count_feeship = $feeship->count();
                if($count_feeship>0){
                    foreach($feeship as $key => $fee){
                        Session::put('fee',$fee->fee_feeship);
                        Session::save();
                    }
                }else{
                    Session::put('fee',30000);
                    Session::save();
                }
            }
        }
    }
    public function del_fee(Request $request){
        Session::forget('fee');
        return redirect()->back();
    }
    //----Kết thúc phần hiển thị, tính và xóa phí vận chuyển-----//

    //----Phần xác nhận đặt hàng-----//
    public function confirm_order(Request $request){
        $data = $request->all();
        $shipping = new Shipping();
        $shipping->shipping_name = $data['shipping_name'];
        $shipping->shipping_address = $data['shipping_address'];
        $shipping->shipping_phone = $data['shipping_phone'];
        $shipping->shipping_email = $data['shipping_email'];
        $shipping->shipping_notes = $data['shipping_notes'];
        $shipping->shipping_method = $data['shipping_method'];
        $shipping->save();

        $shipping_id = $shipping->shipping_id;
        $order = new Order();
        $order->customer_id = Session::get('customer_id');
        $order->shipping_id = $shipping_id;
        $order->order_status = 1;

        $checkout_code = substr(md5(microtime()),rand(0,26),5);

        $order->order_code = $checkout_code;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $order->created_at = now();
        $order->save();

        
        if(Session::get('cart')){
            foreach(Session::get('cart') as $key => $cart){
                $order_details = new OrderDetails();
                $order_details->order_code = $checkout_code;
                $order_details->product_id = $cart['product_id'];
                $order_details->product_name = $cart['product_name'];
                $order_details->product_price = $cart['product_price'];
                $order_details->product_sales_quantity = $cart['product_qty'];

                $order_details->product_coupon = $data['order_coupon'];
                $order_details->product_feeship = $data['order_fee'];

                $order_details->save();
            }
        }

        Session::forget('coupon');
        Session::forget('fee');
        Session::forget('cart');
    }
    //----Kết thúc phần xác nhận đặt hàng-----//
}
