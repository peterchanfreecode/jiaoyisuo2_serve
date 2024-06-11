@extends('admin._layoutNew')

@section('page-head')
@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label for="currency_id" class="layui-form-label">选择代理</label>
            <div class="layui-input-block">
                <select name="agent_id" lay-verify="required" lay-search>
                    @foreach ($agent as $c)
                        <option value="{{ $c->id }}" @if ((isset($result) && $result->agent_id == $c->id)) selected @endif>{{ $c->username }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">代理域名</label>
            <div class="layui-input-block">
                <input type="text" name="agent_domain" id="agent_domain" required autocomplete="off" placeholder=""
                       class="layui-input"
                       value="{{$result->agent_domain}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">客服链接</label>
            <div class="layui-input-block">
                <input id="level_fra" type="text" name="agent_kefu" lay-verify="required" autocomplete="off"
                       placeholder="" class="layui-input" value="{{$result->agent_kefu}}">
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
        layui.use(['form', 'laydate'], function () {
            var form = layui.form
                , $ = layui.jquery
                , laydate = layui.laydate
                , index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '{{url('admin/agent/postAdd')}}'
                    , type: 'post'
                    , dataType: 'json'
                    , data: data
                    , success: function (res) {
                        if (res.type == 'error') {
                            layer.msg(res.message);
                        } else {
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