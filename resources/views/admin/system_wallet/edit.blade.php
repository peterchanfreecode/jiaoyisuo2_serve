@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">

        <div class="layui-form-item">
            <label class="layui-form-label">交易币</label>
            <div class="layui-input-inline">
                <select name="currency_id" lay-filter="" lay-search>
                    <option value=""></option>
                    @if(!empty($currencies))
                        @foreach($currencies as $currency)
                            <option value="{{$currency->id}}"
                                    @if($currency->id == $result->currency_id) selected @endif>{{$currency->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">类型</label>
            <div class="layui-input-inline">
                <input type="text" name="type"  lay-verify="required"  autocomplete="off" placeholder="trc20或erc20"
                       class="layui-input" value="@if(!empty($result->type)){{$result->type}}@endif">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">钱包地址</label>
            <div class="layui-input-block">
                <input type="text" name="address" lay-verify="required" autocomplete="off" placeholder=""
                       class="layui-input" value="@if(!empty($result->address)){{$result->address}}@endif">
            </div>
        </div>
        <!-- <div class="layui-form-item">
            <label class="layui-form-label">安全码</label>
            <div class="layui-input-inline">
                <input type="text" name="stepcode" lay-verify="required" autocomplete="off" placeholder=""
                       class="layui-input" value="">
            </div>
        </div> -->

        <input type="hidden" name="id" value="@if(!empty($result->id)){{$result->id}}@endif">
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


        layui.use(['form','laydate','upload'], function () {
            var form = layui.form
                , $ = layui.jquery
                , laydate = layui.laydate
                ,upload = layui.upload
                , index = parent.layer.getFrameIndex(window.name);
            form.on('submit(demo1)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '{{url('admin/system_wallet/edit')}}'
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