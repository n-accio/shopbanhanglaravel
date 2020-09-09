<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Front end
Route::get('/', 'HomeController@index');

Route::get('/trang-chu', 'HomeController@index');
Route::post('/tim-kiem', 'HomeController@search');

//Danh muc san pham trang chu
Route::get('/danh-muc-san-pham/{slug_category_product}', 'CategoryProduct@show_category_home');
Route::get('/thuong-hieu-san-pham/{brand_slug}', 'BrandProduct@show_brand_home');
Route::get('/chi-tiet-san-pham/{product_slug}', 'ProductController@details_product');

// Admin
Route::get('/admin', 'AdminController@index');

Route::get('/dashboard', 'AdminController@show_dashboard');

Route::get('/logout', 'AdminController@logout');

Route::post('/admin-dashboard', 'AdminController@dashboard');

//Category Product
Route::get('/add-category-product', 'CategoryProduct@add_category_product');
Route::get('/all-category-product', 'CategoryProduct@all_category_product');
Route::get('/edit-category-product/{category_product_id}', 'CategoryProduct@edit_category_product');
Route::get('/delete-category-product/{category_product_id}', 'CategoryProduct@delete_category_product');

Route::get('/unactive-category-product/{category_product_id}', 'CategoryProduct@unactive_category_product');
Route::get('/active-category-product/{category_product_id}', 'CategoryProduct@active_category_product');

Route::post('/save-category-product', 'CategoryProduct@save_category_product');
Route::post('/update-category-product/{category_product_id}', 'CategoryProduct@update_category_product');

//Brand Product
Route::get('/add-brand-product', 'BrandProduct@add_brand_product');
Route::get('/all-brand-product', 'BrandProduct@all_brand_product');
Route::get('/edit-brand-product/{brand_product_id}', 'BrandProduct@edit_brand_product');
Route::get('/delete-brand-product/{brand_product_id}', 'BrandProduct@delete_brand_product');

Route::get('/unactive-brand-product/{brand_product_id}', 'BrandProduct@unactive_brand_product');
Route::get('/active-brand-product/{brand_product_id}', 'BrandProduct@active_brand_product');

Route::post('/save-brand-product', 'BrandProduct@save_brand_product');
Route::post('/update-brand-product/{brand_product_id}', 'BrandProduct@update_brand_product');

//Product
Route::get('/add-product', 'ProductController@add_product');
Route::get('/all-product', 'ProductController@all_product');
Route::get('/edit-product/{product_id}', 'ProductController@edit_product');
Route::get('/delete-product/{product_id}', 'ProductController@delete_product');

Route::get('/unactive-product/{product_id}', 'ProductController@unactive_product');
Route::get('/active-product/{product_id}', 'ProductController@active_product');

Route::post('/save-product', 'ProductController@save_product');
Route::post('/update-product/{product_id}', 'ProductController@update_product');

//Cart
Route::post('/save-cart', 'CartController@save_cart');
Route::post('/update-cart-quantity', 'CartController@update_cart_quantity');
Route::get('/show-cart', 'CartController@show_cart');
Route::get('/delete-to-cart/{rowId}', 'CartController@delete_to_cart');

Route::post('/add-cart-ajax', 'CartController@add_cart_ajax');
Route::get('/gio-hang', 'CartController@hien_thi_gio_hang');
Route::post('/update-cart', 'CartController@update_cart');
Route::get('/del-product/{session_id}', 'CartController@del_product');
Route::get('/del-all-product', 'CartController@del_all_product');

//Coupon
Route::post('/check-coupon', 'CartController@check_coupon');

Route::get('/unset-coupon', 'CouponController@unset_coupon');
Route::get('/insert-coupon', 'CouponController@insert_coupon');
Route::post('/insert-coupon-code', 'CouponController@insert_coupon_code');

Route::get('/list-coupon', 'CouponController@list_coupon');
Route::get('/delete-coupon/{coupon_id}', 'CouponController@delete_coupon');

//Delivery
Route::get('/delivery', 'DeliveryController@delivery');
Route::post('/select-delivery', 'DeliveryController@select_delivery');
Route::post('/insert-delivery', 'DeliveryController@insert_delivery');
Route::post('/select-feeship', 'DeliveryController@select_feeship');
Route::post('/update-feeship-delivery', 'DeliveryController@update_feeship_delivery');


// Checkout
Route::get('/login-checkout', 'CheckoutController@login_checkout');
Route::get('/logout-checkout', 'CheckoutController@logout_checkout');

Route::post('/add-customer', 'CheckoutController@add_customer');
Route::post('/login-customer', 'CheckoutController@login_customer');
Route::get('/checkout', 'CheckoutController@checkout');
Route::post('/save-checkout-customer', 'CheckoutController@save_checkout_customer');
Route::get('/payment', 'CheckoutController@payment');
Route::post('/order-place', 'CheckoutController@order_place');

Route::post('/select-delivery-home', 'CheckoutController@select_delivery_home');
Route::post('/caculate-fee', 'CheckoutController@caculate_fee');
Route::get('/del-fee', 'CheckoutController@del_fee');

Route::post('/confirm-order', 'CheckoutController@confirm_order');



// Manage Order
Route::get('/manage-order', 'OrderController@manage_order');
Route::get('/view-order/{order_code}', 'OrderController@view_order');
Route::get('/print-order/{checkout_code}', 'OrderController@print_order');

Route::post('/update-order-status', 'OrderController@update_order_status');
Route::post('/update-order-qty', 'OrderController@update_order_qty');


// Route::get('/manage-order', 'CheckoutController@manage_order');
// Route::get('/view-order/{orderId}', 'CheckoutController@view_order');
// Route::get('/delete-order/{orderId}', 'CheckoutController@delete_order');

//Send Mail
Route::get('/send-mail', 'SendMailController@send_mail');

//Login facebook
Route::get('/login-facebook','AdminController@login_facebook');
Route::get('/admin/callback','AdminController@callback_facebook');

// Manage Banner
Route::get('/manage-slider', 'SliderController@manage_slider');
Route::get('/add-slider', 'SliderController@add_slider');
Route::post('/insert-slider', 'SliderController@insert_slider');
Route::get('/unactive-slide/{slider_id}', 'SliderController@unactive_slide');
Route::get('/active-slide/{slider_id}', 'SliderController@active_slide');


