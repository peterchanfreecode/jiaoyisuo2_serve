@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <button class="layui-btn layui-btn-normal layui-btn-radius" id="add_set">添加项目</button>


    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

@endsection

@section('scripts')
    <script type="text/html" id="status">
        <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="status" @{{ d.status == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="auto_claim">
        <input type="checkbox" name="auto_claim" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="auto_claim" @{{ d.auto_claim == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="is_t_add_1_t">
        <input type="checkbox" name="is_t_add_1" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="is_t_add_1" @{{ d.is_t_add_1 == 1 ? 'checked' : '' }}>
    </script>
    <script>

        layui.use(['table','form'], function() {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例

            $('#add_set').click(function() {
                layer_show('添加项目', '{{url('admin/deposit/add')}}', 600, 400);
            });

            table.render({
                elem: '#demo'
                ,url: '{{url('admin/deposit/lists')}}' //数据接口
                ,page: true //开启分页
                ,height: 'full-100'
                ,id: 'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', width:80, sort: true}
                    ,{field: 'currency_name', title: '交易币', width: 100}
                    ,{field: 'day', title: '期限', width: 100}
                    ,{field: 'rate', title: '利息（%）', width: 100}
                    ,{field: 'save_min', title: '最少存币数', minWidth: 100}
                    ,{field: 'cancel_rate', title: '违约结算比例(%)', width: 150}
                    ,{field: 'level_zh', title: '等级中文', width: 150}
                    ,{field: 'level_en', title: '等级英文', width: 150}
                    ,{field: 'level_jp', title: '等级日文', width: 150}
                    ,{field: 'level_kr', title: '等级韩文', width: 150}
                    ,{field: 'level_de', title: '等级德文', width: 150}
                    ,{field: 'level_fra', title: '等级法文', width: 150}
                    ,{field: 'level_th', title: '等级泰语', width: 150}
                    ,{field: 'level_vi', title: '等级越南语', width: 150}
                    ,{field: 'level_hk', title: '等级繁体', width: 150}
                    ,{field: 'created_at', title: '创建时间', minWidth: 100}
                    ,{title:'操作', minWidth:150, toolbar: '#barDemo'}
                ]]
            });
            table.on('tool(test)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    layer.confirm('真的要删除吗？', function(index){
                        $.ajax({
                            url:'{{url('admin/deposit/del')}}',
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
                } else if(obj.event === 'edit'){
                    layer_show('编辑','{{url('admin/deposit/add')}}?id='+data.id, 600, 400);
                }
            });


        });
    </script>

@endsection