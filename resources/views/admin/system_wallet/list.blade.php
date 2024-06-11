@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <button class="layui-btn layui-btn-normal layui-btn-radius"
                onclick="layer_show('添加地址','{{url('admin/system_wallet/add')}}',800,600)">添加地址
        </button>
    </div>
    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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
                , url: '{{url('admin/system_wallet/list_data')}}' //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', Width: 50, sort: true}
                    , {field: 'currency_name', title: '币种', Width: 150}
                    , {field: 'type', title: '类型', minWidth: 100}
                    , {field: 'address', title: '地址', minWidth: 250}
                    , {field: 'operator_name', title: '操作者', minWidth: 50}
                    , {field: 'operator_time', title: '操作时间', minWidth: 150}
                    , {field: 'create_at', title: '添加时间', minWidth: 150}
                    , {title: '操作', toolbar: '#barDemo'}

                ]]
            });


            table.on('tool(test)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    layer.prompt({
                        formType: 3,
                        title: '请输入谷歌安全码'
                        ,placeholder: '请输入谷歌安全码'
                        ,btn: ['确定', '取消']
                        ,btn2: function(index){
                            layer.close(index);
                        }
                        ,cancel: function(index){
                            layer.close(index);
                        }
                    },function(value, index){
                        layer.close(index);
                        layer.confirm('真的删除行么', function (index) {
                            $.ajax({
                                url: '{{url('admin/system_wallet/delete')}}',
                                type: 'post',
                                dataType: 'json',
                                data: {id: data.id,stepcode:value},
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

                    });

                } else if (obj.event === 'edit') {
                    layer_show('编辑地址', '{{url('admin/system_wallet/edit')}}?id=' + data.id, 800, 600);
                }
            });

            //监听提交
            form.on('submit(mobile_search)', function (data) {
                var account_number = data.field.account_number;
                table.reload('mobileSearch', {
                    where: {account_number: account_number},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection