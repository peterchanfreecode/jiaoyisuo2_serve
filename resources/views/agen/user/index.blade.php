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
                        <label class="layui-form-label">start date</label>
                        <div class="layui-input-block">
                            <input type="text" name="start" id="datestart" placeholder="yyyy-MM-dd" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">end date</label>
                        <div class="layui-input-block">
                            <input type="text" name="end" id="dateend" placeholder="yyyy-MM-dd" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="id" placeholder="please enter" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">username</label>
                        <div class="layui-input-block">
                            <input type="text" name="account_number" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">currency</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="currency_id">
                                <option value="-1" class="ww">all</option>
                                @foreach ($legal_currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="san-user-search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                        <!-- <button class="layui-btn layuiadmin-btn-useradmin"  onclick="javascript:window.location.href='/order/users_excel'">导出Excel</button> -->
                        <button class="layui-btn layui-btn-normal dao" lay-event="excel">export table</button>
                    </div>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-carousel layadmin-backlog" style="background-color: #fff">
                    <ul class="layui-row layui-col-space10 layui-this">
                        <li class="layui-col-xs6">
                            <a href="javascript:;" onclick="layer.tips('total number of users', this, {tips: 3});"
                               class="layadmin-backlog-body" style="color: #fff;background-color: #01AAED;">
                                <h3>total number of users：</h3>
                                <p><cite style="color:#fff" id="_num">0</cite></p>
                            </a>
                        </li>
                        <li class="layui-col-xs6">
                            <a href="javascript:;" onclick="layer.tips('Number of reseller users', this, {tips: 3});"
                               class="layadmin-backlog-body" style="color: #fff;background-color: #01AAED;">
                                <h3>Number of reseller users</h3>
                                <p><cite style="color:#fff" id="_daili">0</cite></p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="layui-card-body">
                <div class="layui-carousel layadmin-backlog" style="background-color: #fff">
                    <table id="san-user-manage" lay-filter="san-user-manage"></table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .layui-table-cell {
            height: auto;
        }
    </style>
@endsection

@section('scripts')
    <script type="text/html" id="table-useradmin-webuser">
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="update_pass">change Password</a><br/>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="wallet_info">View funding</a><br/>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="flow">cash flow</a>
        @if($is_admin== 1 )
        @{{#  if(d.agent_id ==0 ){ }}
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="update_agent"><i class="layui-icon"></i>change agent</a>
        @{{#  } }}
        @endif
    </script>


    <script>
        layui.use(['index', 'laydate', 'form', 'table'], function () {
            var $ = layui.$
                , admin = layui.admin
                , table = layui.table
                , layer = layui.layer
                , laydate = layui.laydate
                , form = layui.form;


            //日期
            laydate.render({
                elem: '#datestart'
            });
            laydate.render({
                elem: '#dateend'
            });

            var parent_id = {{ $parent_id }};
            // console.log(parent_id);

            admin.req({
                type: "POST",
                url: '/agen/get_user_num',
                dataType: "json",
                data: {all: 1, parent_id: parent_id},
                done: function (result) { //返回数据根据结果进行相应的处理
                    $("#_num").html(result.data._num);
                    $("#_daili").html(result.data._daili);
                    $("#_ru").html(result.data._ru);
                    $("#_chu").html(result.data._chu);

                }
            });
            parent_id = parent_id || 0;
            table.render({
                elem: '#san-user-manage'
                , url: '/agen/user/lists?parent_id=' + parent_id //模拟接口
                , cols: [[
                    {field: 'id', width:120, title: 'User ID', sort: true}
                    , {field: 'my_en_agent_level', width:120,title: 'Agency level'}
                    , {field: 'user_info',  minWidth:270,title: 'account information'}
                    , {field: 'time_info',  minWidth:270,title: 'time information'}
                    , {field: 'invite_info',  minWidth:370, title: 'Promotional information'}
                    , {field: 'money_info',minWidth:370, title: 'Funding Information', }
                    , {field: 'lock_info', minWidth:170,title: 'Asset Freeze', }
                    , {
                        title: 'operate',
                        align: 'center',
                        minWidth:200,
                        toolbar: '#table-useradmin-webuser'
                    }
                ]]
                , page: true
                , limit: 30
                , height: 'full-320'
                , text: 'Sorry, there was an exception loading！'

                , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                    if (res !== 0) {
                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }
                    }
                }
            });


            table.on('tool(san-user-manage)', function (obj) {
                var event = obj.event;
                var data = obj.data;
                if (event == 'status_close') { //删除
                    layer.confirm('OK to disable？',{
                        btn : ['Sure', 'Cancel']
                    },function (index) {
                        //向服务端发送删除指令
                        $.ajax({
                            url: "{{url('/agen/user/doLock')}}",
                            type: 'post',
                            dataType: 'json',
                            data: {id: data.id, status: 1},
                            success: function (res) {
                                layer.close(index);
                                layer.msg(res.msg);
                                table.reload('san-user-manage');
                            }
                        });
                    });
                }
                if (event == 'status_open') { //删除
                    layer.confirm('OK to enable？',{
                        btn : ['Sure', 'Cancel']
                    },function (index) {
                        //向服务端发送删除指令
                        $.ajax({
                            url: "{{url('/agen/user/doLock')}}",
                            type: 'post',
                            dataType: 'json',
                            data: {id: data.id, status: 0},
                            success: function (res) {
                                layer.close(index);
                                layer.msg(res.msg);
                                table.reload('san-user-manage');
                            }
                        });
                    });
                }
                if (event == 'edit') {
                    layer.open({
                        title: 'Risk control configuration'
                        , type: 2
                        , content: '{{url('/agen/user/risk')}}?id=' + data.id
                        // , maxmin: true
                        , area: ['500px', '300px']
                    });
                }
                if (event == 'micro') {
                    //查看订单

                    layer.open({
                        title: 'interest rate control'
                        , type: 2
                        , content: '{{url('/agen/user/user_mic')}}?id=' + data.id
                        // , maxmin: true
                        , area: ['900px', '500px']
                    });
                }

                if (event == 'wallet_info') {
                    //查看资金

                    layer.open({
                        title: 'View funding'
                        , type: 2
                        , content: '{{url('/agen/user/users_wallet')}}?id=' + data.id
                        // , maxmin: true
                        , area: ['900px', '700px']
                    });


                } else if (event == 'micro_risk') {
                    layer.open({
                        title: 'User point control'
                        , type: 2
                        , content: '{{url('/agen/user/risk')}}?id=' + data.id
                        // , maxmin: true
                        , area: ['400px', '300px']
                    });

                }
                if (event == 'flow') {
                    //查看订单

                    layer.open({
                        title: 'cash flow'
                        , type: 2
                        , content: '{{url('/agen/user/flow')}}?id=' + data.id
                        , area: ['1000px', '800px']
                    });
                }
                if (event == 'son') {
                    load(data.id);
                }
                if (event === 'update_agent') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入代理ID'
                        , placeholder: '请输入代理ID'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('/agent/user/update_user_agent')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, agent_id: value},
                            success: function (res) {
                                if (res.code == "0") {
                                    layer.alert(res.msg);
                                    layer.close(index);
                                    load(parent_id);
                                } else {
                                    layer.alert(res.msg);
                                    layer.close(index);
                                }

                            }
                        })

                        return true;
                    });
                }
                if (event === 'update_pass') {
                    layer.open({
                    title: 'change Password'
                        , type: 2
                        , content: '{{url('/agen/user/update_password')}}?id=' + data.id
                        // , maxmin: true
                        , area: ['500px', '350px']
                    });
                }
            });


            form.render(null, 'layadmin-userfront-formlist');

            //监听搜索
            form.on('submit(san-user-search)', function (data) {
                var field = data.field;


                admin.req({
                    type: "POST",
                    url: '/agen/get_user_num',
                    dataType: "json",
                    data: field,
                    done: function (result) { //返回数据根据结果进行相应的处理
                        $("#_num").html(result.data._num);
                        $("#_daili").html(result.data._daili);
                        $("#_ru").html(result.data._ru);
                        $("#_chu").html(result.data._chu);
                    }
                });

                //执行重载
                table.reload('san-user-manage', {
                    where: field
                    , page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行

                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }
                        if (res.code === 1) {
                            layer.msg(res.msg, {icon: 5});
                        }
                    }
                });
            });

            //导出表格
            $('.dao').click(function () {
                var id = $('input[name="id"]').val();
                var account_number = $('input[name="account_number"]').val();
                var start = $('input[name="start"]').val();
                var end = $('input[name="end"]').val();

                var url = '/agent/users_excel?id=' + id + '&account_number=' + account_number + '&start=' + start + '&end=' + end;
                window.open(url);

            })

        });
    </script>

@endsection