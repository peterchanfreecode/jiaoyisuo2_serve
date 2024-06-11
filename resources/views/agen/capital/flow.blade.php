@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div class="layui-inline">
        <div class="layui-inline" style="margin-left: 50px;">
            <label>log type&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="type" id="type" class="layui-input">
                    <option value="">all types</option>
                    @foreach ($types as $key=>$type)
                        <option value="{{ $key }}" class="ww">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-inline" style="margin-left: 50px;">
            <label>currency type&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="currency_type" id="currency_type" class="layui-input">
                    <option value="">all</option>
                    @foreach ($currency_type as $key=>$type)
                        <option value="{{ $type['id'] }}" class="ww">{{ $type['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-inline" style="margin-left: 50px;">
            <label>Plus or minus&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="sign" id="sign" class="layui-input">
                    <option value="0">all values</option>
                    <option value="1" class="ww">positive value</option>
                    <option value="-1" class="ww">negative value</option>
                </select>
            </div>
        </div>
        <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
    </div>

    <div class="layui-form">
        <table id="accountlist" lay-filter="accountlist">
            <input type="hidden" name="user_id" value="{{$user_id}}">
        </table>
        @endsection

        @section('scripts')
            <script>

                window.onload = function() {
                    document.onkeydown=function(event){
                        var e = event || window.event || arguments.callee.caller.arguments[0];
                        if(e && e.keyCode==13){ // enter 键
                            $('#mobile_search').click();
                        }
                    };
                    layui.use(['element', 'form', 'layer', 'table','laydate'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;
                        var laydate = layui.laydate;

                        laydate.render({
                            elem: '#start_time'
                        });
                        laydate.render({
                            elem: '#end_time'
                        });

                        form.on('submit(mobile_search)',function(obj){
                            var currency_type =  $("#currency_type").val()
                            var type = $('#type').val()
                            var sign = $('#sign').val()
                            var user_id = $("input[name='user_id']").val();
                            tbRend("{{url('agen/user/flow_lists')}}?type="+type
                                +'&currency_type='+currency_type
                                +'&sign='+sign
                                +'&user_id=' + user_id
                            );
                            return false;
                        });
                        function tbRend(url) {
                            table.render({
                                elem: '#accountlist'
                                ,url: url
                                ,page: true
                                ,limit: 20
                                ,height: 'full-100'
                                ,toolbar: true
                                ,cols: [[
                                   {field:'user_id',title: 'User ID',width: 130}
                                    ,{field:'before',title:'before change', width:150}
                                    ,{field:'value',title:'Variation', minWidth:160}
                                    ,{field:'after',title:'After the change', width:150}
                                    ,{field:'transaction_info',title:'Trading Information', width:100}
                                    ,{field:'currency_name',title:'Currency', width:100}
                                    ,{field:'info',title:'Record', minWidth:300}
                                    ,{field:'created_time',title:'creation time', width:170}
                                ]]
                                ,parseData: function(res){ //res 即为原始返回的数据
                                    $('#statistics').html(res.sum);
                                }
                            });
                        }
                        var user_id = $("input[name='user_id']").val();
                        tbRend("{{url('agen/user/flow_lists')}}?user_id=" + user_id);
                        //监听工具条
                        table.on('tool(accountlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;
                        });
                    });
                }
            </script>
@endsection