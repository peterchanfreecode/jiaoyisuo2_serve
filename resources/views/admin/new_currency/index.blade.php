@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <button class="layui-btn layui-btn-normal layui-btn-radius" id="add_set">添加项目</button>


    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="order">查看订单</a>
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
    <script type="text/html" id="statustml">
        @{{d.status==0 ? '<span class="layui-badge layui-bg-green">'+'待开始'+'</span>' : '' }}
        @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'进行中'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'已结束'+'</span>' : '' }}
    </script>
    <script type="text/html" id="fanwei">
        @{{d.min}}-@{{d.max}}
    </script>
    <script type="text/html" id="show">
        @{{d.show_start}}~@{{d.show_end}}
    </script>
    <script type="text/html" id="raise">
        @{{d.raise_start}}~@{{d.raise_end}}
    </script>
    <script type="text/html" id="lock">
        @{{d.lock_start}}~@{{d.lock_end}}
    </script>
    <script>

        layui.use(['table','form'], function() {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例

            $('#add_set').click(function() {
                layer_show('添加项目', '/admin/new_currency/add');
            });

            table.render({
                elem: '#demo'
                ,url: '{{url('admin/new_currency/lists')}}' //数据接口
                ,page: true //开启分页
                ,height: 'full-100'
                ,id: 'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', width:40, sort: true}
                    ,{field: 'title', title: '活动名称', width: 100}
                    ,{field: 'currency_name', title: '认购币种', width: 100}
                    ,{field: 'price', title: '发行价', width: 80}
                    ,{field: 'amount', title: '发行量', width: 100}
                    ,{field: 'start_at', title: '发行时间', width: 120}
                    ,{field: 'market_price', title: '市场价', width: 100}
                    ,{field: 'min', title: '可申购范围', width: 150, templet: '#fanwei'}
                    ,{field: 'progress', title: '剩余进度%', width: 100}
                    ,{field: 'status', title: '状态', width: 80, templet: '#statustml'}
                    ,{field: 'show_start', title: '公告时间', width: 200, templet: '#show'}
                    ,{field: 'raise_start', title: '筹集时间', width: 200, templet: '#raise'}
                    ,{field: 'lock_start', title: '锁仓时间', width: 200, templet: '#lock'}
                    ,{title:'操作', minWidth:150, toolbar: '#barDemo'}
                ]]
            });

            table.on('tool(test)', function(obj){
                var data = obj.data;
                if(obj.event === 'edit'){
                    layer_show('编辑','{{url('admin/new_currency/add')}}?id='+data.id);
                } else if (obj.event === 'del') {
                    layer.confirm('真的要删除吗？', function(index){
                        $.ajax({
                            url:'{{url('admin/new_currency/del')}}',
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
                } else {
                    layer_show('查看订单','{{url('admin/new_currency/order')}}?id='+data.id, 1500);
                }
            });

        });
    </script>

@endsection