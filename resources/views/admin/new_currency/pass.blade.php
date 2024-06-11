@extends('admin._layoutNew')

@section('page-head')
@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">活动名称</label>
            <div class="layui-input-block">
                <input class="layui-input" disabled="disabled" type="text" value="@if (isset($title)){{$title}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">申购数量（USDT）</label>
            <div class="layui-input-block">
                <input class="layui-input" disabled="disabled" type="text" value="@if (isset($result['coin_amount'])){{$result['coin_amount']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">申购币种</label>
            <div class="layui-input-block">
                <input class="layui-input" disabled="disabled" type="text" value="@if (isset($result['currency_name'])){{$result['currency_name']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结算币种</label>
            <div class="layui-input-block">
                <input class="layui-input" disabled="disabled" type="text" value="@if (isset($result['pay_currency_name'])){{$result['pay_currency_name']}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">中标率%</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="rate">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="desc" >
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
        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/new_currency/pass')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            layer.msg('操作成功');
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