@extends('admin._layoutNew')

@section('page-head')
    <style>
        [hide] {
            display: none;
        }
    </style>
    <style>
        .layui-table-cell {
            height: auto;
        }
    </style>
@endsection

@section('page-content')
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">


        <form class="layui-form layui-form-pane layui-inline" action="">
            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" id="user_id" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="account_number" id="account_number" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">提币类型</label>
                <div class="layui-input-inline">
                    <select name="is_real" id="is_real">
                        <option value="">全部</option>
                        <option value="1">真实提币</option>
                        <option value="2">代充提币</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <div class="layui-input-inline date_time111" style="margin-left: 50px;">
                    <input type="text" name="start_time" id="start_time" placeholder="请输入开始时间" autocomplete="off"
                           class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <div class="layui-input-inline date_time111" style="margin-left: 50px;">
                    <input type="text" name="end_time" id="end_time" placeholder="请输入结束时间" autocomplete="off"
                           class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i
                                class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal" id="csv">导出数据</button>
    </div>
    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_recommend" value="@{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="sexDemo" @{{ d.is_recommend== 1 ? 'checked' : '' }}>
    </script>

    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="barDemo">

        <a class="layui-btn layui-btn-xs" lay-event="show">查看</a>
        <a class="layui-btn layui-btn-xs" lay-event="edit_adress">修改地址</a><br/>
        @{{d.status==3 ? '<a class="layui-btn layui-btn-xs layui-bg-red" lay-event="back">'+'重置订单'+'</a>' : '' }}
    </script>
    <script type="text/html" id="statustml">
        @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'申请提币'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'提币完成'+'</span>' : '' }}
        @{{d.status==3 ? '<span class="layui-badge layui-bg-black">'+'申请失败'+'</span>' : '' }}

    </script>
@endsection

@section('scripts')
    <script>

        layui.use(['table', 'form', 'laydate'], function () {
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
            var data_table = table.render({
                elem: '#demo'
                , url: "{{url('admin/cashb_list')}}" //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: '提币ID', width: 100, sort: true}
                    , {field: 'user_id', title: '用户ID', width: 100}
                    , {field: 'account_number', title: '用户名', width: 100}
                    , {field: 'belong_agent_name', title: '代理', width: 150}
                    , {field: 'currency_name', title: '虚拟币', width: 80}
                    , {field: 'real_type', title: '提币类型', minWidth: 110}
                    , {field: 'number', title: '提币数量', minWidth: 110}
                    , {field: 'rate', title: '手续费', minWidth: 80}
                    , {field: 'real_number', title: '实际提币', minWidth: 110}
                    , {field: 'status', title: '状态', minWidth: 60, templet: '#statustml'}
                    , {field: 'gold', title: '体验金扣除', minWidth: 100}
                    , {field: 'create_time', title: '提币时间', minWidth: 180}
                    , {field: 'notes', title: '备注', minWidth: 180}
                    , {title: '操作', minWidth: 150, toolbar: '#barDemo'}

                ]]
                , height: 'full-150'
                , text: {none: '未查询到数据^_^'}
            });
            $('#csv').click(function () {
                var user_id = $("#user_id").val();
                var account_number = $("#account_number").val();
                var is_real = $("#is_real").val();
                var start_time = $("#start_time").val();
                var end_time = $("#end_time").val();
                var str = "user_id=" + user_id + "&account_number=" + account_number + "&is_real=" + is_real + "&start_time=" + start_time + "&end_time=" + end_time;
                window.location.href = '{{url('/admin/cashb_csv')}}' + "?" + str;

            });
            table.on('tool(test)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    layer.confirm('真的删除行么', function (index) {
                        $.ajax({
                            url: '{{url('admin/cashb_show')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: data.id},
                            success: function (res) {
                                if (res.type == 'error') {
                                    layer.msg(res.message);
                                } else {
                                    obj.del();
                                    layer.close(index);
                                }
                            }
                        });


                    });
                } else if (obj.event === 'show') {
                    layer_show('确认提币', '{{url('admin/cashb_show')}}?id=' + data.id, 800, 600);
                }
                if (obj.event === 'back') {
                    layer.prompt({
                        formType: 3,
                        title: '请输入谷歌安全码'
                        , placeholder: '请输入谷歌安全码'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        layer.confirm('确定重置?', function (index) {
                            $.ajax({
                                url: '{{url('admin/cashb_back')}}',
                                type: 'post',
                                dataType: 'json',
                                data: {id: data.id, stepcode: value},
                                success: function (res) {
                                    table.reload('mobileSearch');
                                    if (res.type == 'error') {
                                        layer.msg(res.message);
                                    } else {
                                        obj.del();
                                        layer.close(index);
                                    }
                                }
                            });


                        });

                    });
                }
                if (obj.event === 'edit_adress') {
                    layer.prompt({
                        formType: 3,
                        title: '请输入提币地址'
                        , placeholder: '请输入提币地址'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        layer.confirm('确定修改?', function (index) {
                            $.ajax({
                                url: '{{url('admin/edit_adress')}}',
                                type: 'post',
                                dataType: 'json',
                                data: {id: data.id, adress: value},
                                success: function (res) {
                                    table.reload('mobileSearch');
                                    if (res.type == 'error') {
                                        layer.msg(res.message);
                                    } else {
                                        layer.close(index);
                                    }
                                }
                            });


                        });

                    });
                }
            });
            form.on('submit(mobile_search)', function (data) {
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