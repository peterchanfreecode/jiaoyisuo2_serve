@extends('agent.layadmin')

@section('page-head')

@endsection

@section('page-content')

    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">order id</label>
                        <div class="layui-input-block">
                            <input type="text" name="id" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">account</label>
                        <div class="layui-input-block">
                            <input type="text" name="account" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">agent name</label>
                        <div class="layui-input-block">
                            <input type="text" name="agentusername" placeholder="Please enter a superior agent"
                                   autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">start date：</label>
                        <div class="layui-input-block" style="width:170px;">
                            <input type="text" class="layui-input" id="start_time" value="" name="start_time">
                        </div>
                    </div>

                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">end date：</label>
                        <div class="layui-input-block" style="width:170px;">
                            <input type="text" class="layui-input" id="end_time" value="" name="end_time">
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">trading</label>
                        <div class="layui-input-block">
                            <select name="status">
                                <option value="-1">unlimited</option>

                                <option value="1">in transaction</option>
                                <option value="2">Closing position</option>
                                <option value="3">closed</option>

                            </select>
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">Type of sale</label>
                        <div class="layui-input-block">
                            <select name="type">
                                <option value="-1">unlimited</option>
                                <option value="1">buy up</option>
                                <option value="2">buy down</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">result</label>
                        <div class="layui-input-block">
                            <select name="profit_result" id="profit_result" class="layui-input">
                                <option value="-2">all</option>
                                <option value="-1">deficit</option>
                                <option value="0">none</option>
                                <option value="1">Profit</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">currency</label>
                        <div class="layui-input-block">
                            <select name="currency_id" id="currency_id" class="">
                                <option value="-1" class="ww">all</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="layui-inline" style="margin-left: 10px;">
                        <label class="layui-form-label">trading pair</label>
                        <div class="layui-input-block">
                            <select name="match_id" id="currency_match" class="">
                                <option value="-1" class="ww">all</option>
                                @foreach ($currency_matches as $match)
                                    <option value="{{$match->id}}" class="ww">{{$match->currency_name}}
                                        /{{$match->legal_name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-useradmin" lay-submit
                                lay-filter="LAY-user-front-search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>


                </div>
            </div>


            <div class="layui-card-body">
                <table id="LAY-user-manage" lay-filter="LAY-user-manage"></table>
                <script type="text/html" id="table-useradmin-webuser">
                    <!-- <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event=""><i class="layui-icon layui-icon-edit"></i>查看详情</a> -->
                </script>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/html" id="status_name">
        <span class="layui-badge @{{d.status == 1 ? '' : 'layui-bg-gray'}}">@{{d.status_en}}</span>
    </script>
    <script type="text/html" id="symbol_name">
        <span>@{{d.symbol_name}}</span><span style="color: #848484dd; font-size: 10px;"
                                             title="收益率:@{{d.profit_ratio}}%">-@{{d.seconds}}S</span>
    </script>
    <script type="text/html" id="type_name">
        @{{# if (d.type == 1) { }}
        <div class="order_type order_type_rise">
            <span style="font-size: 13px; font-weight: bold;">@{{d.type_name_en}}</span><span
                    style="font-size: 18px; font-weight: bold;">↑</span>
        </div>
        @{{# } else { }}
        <div class="order_type order_type_fall">
            <span style="font-size: 13px; font-weight: bold;">@{{d.type_name_en}}</span><span
                    style="font-size: 18px; font-weight: bold;">↓</span>
        </div>
        @{{# } }}
    </script>

    <script type="text/html" id="pre_profit_result_name">
        @{{# if (d.pre_profit_result == 1) { }}
        <span style="color:#89deb3;">@{{d.pre_profit_result_name}}</span>
        @{{# } else if (d.pre_profit_result == -1) { }}
        <span style="color:#d67a7a;">@{{d.pre_profit_result_name}}</span>
        @{{# } else { }}
        <span class="layui-badge-rim">@{{d.pre_profit_result_name}}</span>
        @{{# } }}
    </script>

    <script type="text/html" id="profit_result_name">
        <span class="layui-badge @{{d.profit_result == 1 ? 'layui-bg-green' : ''}}">@{{d.pre_profit_result_name_en}}</span>
    </script>

    <script type="text/html" id="fact_profits">
        <div style="text-align: right; margin-right: 10px;">
            @{{# if (d.profit_result == 1) { }}
            <span style="color: #f00; font-weight: bolder;">@{{Number(d.fact_profits).toFixed(4)}}</span>
            @{{# } else { }}
            <span>@{{Number(d.fact_profits).toFixed(4)}}</span>
            @{{# } }}
        </div>
    </script>

    <script>

        layui.use(['index', 'table', 'layer', 'laydate', 'form'], function () {
            var $ = layui.$
                , admin = layui.admin
                , view = layui.view
                , table = layui.table
                , laydate = layui.laydate
                , form = layui.form;


            laydate.render({
                elem: '#start_time',
                type: 'datetime'
            });
            laydate.render({
                elem: '#end_time',
                type: 'datetime'
            });


            //秒合约订单管理
            var data_table = table.render({
                elem: '#LAY-user-manage'
                , method: 'post'
                , url: '/agen/micro/list'
                , cols: [[
                    {field: '', type: 'checkbox', width: 60}
                    , {field: 'id', title: 'order id', width: 100}
                    , {field: 'user_id', title: 'User ID', width: 130, sort: true}
                    , {field: 'account', title: 'account', width: 160, sort: true, totalRowText: '小计'}
                    , {field: 'real_name', title: 'actual name', width: 140}
                    , {field: 'parent_agent_name', title: 'agent', width: 140}
                    , {field: 'symbol_name', title: 'contract', width: 160, sort: true, templet: '#symbol_name'}
                    , {field: 'currency_name', title: 'currency', width: 100, sort: true}
                    , {field: 'type_name', title: 'type', width: 80, templet: '#type_name', sort: true}
                    , {field: 'seconds', title: 'seconds', width: 80, templet: '#seconds', sort: true, hide: true}
                    , {field: 'status_name', title: 'trading status', width: 140, sort: true, templet: '#status_name'}
                    , {
                        field: 'number',
                        title: 'quantity',
                        width: 90,
                        templet: '<div><div style="text-align: right;">@{{Number.parseInt(d.number)}}</div></div>',
                        totalRow: true
                    }
                    , {
                        field: 'fee',
                        title: 'handling fee',
                        width: 140,
                        totalRow: true,
                        templet: '<div><div style="text-align: right;"><span>@{{Number(d.fee).toFixed(4)}}</span></div></div>'
                    }
//        , { field: 'pre_profit_result_name', title: '预设', width: 90, sort: true, templet: '#pre_profit_result_name', hide: false }
                    , {
                        field: 'profit_result_name',
                        title: 'result',
                        width: 90,
                        sort: true,
                        templet: '#profit_result_name',
                        hide: false
                    }
                    , {
                        field: 'fact_profits',
                        title: 'profit',
                        width: 100,
                        sort: true,
                        totalRow: true,
                        templet: '#fact_profits'
                    }
                    , {
                        field: 'open_price',
                        title: 'opening price',
                        width: 140,
                        templet: '<div><div style="text-align: right;"><span>@{{Number(d.open_price).toFixed(4)}}</span></div></div>'
                    }
                    , {
                        field: 'end_price',
                        title: 'Closing price',
                        width: 140,
                        templet: '<div><div style="text-align: right;"><span>@{{Number(d.end_price).toFixed(4)}}</span></div></div>'
                    }
                    , {field: 'created_at', title: 'order time', width: 170, sort: true}
                    , {field: 'updated_at', title: 'updated', width: 170, sort: true, hide: true}
                    , {field: 'handled_at', title: 'closing time', width: 170, sort: true, hide: true}
                    , {field: 'complete_at', title: 'complete time', width: 170, sort: true, hide: true}
                    //,{fixed: 'right', title: '操作', width: 100, align: 'center', toolbar: '#barDemo'}
                ]]
                , page: true
                , limit: 20
                , limits: [20, 50, 100, 500, 1000]
                , toolbar: true
                , height: 'full-200'
                , totalRow: true
                , text: 'Sorry, there was an exception loading！'
                , headers: { //通过 request 头传递
                    access_token: layui.data('layuiAdmin').access_token
                }
                , where: { //通过参数传递
                    access_token: layui.data('layuiAdmin').access_token
                }
                , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                    if (res !== 0) {

                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }
                        var total = res.extra_data.total;
                        $('#total_fee').text(total.total_fee);
                        $('#total_fact_profits').text(total.total_fact_profits);

                    }
                }
            });

            //监听搜索
            form.on('submit(LAY-user-front-search)', function (data) {
                var a = layui.data('layuiAdmin').access_token;
                data.field.access_token = a;

                var option = {
                    where: data.field,
                    page: {curr: 1}
                }
                data_table.reload(option);
                return false;
            });


        });

    </script>
@endsection
