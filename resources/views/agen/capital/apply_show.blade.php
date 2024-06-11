@extends('admin._layoutNew')

@section('page-head')
@endsection

@section('page-content')
    <form class="layui-form" action="">
        <input type="hidden" name="id" value="{{$id}}">
        <div class="layui-form-item">
            <label class="layui-form-label">deposit type</label>
            <div class="layui-input-block">
                <select name="is_real" lay-filter="type">
                    <option value="">please choose</option>
                    <option value="1">real deposit</option>
                    <option value="2">Trial play/recharge</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">submit now</button>
                <button type="reset" class="layui-btn layui-btn-primary">reset</button>
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
                    url: '{{url('agen/recharge/pass')}}'
                    , type: 'post'
                    , dataType: 'json'
                    , data: data
                    , success: function (res) {
                        layer.msg(res.msg);
                        if (res.code == 0) {
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