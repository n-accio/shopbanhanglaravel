<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Brand;
use App\Slider;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
session_start();

class BrandProduct extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    public function add_brand_product(){
        $this->AuthLogin();
        return view('admin.add_brand_product');
    }
    
    public function all_brand_product(){
        $this->AuthLogin();
        // ----------Cách 1---------
        // $all_brand_product = DB::table('tbl_brand')->get();
        // -------- Cách 2 ------------
        // $all_brand_product = Brand::all();
        // -------- Cách 3 sử dụng get khi có thêm câu lệnh truy vấn như where, orderBy... ------------
        $all_brand_product = Brand::orderBy('brand_id','DESC')->get();
        //or sử dụng paginate để hiển thị số thương hiệu trên 1 trang
        // $all_brand_product = Brand::orderBy('brand_id','DESC')->paginate(3);
        $manager_brand_product = view('admin.all_brand_product')->with('all_brand_product',$all_brand_product);
        return view('admin_layout')->with('admin.all_brand_product',$manager_brand_product);
    }
    public function save_brand_product(Request $request){
        
        $this->AuthLogin();
        // ----------Cách 1---------
        // $data = array();
        // $data['brand_name'] = $request->brand_product_name;
        // $data['brand_desc'] = $request->brand_product_desc;
        // $data['brand_status'] = $request->brand_product_status;
        // DB::table('tbl_brand')->insert($data);

        // -------- Cách 2 ------------
        $data = $request->all();
        
        $brand = new Brand();
        $brand->brand_name = $data['brand_product_name'];
        $brand->brand_slug = $data['brand_slug'];
        $brand->brand_desc = $data['brand_product_desc'];
        $brand->brand_status = $data['brand_product_status'];
        $brand->save();
        
        Session::put('message','Thêm thương hiệu sản phẩm thành công');
        return Redirect::to('add-brand-product');        
    }
    public function unactive_brand_product($brand_product_id){
        DB::table('tbl_brand')->where('brand_id',$brand_product_id)->update(['brand_status' => 1]);
        Session::put('message','Kích hoạt thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product'); 
    }
    public function active_brand_product($brand_product_id){
        DB::table('tbl_brand')->where('brand_id',$brand_product_id)->update(['brand_status' => 0]);
        Session::put('message','Ẩn thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product'); 
    }
    public function edit_brand_product($brand_product_id){
        $this->AuthLogin();
        // ----------Cách 1-----------
        //$edit_brand_product = DB::table('tbl_brand')->where('brand_id',$brand_product_id)->get();
        // ----------Cách 2-----------
        $edit_brand_product = Brand::where('brand_id',$brand_product_id)->get();

        $manager_brand_product = view('admin.edit_brand_product')->with('edit_brand_product',$edit_brand_product);
        return view('admin_layout')->with('admin.edit_brand_product',$manager_brand_product);
    }
    public function update_brand_product(Request $request,$brand_product_id){
        $this->AuthLogin();
        // ----------Cách 1-----------
        // $data = array();
        // $data['brand_name'] = $request->brand_product_name;
        // $data['brand_desc'] = $request->brand_product_desc;
        //DB::table('tbl_brand')->where('brand_id',$brand_product_id)->update($data);

        // -------- Cách 2 ------------
        $data = $request->all();
        $brand = Brand::find($brand_product_id);
        $brand->brand_name = $data['brand_product_name'];
        $brand->brand_slug = $data['brand_slug'];
        $brand->brand_desc = $data['brand_product_desc'];
        $brand->save();
        
        
        Session::put('message','Cập nhật thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product');        
    }
    public function delete_brand_product($brand_product_id){
        $this->AuthLogin();
        DB::table('tbl_brand')->where('brand_id',$brand_product_id)->delete();
        Session::put('message','Xóa thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product');        
    }

    //End function Admin page

    public function show_brand_home(Request $request,$brand_slug){
        //slide
        
        $slider = Slider::orderBy('slider_id','DESC')->where('slider_status','1')->take(3)->get();

        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        $brand_by_id = DB::table('tbl_product')
        ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
        ->where('tbl_brand.brand_slug',$brand_slug)->get();
        $brand_name =  DB::table('tbl_brand')->where('tbl_brand.brand_slug',$brand_slug)->limit(1)->get();
        foreach ($brand_name as $key => $value) {
            // Seo
            $meta_desc = $value->brand_desc;
            $meta_keywords = $value->brand_slug;
            $url_canonical = $request->url();
            $meta_title = $value->brand_name;
            // --- Seo
        }

        

        return view('pages.brand.show_brand')->with('cate_product',$cate_product)
        ->with('brand_product',$brand_product)->with('brand_by_id',$brand_by_id)->with('brand_name',$brand_name)
        ->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)
        ->with('url_canonical',$url_canonical)->with('meta_title',$meta_title)->with('slider',$slider);
    }
}
