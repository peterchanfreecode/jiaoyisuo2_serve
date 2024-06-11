@extends('agent.layadmin')

@section('title', 'Deposit list')

@section('page-head')

@endsection

@section('page-content')
    <style>
        .layui-table-cell {
            height: auto;
        }
    </style>
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">state</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="status">
                                <option value="0" class="ww">all</option>
                                <option value="1">in application</option>
                                <option value="2">passed</option>
                                <option value="3">rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="uid" placeholder="please enter" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">Currency</label>
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
                        <label class="layui-form-label">username</label>
                        <div class="layui-input-block">
                            <input type="text" name="account_number" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="uid" placeholder="please enter" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">Agent</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="belong_agent">
                                <option value="">all</option>
                                @foreach ($son_agents as $son)
                                    <option value="{{$son->username}}">{{$son->username}}</option>
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

            </div>
        </div>
    </div>
    <script type="text/html" id="statustml">
        @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'Apply for recharge'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'Recharge completed'+'</span>' : '' }}
        @{{d.status==3 ? '<span class="layui-badge layui-bg-black">'+'application failed'+'</span>' : '' }}
    </script>
    <script type="text/html" id="realhml">
        @{{d.is_real==1 ? '<span class="layui-badge layui-bg-green">'+'real deposit'+'</span>' : '' }}
        @{{d.is_real==2 ? '<span class="layui-badge layui-bg-red">'+'Trial play/recharge'+'</span>' : '' }}
    </script>
    <script type="text/html" id="ophtml">
        @if($level <= 1 )
        @{{d.status==1 ? '
        <button class="layui-btn layui-btn-normal layui-btn-xs" onclick="pass('+d.id+')">pass</button>
        <button class="layui-btn layui-btn-danger layui-btn-xs" onclick="refuse('+d.id+')" data-id='+d.id+'
                class="btn-refuse">reject
        </button>' : '' }}
        @endif
    </script>
@endsection

@section('scripts')

    <script>
        let $;
        layui.use(['index', 'table', 'layer'], function () {
            $ = layui.$
                , admin = layui.admin
                , view = layui.view
                , table = layui.table
                , form = layui.form


            //充币管理
            table.render({
                elem: '#LAY-user-manage'
                , method: 'get'
                , url: '{{url('agen/capital/apply')}}'
                , toolbar: true
                , totalRow: true
                , cols: [[
                    {field: 'id', title: 'Deposit ID', width: 100, sort: true}
                    , {field: 'time_info', title: 'User Info', width: 270}
                    , {field: 'currency_name', title: 'Virtual currency', width: 80}

                    , {field: 'amount', title: 'quantity', minWidth: 80},
                    {
                        field: "charge_url", align: 'center', title: "Recharge screenshot", templet: function (item) {
                            return '<img onclick="showimg(this)" src="' + item.charge_url + '">';
                        }
                    }
                    , {field: 'status', title: 'trading status', minWidth: 100, templet: '#statustml'}
                    , {field: 'is_real', title: 'deposit type', minWidth: 100, templet: '#realhml'}
                    , {field: 'remark', title: 'Remark', minWidth: 180}
                    , {title: 'operate', minWidth: 120, templet: '#ophtml'}
                ]]
                , page: true
                , limit: 30
                , height: 'full-180'
                , text: 'Sorry, there was an error loading！'

                , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                    if (res !== 0) {
                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }
                    }
                }
            });

            form.render(null, 'layadmin-userfront-formlist');

            //监听搜索
            form.on('submit(LAY-user-front-search)', function (data) {
                var field = data.field;

                //执行重载
                table.reload('LAY-user-manage', {
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

        });
        function showimg(t) {
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                skin: 'layui-layer-nobg', //没有背景色
                shadeClose: true,
                content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + t.src + '">'
            });
        }

        function pass(id) {
            layer.open({
                type: 2,
                area: ["500px", '300px'],
                title: "OK to pass",
                content: '{{url('agen/recharge/req_show')}}?id=' + id,
            });
        }

        function refuse(id) {
            layer.prompt({
                formType: 3,
                title: 'Please enter a reason for rejection'
                , placeholder: 'Please enter a reason for rejection'
                , btn: ['Sure', 'Cancel']
                , btn2: function (index) {
                    layer.close(index);
                }
                , cancel: function (index) {
                    layer.close(index);
                }
            }, function (value, index) {
                layer.close(index);
                $.ajax({
                    url: '{{url('agen/recharge/refuse')}}',
                    type: 'post',
                    dataType: 'json',
                    data: {id: id, remark: value},
                    success: function (res) {
                        layer.msg(res.msg);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1200)

                    }
                })
                return true;
            });

        }
    </script>
@endsection
