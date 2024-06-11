@extends('agent.layadmin')

@section('page-head')

@endsection
@section('page-content')
    <form class="layui-form" action="" style="margin-top: 20px;margin-left: 50px">
        @foreach ($list as $key=>$value)
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">{{$value->seconds}}秒</label>
                    <input class="layui-input"  name="mic_sec_id[]" type="hidden" value="{{$value->id}}">
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">赢利率</label>
                    <div class="layui-input-inline">
                        <input class="layui-input"  placeholder="请输入赢利率" autocomplete="off" name="profit_ratio[]" type="text" value="{{$value->profit_ratio}}">
                    </div>
                    <div class="layui-form-mid layui-word-aux">%</div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">亏利率</label>
                    <div class="layui-input-inline">
                        <input class="layui-input"  placeholder="请输入亏利率" autocomplete="off" name="lose_ratio[]" type="text" value="{{$value->lose_ratio}}">
                    </div>
                    <div class="layui-form-mid layui-word-aux">%</div>
                </div>
            </div>
        @endforeach

        <input type="hidden" name="user_id" value="{{$user_id}}">
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


        layui.use(['form', 'laydate'], function () {
            var form = layui.form
                , $ = layui.jquery
                , laydate = layui.laydate
                , index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '{{url('/agen/user/postMicro')}}'
                    , type: 'post'
                    , dataType: 'json'
                    , data: data
                    , success: function (res) {
                        console.log(res);
                        if (res.code == 1) {
                            layer.msg(res.msg);
                        } else {
                            layer.msg(res.msg, function () {
                                parent.layer.close(index);
                            });

                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection

