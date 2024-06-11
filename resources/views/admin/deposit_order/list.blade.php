@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')

    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <form class="layui-form layui-form-pane layui-inline" action="">
            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="account_number" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" placeholder="用户ID" autocomplete="off" class="layui-input"
                           value="">
                </div>
            </div>
            <div class="layui-inline" >
                <label>币种&nbsp;&nbsp;</label>
                <div class="layui-input-inline">
                    <select name="currency">
                        <option value="0" class="ww">全部</option>
                        @if(!empty($currencies))
                            @foreach($currencies as $currency)
                                <option value="{{$currency->id}}">{{$currency->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="layui-inline" ">
                <label>状态</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="0" class="ww">全部</option>
                        <option value="-1" class="ww">已取消</option>
                        <option value="1" class="ww">进行中</option>
                        <option value="2" class="ww">已结束</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i
                                class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
            <script type="text/html" id="barDemo">
                @{{d.is_end==1 ? '<a class="layui-btn layui-btn-xs" lay-event="end_order">'+'人工结算'+'</a>' : '' }}

            </script>
        </form>
    </div>
    <table id="demo" lay-filter="demo"></table>
    <script type="text/html" id="statustml">
        @{{d.status==-1 ? '<span class="layui-badge layui-bg-green">'+'已取消'+'</span>' : '' }}
        @{{d.status==1 ? '<span class="layui-badge layui-bg-red">'+'进行中'+'</span>' : '' }}
        @{{d.status==2 ?'<span class="layui-badge layui-bg-blue">'+'已结束'+'</span>' : '' }}
    </script>
@endsection

@section('scripts')
    <script>

        layui.use(['table', 'form'], function () {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例
            table.render({
                elem: '#demo'
                , url: '{{url('admin/deposit_order/list_data')}}' //数据接口
                , page: true //开启分页
                , id: 'demo'
                , cols: [[ //表头
                    {field: 'id', title: '质押ID', Width: 50, sort: true}
                    , {field: 'user_name', title: '用户', minWidth: 50}
                    , {field: 'user_id', title: '用户ID', minWidth: 50}
                    , {field: 'currency_name', title: '币种', Width: 150}
                    , {field: 'level_name', title: '质押等级', Width: 150}
                    , {field: 'amount', title: '存币数量', minWidth: 50}
                    , {field: 'status', title: '状态', minWidth: 100, templet: '#statustml'}
                    , {field: 'day_rate', title: '日利率', minWidth: 50}
                    , {field: 'total_interest', title: '日利息', minWidth: 50}
                    , {field: 'total_interest', title: '总利息', minWidth: 50}
                    , {field: 'start_at', title: '质押开始时间', minWidth: 150}
                    , {field: 'end_at', title: '质押结束时间', minWidth: 150}
                    , {field: 'termination_at', title: '质押终止时间', minWidth: 150}
                    , {title: '操作', minWidth: 150, toolbar: '#barDemo'}
                ]]
                , height: 'full-150'
                , text: {none: '未查询到数据^_^'}
            });


            table.on('tool(demo)', function (obj) {
                var data = obj.data;
                if (obj.event === 'end_order') {
                    layer.confirm('真的人工结算', function (index) {
                        $.ajax({
                            url: '{{url('admin/deposit_order/end_order')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: data.id},
                            success: function (res) {
                                layer.msg(res.message);
                                table.reload('demo'); //数据刷新
                            }
                        });
                    });
                }
            });

            //监听提交
            form.on('submit(mobile_search)', function (data) {
                var account_number = data.field.account_number;
                var currency = data.field.currency;
                var user_id = data.field.user_id;
                var status = data.field.status;
                table.reload('demo', {
                    where: {account_number: account_number, currency: currency, user_id: user_id,status: status},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection