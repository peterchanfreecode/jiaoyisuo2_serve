..@extends('agent.layadmin')

@section('page-head')

@endsection

@section('page-content')
    <!-- <div class="layui-form"> -->
    <table id="userlist" lay-filter="userlist">
        <input type="hidden" name="user_id" value="{{$user_id}}">
    </table>
    <script type="text/html" id="barDemo">
        @if($level <= 1 )
     {{--   <a class="layui-btn layui-btn-xs" lay-event="conf">调节账户</a>--}}
        @endif
    </script>
@endsection

@section('scripts')
    <script>
        window.onload = function () {

            layui.use(['element', 'form', 'layer', 'table'], function () {
                var element = layui.element;
                var layer = layui.layer;
                var table = layui.table;
                var $ = layui.$;
                var form = layui.form;

                function tbRend(url) {
                    table.render({
                        elem: '#userlist'
                        , url: url
                        , page: true
                        , limit: 30
                        , toolbar: true
                        , totalRow: true
                        , height: 'full-100'
                        , cols: [[
                            {field: 'id', title: '钱包id'}
                            , {field: 'currency_name', title: '币种', totalRowText: '小计'}
                            , {field: '_ru', title: '入金', totalRow: true}
                            , {field: '_chu', title: '出金', totalRow: true}
                            , {field: 'change_balance', title: '账户金额'}
                            , {field: 'lock_change_balance', title: '冻结金额'}
                            , {fixed: 'right', title: '操作', align: 'center', toolbar: '#barDemo'}
                        ]]
                    });
                }

                var user_id = $("input[name='user_id']").val();
                tbRend("{{url('/agent/users_wallet_total')}}?user_id=" + user_id);
                //监听工具条
                table.on('tool(userlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data;
                    var layEvent = obj.event;
                    var tr = obj.tr;
                    if (layEvent === 'conf') {
                        var index = layer.open({
                            title: '调节账户'
                            , type: 2
                            , content: '{{url('/agent/user/conf')}}?id=' + data.id
                            , maxmin: true
                        });
                        layer.full(index);
                    }
                });
            });
        }
    </script>

@endsection