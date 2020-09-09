@extends('admin_layout')
@section('admin_content')
<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
        Liệt kê Slider
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
            <th>Tên slide</th>
            <th>Hình ảnh</th>
            <th>Mô tả</th>
            <th>Tình trạng</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($all_slide as $key => $slide )
          <tr>
            <td>{{$slide->slider_name }}</td>
            <td><img src="public/uploads/slider/{{$slide->slider_image}}" height="100" width="250"><img></td>
            <td>{{$slide->slider_desc }}</td>
            <td>
              <span class="text-ellipsis">
                <?php
                  if($slide->slider_status==0){
                    ?>
                    <a href="{{URL::to('/unactive-slide/'.$slide->slider_id )}}"><span class="fa-thumb-styling fa fa-thumbs-down"></span></a>
                  <?php
                  }
                  else{
                    ?>
                    <a href="{{URL::to('/active-slide/'.$slide->slider_id )}}"><span class="fa-thumb-styling fa fa-thumbs-up"></span></a>
                  <?php
                  }
                ?>
              </span>
            </td>
            <td>
              
              <a onclick="return confirm('Bạn có chắc chắn muốn xóa slide này không?')" href="{{URL::to('/delete-slide/'.$slide->slider_id)}}" class="active styling-edit" ui-toggle-class="">
                <i class="fa fa-times text-danger text"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection