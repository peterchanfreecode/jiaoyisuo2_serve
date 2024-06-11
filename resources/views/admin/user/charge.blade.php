@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <style>
        .layui-table-cell {
            height: auto;
            text-overflow: inherit;
            white-space: normal;
            word-break: break-all;
        }
    </style>
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <form class="layui-form layui-form-pane layui-inline" action="">

            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="account_number" id="account_number" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">入金类型</label>
                <div class="layui-input-inline">
                    <select name="is_real" id="is_real" >
                        <option value="">全部</option>
                        <option value="1">真实入金</option>
                        <option value="2">代充</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="uid" id="uid" placeholder="用户ID" autocomplete="off" class="layui-input"
                           value="">
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
                    <button class="layui-btn" id="btn-search" lay-submit lay-filter="btn-search"><i
                                class="layui-icon layui-icon-search"></i></button>

                </div>
            </div>
        </form>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal">总入金(USDT): <span style="color:brown">{{$total_money}}</span>
        </button>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal">BTC总充值: <span style="color:brown">{{$btc_money}}</span></button>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal">ETH总充值: <span style="color:brown">{{$eth_money}}</span></button>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal ">USDT总充值: <span style="color:brown">{{$usdt_money}}</span>
        </button>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal ">USDC总充值: <span style="color:brown">{{$usdc_money}}</span>
        </button>
    </div>
    <div class="layui-inline" style="margin-top: 10px;">
        <button class="layui-btn layui-btn-normal" id="csv">导出数据</button>
    </div>
    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_recommend" value="@{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="sexDemo" @{{ d.is_recommend== 1 ? 'checked' : '' }}>
    </script>

    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="statustml">
        @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'申请充值'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'充值完成'+'</span>' : '' }}
        @{{d.status==3 ? '<span class="layui-badge layui-bg-black">'+'申请失败'+'</span>' : '' }}

    </script>
    <script type="text/html" id="realhml">
        @{{d.is_real==1 ? '<span class="layui-badge layui-bg-green">'+'真实入金'+'</span>' : '' }}
        @{{d.is_real==2 ? '<span class="layui-badge layui-bg-red">'+'试玩/代充'+'</span>' : '' }}


    </script>
    <script type="text/html" id="ophtml">
        @{{d.status==1 ? '
        <button class="layui-btn layui-btn-normal layui-btn-xs" onclick="pass('+d.id+')">通过</button>
        <button class="layui-btn layui-btn-danger layui-btn-xs" onclick="refuse('+d.id+')" data-id='+d.id+'
                class="btn-refuse">拒绝
        </button>' : '' }}
    </script>
@endsection

@section('scripts')
    <script>

        layui.use(['element', 'form', 'layer', 'table', 'laydate'], function () {
            var element = layui.element;
            var layer = layui.layer;
            var table = layui.table;
            var $ = layui.$;
            var form = layui.form;
            var laydate = layui.laydate;

            laydate.render({
                elem: '#start_time'
            });
            laydate.render({
                elem: '#end_time'
            });
            form.on('submit(btn-search)', function (data) {
                var option = {
                    where: data.field,
                    page: {curr: 1}
                }
                data_table.reload(option);
                return false;
            });
            //第一个实例
            var data_table = table.render({
                elem: '#demo'
                , url: "{{url('admin/user/charge_list')}}" //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: '充币ID', width: 80, sort: true}
                    , {field: 'uid', title: '用户ID', width: 80}
                    , {field: 'belong_agent_name', title: '代理', width: 150}
                    , {field: 'account_number', title: '用户名', width: 150}
                    , {field: 'name', title: '虚拟币', width: 80}
                    , {field: 'amount', title: '数量', minWidth: 80},
                    {
                        field: "charge_url", align: 'center', title: "充值截图", templet: function (item) {
                            return '<img onclick="showimg(this)" src="' + item.charge_url + '">';
                        }
                    }
                    , {field: 'status', title: '交易状态', minWidth: 100, templet: '#statustml'}
                    , {field: 'created_at', title: '充币时间', minWidth: 180}
                    , {field: 'is_real', title: '入金类型', minWidth: 100, templet: '#realhml'}
                    , {field: 'remark', title: '备注', minWidth: 180}
                    , {title: '操作', minWidth: 120, templet: '#ophtml'}

                ]]
            });
        })
        $('#csv').click(function () {
            var uid = $("#uid").val();
            var account_number = $("#account_number").val();
            var is_real = $("#is_real").val();
            var start_time = $("#start_time").val();
            var end_time = $("#end_time").val();
            var str = "uid="+uid+"&account_number="+account_number+"&is_real="+is_real+"&start_time="+start_time+"&end_time="+end_time;
            window.location.href='{{url('/admin/user/charge_csv')}}'+"?"+str;

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
            layer_show('确定通过', '{{url('admin/user/req_show')}}?id=' + id, 500, 300);
        }

        function refuse(id) {
            layer.prompt({
                formType: 3,
                title: '请输入拒绝原因'
                , placeholder: '请输入拒绝原因'
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
                    url: '{{url('admin/user/refuse_req')}}',
                    type: 'post',
                    dataType: 'json',
                    data: {id: id, remark: value},
                    success: function (res) {
                        if (res.type == 'ok') {
                            layer.msg(res.message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1200)
                        }
                    }
                })
                return true;
            });

        }

    </script>

@endsection