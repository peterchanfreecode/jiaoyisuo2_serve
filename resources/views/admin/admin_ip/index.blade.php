@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <button class="layui-btn layui-btn-normal layui-btn-radius"
                onclick="layer_show('添加白名单','{{url('admin/admin_ip/add')}}')">添加白名单
        </button>
        <form class="layui-form layui-form-pane layui-inline" action="">

            <div class="layui-inline" style="margin-left: 50px;">
                <label class="layui-form-label">IP</label>
                <div class="layui-input-inline">
                    <input type="text" name="ip" autocomplete="off" class="layui-input">
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
    <table id="demo" lay-filter="demo"></table>
    <script type="text/html" id="barDemo">
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
                , url: '{{url('admin/admin_ip/list')}}' //数据接口
                ,height: 'full-160'
                ,page: true
                , id: 'demo'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', minWidth: 80, sort: true}
                    , {field: 'ip', title: '白名单ip'}
                    , {field: 'created_at', title: '添加时间'}
                    , {title: '操作', minWidth: 100, toolbar: '#barDemo'}

                ]]
            });


            table.on('tool(demo)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    layer.confirm('真的删除行么', function (index) {
                        $.ajax({
                            url: '{{url('admin/admin_ip/del')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: data.id},
                            success: function (res) {
                                layer.msg(res.message);
                                table.reload('demo');
                            }
                        });
                    });
                }
            });

            //监听提交
            form.on('submit(mobile_search)', function (data) {
                var ip = data.field.ip;
                table.reload('demo', {
                    where: {ip: ip},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection