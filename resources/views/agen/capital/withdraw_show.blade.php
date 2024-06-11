@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">Withdrawal information</label>
            <div class="layui-input-block">
               <table class="layui-table">
                <tbody>
                    <tr>
                        <td>
                            account name：{{$wallet_out->account_number}}
                        </td>
                        <td>
                            Currency：{{$wallet_out->currency_name}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Currency Type: Based on{{$wallet_out->currency_type}}
                        </td>
                        <td>
                            rate：{{$wallet_out->rate}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Withdrawal amount：{{$wallet_out->number}}
                        </td>
                        <td>
                            The actual amount received：{{$wallet_out->real_number}}
                        </td>
                    </tr>
                    <tr>
                         <td colspan="2">
                             Withdrawal address：{{$wallet_out->address}}
                        </td>
                    </tr>
                    @if($wallet_out->status == 1 || $wallet_out->status == 2)
                    @endif
                    
                    <tr>
                        <td>
                            application time：{{$wallet_out->create_time}}
                        </td>
                        <td>
                            current status：@if($wallet_out->status==1) submit application
								     @elseif($wallet_out->status==2) Successful withdrawal
								     @elseif($wallet_out->status==3) Withdrawal failed
								    @else
                                    @endif
                        </td>
                    </tr>

                </tbody>
            </table>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">Feedback</label>
            <div class="layui-input-block">
               <textarea name="notes" id="" cols="90" rows="10">{{$wallet_out->notes}}</textarea>
            </div>
        </div>
        <input type="hidden" name="id" value="">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name='id' value='{{$wallet_out->id}}'>
                @if($wallet_out->status==1)
                <button class="layui-btn" lay-submit="" lay-filter="demo1" name='method' value="done">Confirm withdrawal</button>
                <button class="layui-btn layui-btn-danger" lay-submit="" lay-filter="demo2">return application</button>
                @endif
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
            form.on('submit(demo1)', function(data) {
                var data = data.field;
                layer.confirm('Confirm that withdrawal is allowed?', function (index) {
                    var loading = layer.load(1, {time: 30 * 1000});
                    layer.close(index);
                    $.ajax({
                        url: '{{url('agen/capital/withdraw_done')}}'+'?method=done'
                        ,type: 'post'
                        ,dataType: 'json'
                        ,data : data
                        ,success: function(res) {
                            if (res.type=='error') {
                                layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                                parent.layer.close(index);
                                parent.window.location.reload();
                            }
                        }
                        ,complete: function () {
                            layer.close(loading);
                        }
                    });
                });
                return false;
            });
            form.on('submit(demo2)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('agen/capital/withdraw_done')}}'
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