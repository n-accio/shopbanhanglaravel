@extends('admin_layout')
@section('admin_content')
<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
        Thông tin khách hàng đặt hàng
    </div>

    <div class="table-responsive">
      <?php
        $message = Session::get('message');
        if($message){
            echo '<span class="text-alert"> ',$message.' </span>';
            Session::put('message',null);
        }
      ?>
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>Tên khách hàng</th>
            <th>Email</th>
            <th>Số điện thoại</th>
          </tr>
        </thead>       
        <tbody>
        
            <tr>
                <td>{{$customer->customer_name}}</td>
                <td>{{$customer->customer_email}}</td>
                <td>{{$customer->customer_phone}}</td>
            </tr>
        </tbody>  
      </table>
    </div>
    
  </div>
</div>

<br>
<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
        Thông tin vận chuyển hàng hóa
    </div>

    <div class="table-responsive">
      <?php
        $message = Session::get('message');
        if($message){
            echo '<span class="text-alert"> ',$message.' </span>';
            Session::put('message',null);
        }
      ?>
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>Tên người nhận hàng</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Ghi chú đơn hàng</th>
            <th>Hình thức thanh toán</th>
          </tr>
        </thead>
        <tbody>
          
          <tr>
            <td>{{$shipping->shipping_name}}</td>
            <td>{{$shipping->shipping_address}}</td>
            <td>{{$shipping->shipping_phone}}</td>
            <td>{{$shipping->shipping_notes}}</td>
            <td>
              @if($shipping->shipping_method==0)
                Thanh toán qua ATM
              @else
                COD
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
  </div>
</div>
<br>

<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
        Thông tin chi tiết đơn hàng
    </div>
    
    <div class="table-responsive">
      <?php
        $message = Session::get('message');
        if($message){
            echo '<span class="text-alert"> ',$message.' </span>';
            Session::put('message',null);
        }
      ?>
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng sản phẩm tồn kho</th>
            <th>Mã giảm giá</th>
            <th>Số lượng</th>
            <th>Gía</th>
            <th>Thành tiền</th>
            
          </tr>
        </thead>
        @php
          $i=0;
          $total = 0;
        @endphp
        @foreach ($order_details_product as $key => $details)
        @php
          $i++;
          $subtotal = $details->product_sales_quantity*$details->product_price;
          $total += $subtotal;
        @endphp
        <tbody>      
          <tr class="color_qty_{{$details->product_id}}">
            <td>{{$i}}</td>
            <td>{{$details->product_name}}</td>
            <td>{{$details->product->product_quantity}}</td>
            <td>
              @if($details->product_coupon != 'no')
                {{$details->product_coupon}}
              @else
                Không có mã giảm giá
              @endif
            </td>
            <td>
                {{-- Số lượng sản phẩm khách đặt --}}
              <input type="number" min="1" {{$order_status ==2 ? 'disabled':''}} class="order_qty_{{$details->product_id}}" 
                value={{$details->product_sales_quantity}} name="product_sales_quantity">
                {{-- Số lượng sản phẩm kho có  --}}
              <input type="hidden" value={{$details->product->product_quantity}} 
                name="order_qty_storage" class="order_qty_storage_{{$details->product_id}}">
                {{-- Mã đơn hàng khách đặt --}}
              <input type="hidden" value={{$details->order_code}} name="order_code" class="order_code">
                {{-- Mã sản phẩm của đơn hàng--}}
              <input type="hidden" value={{$details->product_id}} name="order_product_id" class="order_product_id">
              
              @if($order_status != 2)
              <button class="btn btn-default update_quantity_order"
                data-product_id="{{$details->product_id}}" name="update_quantity_order">Cập nhật</button>
              @endif
            </td>
            <td>{{number_format($details->product_price,0,',','.')}} đ</td>
            <td>{{number_format($subtotal,0,',','.')}} đ</td>
          </tr>
        @endforeach
        
          <tr>
            <td>
              @php
                
                if($coupon_condition==1){
                  $total_after_coupon = ($total*$coupon_number)/100;
                  echo '<b>Số tiền được giảm: </b>' .number_format($total_after_coupon,0,',','.'). ' đ';
                  $total_coupon = $total - $total_after_coupon+ $details->product_feeship;
                }else{
                  $total_coupon =  $total -$coupon_number + $details->product_feeship;
                  echo '<b>Số tiền được giảm: </b>' .number_format($coupon_number,0,',','.'). ' đ';
                } 
              @endphp
            </td>
            <td >
              <b>Phí ship:</b> {{number_format($details->product_feeship,0,',','.')}} đ
            </td>
            <td >
              <b>Tổng cộng:</b> {{number_format($total_coupon,0,',','.')}} đ
            </td>
          </tr>
          <tr>
            <td colspan="7">
              @foreach ($order as $key =>$ord )
                  @if ($ord->order_status==1)
                    <form>
                      @csrf
                      <select class="form-control update_status_order_details">
                        <option value="" >---Chọn trạng thái đơn hàng---</option>
                        <option id="{{$ord->order_id}}" selected value="1" >Đơn hàng chưa được xử lý</option>
                        <option id="{{$ord->order_id}}" value="2">Đã xử lý - Đã giao hàng</option>
                        <option id="{{$ord->order_id}}" value="3">Hủy đơn</option>
                      </select>
                    </form>
                  @elseif($ord->order_status== 2)
                    <form>
                      @csrf
                      <select class="form-control update_status_order_details">
                        <option value="" >---Chọn trạng thái đơn hàng---</option>
                        <option id="{{$ord->order_id}}" value="1" >Đơn hàng chưa được xử lý</option>
                        <option id="{{$ord->order_id}}" selected value="2">Đã xử lý - Đã giao hàng</option>
                        <option id="{{$ord->order_id}}" value="3">Hủy đơn</option>
                      </select>
                    </form>
                  @else
                    <form>
                      @csrf
                      <select class="form-control update_status_order_details">
                        <option value="" >---Chọn trạng thái đơn hàng---</option>
                        <option id="{{$ord->order_id}}" value="1" >Đơn hàng chưa được xử lý</option>
                        <option id="{{$ord->order_id}}" value="2">Đã xử lý - Đã giao hàng</option>
                        <option id="{{$ord->order_id}}" selected value="3">Hủy đơn</option>
                      </select>
                    </form>
                  @endif
              @endforeach
            <td>
          </tr>
        </tbody>
      </table>

      <a target="_blank" href="{{url('/print-order/'.$details->order_code)}}">In đơn hàng</a>
    </div>
  </div>
</div>
@endsection