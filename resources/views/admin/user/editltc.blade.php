@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-block" style="margin-top: 10px;">
                {{$result->account_number}}
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">选择矿机</label>
            <div class="layui-input-inline">
                <select name="ltc_id" class="" lay-filter="" lay-verify="required">
                    @foreach($ltc as $value)
                        <option value="{{$value->id}}" @if( $result->ltc_id == $value->id) selected @endif>{{$value->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每日比例</label>
            <div class="layui-input-inline">
                <input type="text" name="ltc_tate" autocomplete="off" class="layui-input" value="@if (isset($result->ltc_tate)){{$result->ltc_tate}}@endif" placeholder="每日比例">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">截止时间</label>
            <div class="layui-input-inline">
                <input class="layui-input newsTime" lay-verify="required|date" name="ltc_end" type="text" value="@if (isset($result->ltc_endtime)){{$result->ltc_endtime}}@endif" id="ltc_end">
            </div>
        </div>

        <input type="hidden" name="id" value="{{$result->id}}">
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

            //初始化日期控件和富文本编辑器
            laydate.render({
                elem: '#ltc_end' //指定元素
            });

            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/user/editltc')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
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