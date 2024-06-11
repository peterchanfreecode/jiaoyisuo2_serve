@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div class="layui-form">
        <div class="layui-item">
            <div class="layui-inline" style="margin-left: 10px;">
                <label>用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" placeholder="请输入用户ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label >开始日期：</label>
                <div class="layui-input-inline" style="width:170px;">
                    <input type="text" class="layui-input" id="start_time" value="" name="start_time">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label >结束日期：</label>
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
                , url: '{{url('agent/agent_report/list')}}' //数据接口
                , page: true
                ,limit: 50
                ,limits: [20, 50, 100, 500, 1000]
                , height: 'full-100'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 100, sort: true}
                    , {field: 'date_time', title: '日期'}
                    , {field: 'agent_id', title: '代理ID'}
                    , {field: 'user_id', title: '用户ID'}
                    , {field: 'agent_name', title: '代理账号'}
                    , {field: 'user_num', title: '新用户'}
                    , {field: 'charge_amount', title: '充值金额(USDT)'}
                    , {field: 'withdraw_amount', title: '提现金额(USDT)'}
                    , {field: 'rebate_amount', title: '今日返佣'}
                    , {field: 'total_candy_number', title: '累计锁仓'}
                    , {field: 'total_user_num', title: '总人数'}
                    , {field: 'total_charge_amount', title: '总充值'}
                    , {field: 'total_withdraw_amount', title: '总提现'}
                    , {field: 'total_rebate_amount', title: '总返佣'}
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
                    ,text: {none: '未查询到数据^_^'}
                });
            });
        });
    </script>

@endsection