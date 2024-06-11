@extends('agent.layadmin')

@section('page-head')

@endsection
@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>新密码</label>
            <div class="layui-input-inline">
                <input type="password" id="new_password" name="new_password" lay-verify="required" placeholder="请输入新密码"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>确认密码</label>
            <div class="layui-input-inline">
                <input type="password" id="confirm_new_password" name="confirm_new_password" lay-verify="required"
                       placeholder="请再次输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <input type="hidden" name="id" value="{{$user_id}}">
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
        layui.use(['form'], function () {
            var form = layui.form
                , $ = layui.jquery
                , index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '{{url('/agent/user/do_update_password')}}'
                    , type: 'post'
                    , dataType: 'json'
                    , data: data
                    , success: function (res) {
                        console.log(res);
                        if (res.code == 1) {
                            layer.msg(res.msg);
                        } else {
                            layer.msg(res.msg);
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

