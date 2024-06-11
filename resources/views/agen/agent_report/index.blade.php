@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div class="layui-form">
        <div class="layui-item">
            <div class="layui-inline" style="margin-left: 10px;">
                <label>User ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" placeholder="请输入用户ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label >start date：</label>
                <div class="layui-input-inline" style="width:170px;">
                    <input type="text" class="layui-input" id="start_time" value="" name="start_time">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label >end date：</label>
                <div class="layui-input-inline" style="width:170px;">
                    <input type="text" class="layui-input" id="end_time" value="" name="end_time">
                </div>
            </div>
            <div class="layui-inline">
                <button class=" layui-btn-normal layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="filter_search" style="margin-bottom: 2px">
                    <i class="layui-icon layui-icon-search  layuiadmin-button-btn"></i>
                </button>
            </div>
        </div>
    </div>
    <table id="demo" lay-filter="demo"></table>
@endsection

@section('scripts')
    <script>
        layui.use(['table', 'form', 'laydate'], function () {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#start_time',
                type:'datetime'
            });
            laydate.render({
                elem: '#end_time',
                type:'datetime'
            });
            table.render({
                elem: '#demo'
                , toolbar: true
                , url: '{{url('agen/agent_report/list')}}' //数据接口
                , page: true
                ,limit: 50
                ,limits: [20, 50, 100, 500, 1000]
                , height: 'full-100'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 100, sort: true}
                    , {field: 'date_time', title: 'date'}
                    , {field: 'agent_id', title: 'Proxy ID'}
                    , {field: 'user_id', title: 'User ID'}
                    , {field: 'agent_name', title: 'Proxy account'}
                    , {field: 'user_num', title: 'new user'}
                    , {field: 'charge_amount', title: 'Deposit Amount (USDT)'}
                    , {field: 'withdraw_amount', title: 'Withdrawal amount (USDT)'}
                    , {field: 'rebate_amount', title: "oday's rebate"}
                    , {field: 'total_candy_number', title: 'Cumulative lockup'}
                    , {field: 'total_user_num', title: 'total people'}
                    , {field: 'total_charge_amount', title: 'otal recharge'}
                    , {field: 'total_withdraw_amount', title: 'total withdrawal'}
                    , {field: 'total_rebate_amount', title: 'total rebate'}
                ]]
            });
            form.on('submit(filter_search)', function(data){
                var field = data.field;
                //执行重载
                table.reload('demo', {
                    where: field
                    ,page: {
                        curr: 1 //
                    }
                    ,text: {none: 'No data found^_^'}
                });
            });
        });
    </script>

@endsection