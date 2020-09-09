<?php

namespace App\Http\Controllers;
use App;
use Illuminate\Http\Request;


use App\Feeship;
use App\Shipping;
use App\Order;
use App\Product;
use App\OrderDetails;
use App\Customer;
use App\Coupon;
use PDF;

class OrderController extends Controller
{
    //Hiển thị đơn đặt hàng
    public function manage_order(){
        $order = Order::orderby('created_at','DESC')->get();
        return view('admin.order.manage_order')->with(compact('order'));
    }
    public function view_order($order_code){
        $order = Order::where('order_code',$order_code)->get();
        $order_details = OrderDetails::where('order_code',$order_code)->get();
        foreach($order as $key => $ord){
            $customer_id = $ord->customer_id;
            $shipping_id = $ord->shipping_id;
            $order_status = $ord->order_status;
        }
        $customer = Customer::where('customer_id',$customer_id)->first();
        $shipping = Shipping::where('shipping_id',$shipping_id)->first();

        $order_details_product = OrderDetails::with('product')->where('order_code',$order_code)->get();
        foreach($order_details_product as $key=> $order_d){
            $product_coupon = $order_d->product_coupon;
        }
        if($product_coupon !='no'){
            $coupon = Coupon::where('coupon_code',$product_coupon)->first();

            $coupon_condition = $coupon->coupon_condition;
            $coupon_number = $coupon->coupon_number;
        }else{
            $coupon_condition = 2;
            $coupon_number = 0;
        }
        
        return view('admin.order.view_order')->with(compact('order','order_details',
            'order_details_product','customer','shipping','coupon_condition','coupon_number','order_status'));
    }
    //--------Kết thúc phần hiển thị đơn đặt hàng--------//

    // In hóa đơn
    public function print_order($checkout_code){
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($this->print_order_convert($checkout_code));
        return $pdf->stream();
    }

