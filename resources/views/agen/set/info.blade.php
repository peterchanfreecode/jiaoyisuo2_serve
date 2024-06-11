@extends('agent.layadmin')

@section('title', '设置资料')

@section('page-head')

@endsection

@section('page-content')
  
<div class="layui-fluid">
  <div class="layui-row layui-col-space15">
    <div class="layui-col-md12">
      <div class="layui-card">
        <div class="layui-card-header">set up my profile</div>
        <div class="layui-card-body" pad15>
          
          <div class="layui-form" lay-filter="">
           
            <div class="layui-form-item">
              <label class="layui-form-label">my role</label>
              <div class="layui-input-inline">
                <input type="text"  value="{{$agent->self_info}}" readonly class="layui-input">
               
              </div>
              <div class="layui-form-mid layui-word-aux">The current role cannot be changed to another role</div>
            </div>
            <div class="layui-form-item">
              <label class="layui-form-label">username</label>
              <div class="layui-input-inline">
                <input type="text" name="username" value="{{$agent->username }}" readonly class="layui-input">
              </div>
              <div class="layui-form-mid layui-word-aux">Unchangeable. Usually used for background login</div>
            </div>
            <div class="layui-form-item">
              <label class="layui-form-label">Nick name</label>
              <div class="layui-input-inline">
                <input type="text" name="nickname" value="{{$agent->nickname}}" lay-verify="nickname" autocomplete="off" placeholder="请输入昵称" class="layui-input">
              </div>
            </div>
            
            <div class="layui-form-item">
              <label class="layui-form-label">phone</label>
              <div class="layui-input-inline">
                <input type="text" name="phone" value="{{$agent->phone}}" lay-verify="phone" autocomplete="off" class="layui-input">
              </div>
            </div>
            <div class="layui-form-item">
              <label class="layui-form-label">Mail</label>
              <div class="layui-input-inline">
                <input type="text" name="email" value="{{$agent->email}}" lay-verify="email" autocomplete="off" class="layui-input">
              </div>
            </div>
            
            <div class="layui-form-item">
              <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="setmyinfo">Confirm the changes</button>
                <button type="reset" class="layui-btn layui-btn-primary">fill in again</button>
              </div>
            </div>
          
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
layui.use(['index','form','layer','admin'], function(){
          
            var $ = layui.jquery;
            var form = layui.form;
            var admin = layui.admin;
            //设置我的资料
            form.on('submit(setmyinfo)', function(obj){
              //layer.msg(JSON.stringify(obj.field));

              //提交修改
              admin.req({
                  url: '/agen/save_user_info'
                  ,data: obj.field
                  ,type:'post'
                  ,success: function(res){
                      console.log(res);
                      if (res.code == 0){
                          //登入成功的提示与跳转
                          layer.msg(res.msg, {
                              icon: 1
                              ,time: 2000
                          });
                      }else{
                          layer.msg(res.msg, {
                              icon: 5
                          });
                      }
                  }
              });
              return false;
            });



          

          });



</script>
@endsection