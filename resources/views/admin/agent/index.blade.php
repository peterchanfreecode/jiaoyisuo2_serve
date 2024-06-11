@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <button class="layui-btn layui-btn-normal layui-btn-radius" id="add_set">新增</button>
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
                , url: '{{url('admin/agent/lists')}}' //数据接口
                , page: true //开启分页
                , height: 'full-100'
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 80, sort: true}
                    , {field: 'agent_id', title: '代理id'}
                    , {field: 'level', title: '代理级别'}
                    , {field: 'username', title: '相关用户账号' }
                    , {field: 'agent_domain', title: '邀请域名', width: 300}
                    , {field: 'agent_kefu', title: '客服链接', width: 300}
                    , {title: '操作', minWidth: 150, toolbar: '#barDemo'}
                ]]
            });
            $('#add_set').click(function() {
                layer_show('新增', '{{url('admin/agent/add')}}', 600, 400);
            });

            table.on('tool(test)', function (obj) {
                var data = obj.data;
                if(obj.event === 'del'){
                    layer.confirm('真的要删除吗？', function(index){
                        $.ajax({
                            url:'{{url('admin/agent/del')}}',
                            type:'post',
                            dataType:'json',
                            data:{id:data.id},
                            success:function (res) {
                                if(res.type == 'error'){
                                    layer.msg(res.message);
                                }else{
                                    obj.del();
                                    layer.close(index);
                                }
                            }
                        });


                    });
                }
                if (obj.event === 'edit') {
                    layer_show('编辑', '{{url('admin/agent/add')}}?id=' + data.id, 600, 400);
                }
            });


        });
    </script>

@endsection