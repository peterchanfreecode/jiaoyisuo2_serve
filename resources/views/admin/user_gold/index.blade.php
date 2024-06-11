@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')

    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <form class="layui-form layui-form-pane layui-inline" action="">
            <div class="layui-inline" style="margin-left: 10px;">
                <label>用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" placeholder="用户ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
        </form>
    </div>
    <table id="demo" lay-filter="test"></table>

@endsection

@section('scripts')
    <script type="text/html" id="status_t">
        <a class="layui-btn layui-btn-xs @{{ d.status == 1 ? 'layui-btn-danger' : 'layui-btn-primary' }} ">@{{ d.status
            == 1 ? '未扣除' : '已扣除' }}</a>
    </script>
    <script>

        layui.use(['table','form'], function(){
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例
            table.render({
                elem: '#demo'
                ,url: '{{url('admin/user_gold/list_data')}}' //数据接口
                ,page: true //开启分页
                ,id:'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', Width:50, sort: true}
                    ,{field: 'account', title: '用户', minWidth:50}
                    ,{field: 'user_id', title: '用户ID', minWidth:50}
                    ,{field: 'amount', title: '金额', Width:150}
                    , {field: 'status', title: '状态', width: 90, templet: "#status_t"}
                    ,{field: 'create_time', title: '创建时间', minWidth:150}

                ]]
                , height: 'full-150'
                , text: {none: '未查询到数据^_^'}
            });


            //监听提交
            form.on('submit(mobile_search)', function(data){
                var user_id = data.field.user_id;
                table.reload('mobileSearch',{
                    where:{user_id:user_id},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection