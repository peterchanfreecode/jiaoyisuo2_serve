@extends('agent.layadmin')

@section('page-head')

@endsection

@section('page-content')

    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
                <form class="layui-form  layui-inline" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="please enter" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">account</label>
                        <div class="layui-input-block">
                            <input type="text" name="account_number" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">fiat currency</label>
                        <div class="layui-input-inline" style="width:130px;">
                            <select name="legal" id="type_type">
                                <option value="-1" class="ww">all</option>
                                @foreach ($legal_currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">transaction currency</label>
                        <div class="layui-input-inline" style="width:130px;">
                            <select name="currency" id="type_type">
                                <option value="-1" class="ww">all</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">start date：</label>
                        <div class="layui-input-inline" style="width:120px;">
                            <input type="text" class="layui-input" id="start_time" value="" name="start_time">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">end date：</label>
                        <div class="layui-input-inline" style="width:120px;">
                            <input type="text" class="layui-input" id="end_time" value="" name="end_time">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit="" lay-filter="mobile_search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>


                </form>

            </div>
            <div class="layui-card-body">
                <table id="demo" lay-filter="test"></table>
            </div>
        </div>
    </div>

   

@endsection

@section('scripts')
    <script>

        layui.use(['table','form','laydate'], function(){
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#start_time'
            });
            laydate.render({
                elem: '#end_time'
            });
            //第一个实例
            table.render({
                elem: '#demo'
                ,url: '{{url('agen/order/transaction_list')}}' //数据接口
                ,page: true //开启分页
                , height: 'full-200'
                , limit: 20
                , limits: [20, 50, 100, 500, 1000]
                ,id:'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID',  sort: true}
                    ,{field: 'currency_name', title: 'transaction currency'}
                    ,{field: 'legal_name', title: 'fiat currency'}
                    ,{field: 'account_number', title: 'buyer'}
                    ,{field: 'from_number', title: 'seller'}
                    ,{field: 'price', title: 'price'}
                    ,{field: 'number', title: 'quantity'}
                    ,{field: 'time', title: 'creation time'}
                ]], done: function(res){
                    $("#sum").text(res.extra_data);
                }
            });
           


            //监听提交
            form.on('submit(mobile_search)', function(data){
               
                table.reload('mobileSearch',{
                    where: data.field,
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection