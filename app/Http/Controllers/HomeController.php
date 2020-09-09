<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use App\Slider;
use Illuminate\Support\Facades\Redirect;
session_start();

class HomeController extends Controller
{
    public function index(Request $request){
        $meta_desc = "Chuyên phụ kiện điện thoại các dòng - Đồ chơi di động - Tai nghe bluetooth - Smart Watch. Loại: Cáp Sạc, Ốp Lưng, Tai Nghe, Bao Da.";
        $meta_keywords = "phụ kiện điện thoại, điện thoại, tai nghe";
        $url_canonical = $request->url();
        $meta_title = "Thiết bị di động, phụ kiện điện thoại";

        $slider = Slider::where('slider_status','1')->orderby('slider_id','DESC')->take(3)->get();

        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        $all_product = DB::table('tbl_product')->where('product_status','1')->orderby('product_id','desc')->limit(3)->get();

        return view('pages.home')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
        ->with('all_product',$all_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with(compact('slider'));
        // dùng with compact nhanh hơn with
        // with(compact('meta_desc','meta_keywords','url_canonical','meta_title'));
    }

    public function search(Request $request){
        $slider = Slider::where('slider_status','1')->orderby('slider_id','DESC')->take(4)->get();
        // Seo
        $meta_desc = "Tìm kiếm sản phẩm";
        $meta_keywords = "Tìm kiếm sản phẩm";
        $url_canonical = $request->url();
        $meta_title = "Tìm kiếm sản phẩm";
        // --- Seo
        $keywords = $request->keywords_submit;
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        $search_product = DB::table('tbl_product')->where('product_status','1')->where('product_name','like','%'.$keywords.'%')->get();

        return view('pages.sanpham.search')->with('cate_product',$cate_product)->with('brand_product',$brand_product)
        ->with('search_product',$search_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with(compact('slider'));
    }
}
