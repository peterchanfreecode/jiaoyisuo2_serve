@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <table id="demo" lay-filter="test"></table>
@endsection

@section('scripts')
    <script>
        layui.use(['table', 'form'], function () {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            table.render({
                elem: '#demo'
                , toolbar: true
                , url: '{{url('admin/daily_report/list')}}' //数据接口
                , page: true
                ,limit: 50
                ,limits: [20, 50, 100, 500, 1000]
                , height: 'full-100'
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 100, sort: true}
                    , {field: 'c_date', title: '日期'}
                    , {field: 'install_count', title: '新用户'}
                    , {field: 'rep_amount', title: '充值金额(USDT)'}
                    , {field: 'withdraw_amount', title: '提现金额(USDT)'}
                    , {field: 'profit_amount', title: '今日盈利'}
                    , {field: 'month_profit_amount', title: '本月盈利'}
                ]]
            });


        });
    </script>

@endsection