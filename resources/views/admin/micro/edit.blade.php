@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        
    
    <div class="layui-form-item">
            <label class="layui-form-label">注单涨跌</label>
            <div class="layui-input-block">
                <select name="sytype" lay-verify="required" lay-filter="sytype">
                    <option value="1" {{ $result->type  == 1 ? 'selected' : '' }} >涨</option>
                    <option value="2" {{ $result->type  == 2 ? 'selected' : '' }} >跌</option>

                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">预设盈利状态</label>
            <div class="layui-input-block">
                <select name="risk" lay-verify="required" lay-filter="risk_mode">
                    <!-- <option value=""></option>
                    <option value="0" {{ ($result->pre_profit_result ?? 0) == 0 ? 'selected' : '' }} >无</option>
                    <option value="1" {{ ($result->pre_profit_result ?? 0) == 1 ? 'selected' : '' }} >盈利</option>
                    <option value="-1" {{ ($result->pre_profit_result ?? 0) == -1 ? 'selected' : '' }} >亏损</option> -->

                    <option value="0" {{ $result->pre_profit_result  == 0 ? 'selected' : '' }} >无</option>
                    <option value="1" {{ $result->pre_profit_result  == 1 ? 'selected' : '' }} >盈利</option>
                    <option value="-1" {{ $result->pre_profit_result  == -1 ? 'selected' : '' }} >亏损</option>
                    <option value="2" {{ $result->pre_profit_result  == 2 ? 'selected' : '' }} >涨赢</option>
                    <option value="3" {{ $result->pre_profit_result  == 3 ? 'selected' : '' }} >跌赢</option>

                </select>
            </div>
        </div>

        <input type="hidden" name="id" value="{{$result->id}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <!-- <button type="reset" class="layui-btn layui-btn-primary">重置</button> -->
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
                    url:'{{url('admin/micro_order_edit')}}'
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