    public function print_order_convert($checkout_code){
        $order = Order::where('order_code',$checkout_code)->get();
        $order_details = OrderDetails::where('order_code',$checkout_code)->get();
        foreach($order as $key => $ord){
            $customer_id = $ord->customer_id;
            $shipping_id = $ord->shipping_id;
        }
        $customer = Customer::where('customer_id',$customer_id)->first();
        $shipping = Shipping::where('shipping_id',$shipping_id)->first();

        $order_details_product = OrderDetails::with('product')->where('order_code',$checkout_code)->get();
        foreach($order_details_product as $key=> $order_d){
            $product_coupon = $order_d->product_coupon;
        }
        if($product_coupon !='no'){
            $coupon = Coupon::where('coupon_code',$product_coupon)->first();

            $coupon_condition = $coupon->coupon_condition;
            $coupon_number = $coupon->coupon_number;
        }else{
            $coupon_condition = 2;
            $coupon_number = 0;
        }

        $output ='';
        $output .= '<style>
            body{ font-family: DejaVu Sans;}
            .table-styling {
                background:#ffffff;
                width:100%;
                font-family: DejaVu Sans, Roboto,Arial, Helvetica, sans-serif;
                font-size:13px;
               
                border:1px solid #ccc;
            }
            .table-styling th {
                text-align: center;
                font-weight: bold;
                height: 24px;
                border: 1px solid #ddd;
            }
            .table-styling tr td {
                padding: 8px;
                height: 24px;
                border: 1px solid #ccc;
            }

            .sign-left {
                text-align:center;
                font-weight: bold;
                padding-top:10px;
                position:relative;
              
                width:30%;
                color:#000;
                float:left;
                font-size: 12px;
            }
            .sign-middle {
                text-align:center;
                font-weight: bold;
                padding-top:10px;
                position:relative;
                width:40%;
                color:#000;
                float:left;
                font-size: 12px;
             
            }
            .sign-right {
                text-align:center;
                font-weight: bold;
                padding-top:10px;
                position:relative;
                width:30%;
                color:#000;
                font-size: 12px;
                float:right;
            }
           
            </style>
            <h3><center>
                    HÓA ĐƠN THANH TOÁN
                    <br/>
                    -------oOo-------
                </center>
            </h3>
            <h4><center>Độc lập - Tự do - Hạnh phúc</center></h4>
            <br/>
            <br/>
            <p>Người đặt hàng:</p>
            <table class="table-styling">
                <thead>
                    <tr>
                        <th>Tên khách đặt</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        
                    </tr>
                </thead>
                <tbody>';
             
            $output .='
                    <tr>
                        <td>'.$customer->customer_name.'</td>
                        <td>'.$customer->customer_phone.'</td>
                        <td>'.$customer->customer_email.'</td>
                    </tr>
            ';
            $output .='
                </tbody>
            </table> ';

            $output .='<p>Thông tin người nhận hàng:</p>
            <table class="table-styling">
                <thead>
                    <tr>
                        <th>Họ tên người nhận</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Email</th>
                        <th>Ghi chú</th> 
                    </tr>
                </thead>
                <tbody>';
             
            $output .='
                    <tr>
                        <td>'.$shipping->shipping_name.'</td>
                        <td>'.$shipping->shipping_phone.'</td>
                        <td>'.$shipping->shipping_address.'</td>
                        <td>'.$shipping->shipping_email.'</td>
                        <td>'.$shipping->shipping_notes.'</td>
                    </tr>
            ';
            $output .='
                </tbody>
            </table> ';  

            $output .='<p>Thông tin đơn hàng:</p>
            <table class="table-styling">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Mã giảm giá</th>
                        <th>Số lượng</th>
                        <th>Gía sản phẩm</th>
                        <th>Thành tiền</th> 
                    </tr>
                    
                </thead>
                <tbody>';
                $total = 0;
                foreach($order_details_product as $key => $product){
                $subtotal = $product->product_sales_quantity*$product->product_price;
                $total += $subtotal;

                

                if($product->product_coupon != 'no'){
                    $product_coupon = $product->product_coupon;
                }else{
                    $product_coupon = 'Không có mã giảm giá';
                }
                
            $output .='
                    <tr>
                        <td>'.$product->product_name.'</td>
                        <td>'.$product_coupon.'</td>
                        <td>'.$product->product_sales_quantity.'</td>
                        <td>'.number_format($product->product_price,0,',','.').' đ</td>
                        <td>'.number_format($subtotal,0,',','.').' đ</td>
                    </tr>
                    ';
            }
                if($coupon_condition==1){
                    $total_after_coupon = ($total*$coupon_number)/100;
                    $total_coupon = $total - $total_after_coupon+ $product->product_feeship;
                }else{
                    $total_after_coupon= $coupon_number;
                    $total_coupon =  $total -$total_after_coupon + $product->product_feeship;
                }
            $output .='
                    <tr>
                        <td colspan="5"><b>Số tiền được giảm: </b> '.number_format($total_after_coupon,0,',','.').' đ  </td> 
                    </tr>
                    <tr>
                        <td colspan="5"><b>Phí ship: </b>'.number_format($product->product_feeship,0,',','.').' đ  </td> 
                    </tr>
                    <tr>
                        <td colspan="5"><b>Tổng cộng: </b> '.number_format($total_coupon,0,',','.').' đ </td> 
                    </tr>
                </tbody>
            </table> ';

            $output .='<p>Kí tên:</p>
                <div class="sign-left">Người lập phiếu</div>
                <div class="sign-middle">Người giao hàng</div>
                <div class="sign-right">Người nhận hàng</div>
                ';  
        return $output;
    }

    public function update_order_status(Request $request){
        $data = $request->all();

        $order = Order::find($data['order_id']);
        $order->order_status = $data['order_status'];
        $order->save();
        if($order->order_status == 2){
            foreach($data['order_product_id'] as $key => $product_id){
                $product = Product::find($product_id);
                $product_quantity = $product->product_quantity;
                $product_sold = $product->product_sold;

                foreach($data['quantity'] as $key2 => $qty){
                    if($key == $key2){
                        $product_remain = $product_quantity - $qty;
                        $product->product_quantity = $product_remain;
                        $product->product_sold = $product_sold + $qty;
                        $product->save();
                    }
                }
            }
        }elseif($order->order_status != 2 && $order->order_status != 3){
            foreach($data['order_product_id'] as $key => $product_id){
                $product = Product::find($product_id);
                $product_quantity = $product->product_quantity;
                $product_sold = $product->product_sold;

                foreach($data['quantity'] as $key2 => $qty){
                    if($key == $key2){
                        $product_remain = $product_quantity + $qty;
                        $product->product_quantity = $product_remain;
                        $product->product_sold = $product_sold - $qty;
                        $product->save();
                    }
                }
            }
        }
    }

    public function update_order_qty(Request $request){
        $data = $request->all();
        $order_details = OrderDetails::where('product_id',$data['order_product_id'])
            ->where('order_code',$data['order_code'])->first();
        $order_details->product_sales_quantity = $data['order_qty'];
        $order_details->save();
    }
}
