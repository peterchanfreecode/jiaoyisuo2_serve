@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <table id="demo" lay-filter="test"></table>
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
                , url: '{{url('admin/exception/list')}}' //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                , cols: [[ //表头
                    {field: 'id', title: 'ID', Width: 50, sort: true}
                    , {field: 'title', title: '标题'}
                    , {field: 'type', title: '类型'}
                    , {field: 'message', title: '内容'}
                    , {field: 'created_at', title: '添加时间'}

                ]]
            });
            table.on('tool(test)', function (obj) {
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