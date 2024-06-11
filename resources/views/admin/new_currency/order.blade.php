@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <style>
        .layui-table-cell {
            height: auto;
            text-overflow:inherit;
            white-space:normal;
            word-break: break-all;
        }
    </style>
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">


        <form class="layui-form layui-form-pane layui-inline" action="">
            <input type="hidden" name="id" value="{{$id}}" />
            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="account_number" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="uid"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 50px;">
                <label>审核状态</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="-1" >全部</option>
                        <option value="0">待审核</option>
                        <option value="1">已拒绝</option>
                        <option value="2">审核通过</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i class="layui-icon">&#xe615;</i></button>
                </div>
            </div>


        </form>

    </div>
    <table id="demo" lay-filter="yes"></table>
    <table id="demo" lay-filter="no"></table>
    <script type="text/html" id="barDemo">
        @{{d.status==0 ? '
        <a class="layui-btn layui-btn-green layui-btn-xs" onclick="pass('+d.id+')">审核</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" onclick="refuse('+d.id+')">拒绝</a>' : '' }}
        @{{d.status==1 ? '<span class="layui-badge layui-bg-red">'+'已拒绝'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-green">'+'审核通过'+'</span>' : '' }}
    </script>

@endsection

@section('scripts')
    <script>

        layui.use(['table', 'form'], function () {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例
            table.render({
                elem: '#demo'
                , url: "{{url('admin/new_currency/getOrder')}}"+"?id={{$id}}" //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 60, sort: true}
                    ,{field: 'uid', title: '会员ID', width: 100, sort: true}
                    , {field: 'title', title: '活动名称', width: 150}
                    , {field: 'apply_amount', title: '申购币种数量', width: 150}
                    , {field: 'coin_amount', title: '总价（USDT）', width: 100}
                    , {field: 'get_coin_amount', title: '中标数量(USDT)', width: 120}
                    , {field: 'get_apply_amount', title: '中标数量(申购币种)', width: 120}
                    , {field: 'currency_name', title: '申购币种', width: 80}
                    , {field: 'pay_currency_name', title: '结算币种', width: 80}
                    , {field: 'rate', title: '中标率%', width: 80}
                    , {field: 'desc', title: '备注', width: 100}
                    , {field: 'created_at', title: '申购时间', width: 180}
                    , {title: '操作', minWidth: 120, templet: '#barDemo'}
                ]]
                , height: 'full-150'
                , text: {none: '未查询到数据^_^'}
            });

            //监听提交
            form.on('submit(mobile_search)', function(data){
                var account_number = data.field.account_number;
                var status = data.field.status;
                var uid = data.field.uid;
                table.reload('mobileSearch',{
                    where:{account_number:account_number,status:status,uid:uid},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

            table.on('tool(yes)', function(obj){
                var data = obj.data;
                layer_show('编辑11','{{url('admin/deposit_order/edit')}}?id='+data.id,600,400);
            });
        })
        function showimg(t) {
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                area: '800px',
                skin: 'layui-layer-nobg', //没有背景色
                shadeClose: true,
                content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + t.src + '">'
            });
        }

        function pass(id) {
            layer_show('编辑','{{url('admin/new_currency/passView')}}?id='+id,600,500);
        }

        function refuse(id) {
            $.ajax({
                url: '{{url('admin/new_currency/refuse')}}',
                type: 'post',
                dataType: 'json',
                data: {id: id},
                success: function (res) {
                    if (res.type == 'error') {
                        layer.msg('操作成功');
                        parent.layer.close(index);
                        parent.window.location.reload();
                    } else {
                        layer.msg(res.message);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1200)
                    }
                }
            })
        }

    </script>

@endsection