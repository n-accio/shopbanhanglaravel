@extends('admin_layout')
@section('admin_content')
<div class="row">
    <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thêm Slider
                </header>
                <?php
                $message = Session::get('message');
                if($message){
                    echo '<span class="text-alert"> ',$message.' </span>';
                    Session::put('message',null);
                }
                ?>
                <div class="panel-body">
                    <div class="position-center">
                        <form role="form" action="{{URL::to('/insert-slider')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1">Tên slider</label>
                            <input type="text" class="form-control" name="slider_name" id="exampleInputEmail1" placeholder="Nhập tên slider">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Hình ảnh</label>
                            <input type="file" class="form-control" name="slider_image" id="exampleInputEmail1">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Mô tả slider</label>
                            <textarea style="resize: none" rows="5" class="form-control" name ="slider_desc" placeholder="Mô tả slider"></textarea>
                        </div>
                        <div class="form-group">
                        <label for="exampleInputPassword1">Hiển thị slider</label>
                            <select name="slider_status" class="form-control input-sm m-bot15">
                                <option value = "0">Ẩn slider</option>
                                <option value = "1">Hiển thị slider</option>
                            </select>
                        </div>
                        <button type="submit" name="add_slider" class="btn btn-info">Thêm slider</button>
                    </form>
                    </div>
                </div>
            </section>
    </div>
</div>
@endsection