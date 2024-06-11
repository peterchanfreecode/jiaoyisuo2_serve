@extends('agent.layadmin')

@section('title', '提币列表')

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
                        <label class="layui-form-label">币种</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="currency_id">
                                <option value="-1" class="ww">全部</option>
                                @foreach ($legal_currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">用户ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">用户名</label>
                        <div class="layui-input-block">
                            <input type="text" name="account_number" placeholder="请输入" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">所属代理</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="belong_agent">
                                <option value="">全部</option>
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
                <script type="text/html" id="barDemo">
                    @if($level <= 1 )
                     <a class="layui-btn layui-btn-xs" lay-event="show">查看</a>
                    @endif
                </script>
                <script type="text/html" id="statustml">
                    @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'申请提币'+'</span>' : '' }}
                    @{{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'提币完成'+'</span>' : '' }}
                    @{{d.status==3 ? '<span class="layui-badge layui-bg-black">'+'申请失败'+'</span>' : '' }}
                </script>
            </div>

        </div>
    </div>
@endsection

@section('scripts')

    <script>
        layui.use(['index', 'table', 'layer'], function () {
            var $ = layui.$
                , admin = layui.admin
                , view = layui.view
                , table = layui.table
                , form = layui.form

            //提币管理
            table.render({
                elem: '#LAY-user-manage'
                , method: 'get'
                , url: '/agent/capital/withdraw'
                , toolbar: true
                , totalRow: true
                , cols: [[
                    {field: 'id', title: '提币ID', width: 100, sort: true}
                    , {field: 'user_id', title: '用户ID', width: 100}
                    , {field: 'time_info', title: '用户信息', width: 280}
                    , {field: 'currency_name', title: '虚拟币', width: 80}
                    , {field: 'number', title: '提币数量', minWidth: 110}
                    , {field: 'rate', title: '手续费', minWidth: 80}
                    , {field: 'real_number', title: '实际提币', minWidth: 110}
                    , {field: 'status', title: '状态', minWidth: 60, templet: '#statustml'}
                    , {field: 'gold', title: '体验金扣除', minWidth: 100}
                    , {field: 'notes', title: '备注', minWidth: 180}
                    , {title: '操作', minWidth: 150, toolbar: '#barDemo'}

                ]]
                , page: true
                , limit: 30
                , height: 'full-180'
                , text: '对不起，加载出现异常！'

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
            table.on('tool(LAY-user-manage)', function (obj) {
                var data = obj.data;
                if (obj.event === 'show') {
                    layer.open({
                        type: 2,
                        area: ["900px", '700px'],
                        fix: false, //不固定
                        maxmin: true,
                        shade: 0.4,
                        title: "确认提币",
                        content: '{{url('agent/capital/withdraw_show')}}?id=' + data.id,
                        offset: '10px',
                    });
                }

            });

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
    </script>
@endsection