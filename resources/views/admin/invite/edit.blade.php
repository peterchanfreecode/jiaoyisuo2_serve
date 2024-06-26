@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
       
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">图片</label>
            <div class="layui-input-block">
                <button class="layui-btn" type="button" id="upload_test">选择图片</button>
                <br>
                <img src="@if(!empty($res->pic)){{$res->pic}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($res->pic)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                <input type="hidden" name="pic" id="thumbnail" value="@if(!empty($res->pic)){{$res->pic}}@endif">
            </div>
        </div>
        <input type="hidden" name="id" value="{{$res->id}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script>
        layui.use('upload', function(){
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#upload_test' //绑定元素
                ,url: '{{URL("admin/dgx_upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#thumbnail").val(res.message.path)
                        $("#img_thumbnail").show()
                        $("#img_thumbnail").attr("src",res.message.full_path)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
        });


        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);


            //监听提交
            form.on('submit(demo1)', function(data){

                var data = data.field;

                $.ajax({
                    url:'{{url('admin/invite/edit')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        //console.log(res);
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection