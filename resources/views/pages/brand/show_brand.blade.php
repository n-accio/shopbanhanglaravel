@extends('welcome')
@section('content')
 
<div class="features_items"><!--features_items-->
    @foreach ($brand_name as $key => $name )
        <h2 class="title text-center">{{$name->brand_name}}</h2>
    @endforeach
    @foreach ($brand_by_id as $key => $brand)
    {{-- <a href="{{URL::to('chi-tiet-san-pham/'.$brand->product_slug)}}">
    <div class="col-sm-4">
        <div class="product-image-wrapper">
            <div class="single-products">
                    <div class="productinfo text-center">
                        <img src="{{URL::to('public/uploads/product/'.$brand->product_image)}}" alt="" />
                        <h2>{{number_format($brand->product_price).' '.'VND'}}</h2>
                        <p>{{$brand->product_name}}</p>
                        <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm giỏ hàng</a>
                    </div>
                    
            </div>
            <div class="choose">
                <ul class="nav nav-pills nav-justified">
                    <li><a href="#"><i class="fa fa-plus-square"></i>Yêu thích</a></li>
                    <li><a href="#"><i class="fa fa-plus-square"></i>So sánh</a></li>
                </ul>
            </div>
        </div>
    </div>
    </a>   --}}
    <div class="col-sm-4">
        <div class="product-image-wrapper">
            <div class="single-products">
                    <div class="productinfo text-center">
                    <form>
                        {{csrf_field()}}
                        <input type="hidden" value="{{$brand->product_id}}" class="cart_product_id_{{$brand->product_id}}">
                        <input type="hidden" value="{{$brand->product_name}}" class="cart_product_name_{{$brand->product_id}}">
                        <input type="hidden" value="{{$brand->product_image}}" class="cart_product_image_{{$brand->product_id}}">
                        <input type="hidden" value="{{$brand->product_price}}" class="cart_product_price_{{$brand->product_id}}">
                        <input type="hidden" value="1" class="cart_product_qty_{{$brand->product_id}}">

                        <a href="{{URL::to('chi-tiet-san-pham/'.$brand->product_slug)}}">
                            <img src="{{URL::to('public/uploads/product/'.$brand->product_image)}}" alt="" />
                            <h2>{{number_format($brand->product_price).' '.'VNĐ'}}</h2>
                            <p>{{$brand->product_name}}</p>
   
                        </a>
                        <button type="button" class="btn btn-default add-to-cart" data-id_product="{{$brand->product_id}}" name="add-to-cart">Thêm giỏ hàng</button>
                    </form>

                    </div>
                 
            </div>
            <div class="choose">
                <ul class="nav nav-pills nav-justified">
                    <li><a href="#"><i class="fa fa-plus-square"></i>Yêu thích</a></li>
                    <li><a href="#"><i class="fa fa-plus-square"></i>So sánh</a></li>
                </ul>
            </div>
        </div>
    </div>      
    @endforeach
</div><!--features_items-->

@endsection