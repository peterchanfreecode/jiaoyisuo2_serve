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
    <div class="layui-form">
        <div class="layui-item">
            <div class="layui-inline" style="margin-left: 10px;">
                <label>邀请人ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="id" placeholder="邀请人ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>被邀请人ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="son_id" placeholder="被邀请人ID" autocomplete="off" class="layui-input"
                           value="">
                </div>
            </div>
            <div class="layui-btn-group">
                <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"><i class="layui-icon layui-icon-search"></i></button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </div>
    <table id="userlist" lay-filter="userlist"></table>
@endsection

@section('scripts')
    <script type="text/html" id="is_charge">
        <a class="layui-btn layui-btn-xs @{{ d.is_charge == 1 ? 'layui-btn-danger' : 'layui-btn-primary' }} ">@{{ d.is_charge
            == 1 ? '有效' : '无效' }}</a>  </script>

    <script>
        layui.use(['element', 'form', 'layer', 'table'], function () {
            var element = layui.element
                , layer = layui.layer
                , table = layui.table
                , $ = layui.$
                , form = layui.form
            var user_table = table.render({
                elem: '#userlist'
                , toolbar: true
                , url: '/admin/invite/getTree'
                , page: true
                , limit: 100
                , limits: [20, 50, 100, 200, 500, 1000]
                , height: 'full-60'
                , cols: [[
                    {field: '', type: 'checkbox'}
                    , {field: 'id', title: '被邀请人ID'}
                    , {field: 'parent_id', title: '关联邀请人ID'}
                    , {field: 'time', title: '被邀请人注册时间'}
                    , {field: 'first_consumption_time', title: '被邀请人首次消费时间'}
                    ,{title:'被邀请人是否为有效被邀请状态', toolbar: '#is_charge'}
                ]]
            });

            form.on('submit(mobile_search)', function (obj) {
                user_table.reload({
                    where: obj.field
                });
                return false;
            });;

            //监听工具条
            table.on('tool(userlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data;
                var layEvent = obj.event;
                var tr = obj.tr;
            });
        });
    </script>
@endsection