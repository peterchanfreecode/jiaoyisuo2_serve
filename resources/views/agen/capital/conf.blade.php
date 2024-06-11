@extends('admin._layoutNew')
@section('page-head')

@stop
@section('page-content')
    <div class="larry-personal-body clearfix">
        <form class="layui-form col-lg-5">
            <div class="layui-form-item">
                <label class="layui-form-label">user account</label>
                <div class="layui-input-block">
                    <input type="text" name="account" autocomplete="off" class="layui-input layui-disabled"
                           value="{{ $results['account'] }}" placeholder="" disabled>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">wallet currency</label>
                <div class="layui-input-block">
                    <input type="text" name="currency" autocomplete="off" class="layui-input layui-disabled"
                           value="{{ $results['currency_name'] }}" placeholder="" disabled>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">reconcile account</label>
                <div class="layui-input-block">
                    <select name="type" lay-verify="required">
                        <option value="3">Currency Wallet</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Adjustment method</label>
                <div class="layui-input-block">
                    <input type="radio" name="way" value="increment" title="Increase" checked>
                    <input type="radio" name="way" value="decrement" title="reduce">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">adjustment value</label>
                <div class="layui-input-block">
                    <input type="text" name="conf_value" required lay-verify="required" placeholder="Please enter the value to be adjusted"
                           autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">Regulatory remarks</label>
                <div class="layui-input-block">
                    <textarea name="info" placeholder="Please enter content" class="layui-textarea" lay-verify="required"></textarea>
                </div>
            </div>


            <input type="hidden" name="id" value="{{$results['id']}}">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="user_submit">submit now</button>
                    <button type="reset" class="layui-btn layui-btn-primary">reset</button>
                </div>
            </div>
        </form>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">


        layui.use(['form', 'upload', 'layer'], function () {
            var layer = layui.layer;
            var form = layui.form;
            var $ = layui.$;

            form.on('submit(user_submit)', function (data) {
                var data = data.field;
                console.log(data)
                $.ajax({
                    url: '{{url('agen/user/conf')}}',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.msg(res.msg);
                        if (res.code == 0) {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.window.location.reload();
                        } else {
                            return false;
                        }
                    }
                });
                return false;
            });
        });


    </script>
@stop