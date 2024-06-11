@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')

    <div class="layui-inline">
        <label class="layui-form-label">用户帐号</label>
        <div class="layui-input-inline">
            <input type="text" name="account" placeholder="用户手机号或邮箱" autocomplete="off" class="layui-input" value="">
        </div>
        <div class="layui-inline" style="margin-left: 10px;">
            <label>用户ID</label>
            <div class="layui-input-inline">
                <input type="text" name="user_id" placeholder="用户ID" autocomplete="off" class="layui-input" value="">
            </div>
        </div>
        <div class="layui-inline" style="margin-left: 10px;">
            <label>审核状态</label>
            <div class="layui-input-inline" style="width: 90px">
                <select name="review_status_s" id="review_status_s" class="layui-input">
                    <option value="-1">全部</option>
                    <option value="0">待审核</option>
                    <option value="1">未审核</option>
                    <option value="2">已审核</option>
                </select>
            </div>
        </div>
        <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
    </div>
    
        <table id="userlist" lay-filter="userlist"></table>

        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="detail">查看</a>
            @{{d.status==0 ? ' <a class="layui-btn layui-btn layui-btn-xs" lay-event="auth_suc">'+'通过'+'</a>': '' }}
            @{{d.status==0 ? ' <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="auth_fail">'+'拒绝'+'</a>': '' }}
        </script>
      
        <script type="text/html" id="switchTpl">
        <input type="checkbox" name="high_status" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="sexDemo" @{{ d.high_status == 2 ? 'checked' : '' }} >
      </script>
    <script type="text/html" id="switchTpl2">
        @{{d.status==0 ? '<span class="layui-badge layui-bg-yellow">'+'待审核'+'</span>' : '' }}
        @{{d.status==1 ? '<span class="layui-badge layui-bg-red">'+'审核不通过'+'</span>' : '' }}
        @{{d.status==2 ? '<span class="layui-badge layui-bg-green">'+'审核通过'+'</span>' : '' }}
    </script>

    <script type="text/html" id="switchTpl3">
        <img src="@{{d.front_pic}}"   lay-event="showimg" width="80px;" />
    </script>

    <script type="text/html" id="switchTpl4">
        <img src="@{{d.reverse_pic}}" lay-event="showimg1"  width="80px;" />
    </script>

    <script type="text/html" id="switchTpl5">
        <img src="@{{d.hand_pic}}" lay-event="showimg2"  width="80px;" />
    </script>
    <style>
        .layui-table-cell{height:auto;}
    </style>
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
                    layui.use(['element', 'form', 'layer', 'table'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;


                        form.on('submit(mobile_search)',function(obj){
                            var account =  $("input[name='account']").val();
                            var review_status_s =  $("select[name='review_status_s']").val();
                            var user_id = $("input[name='user_id']").val();
                            tbRend("{{url('/admin/user2/real_list')}}?account=" + account + "&review_status_s=" + review_status_s+ "&user_id=" +user_id);
                            return false;
                        });
                        function tbRend(url) {
                            table.render({
                                elem: '#userlist'
                                , url: url
                                , page: true
                                ,limit: 20
                                , cols: [[
                                    { field: 'id', title: '认证ID'}
                                    , {field:'account',title: '用户账号'}
                                    , {field: 'user_id', title: '用户ID'}
                                    , {field:'name',title: '真实姓名'}
                                    , {field:'hand_pic',title: '手持',width: 120, templet: '#switchTpl5'}
                                    ,{field:'high_status', title:'状态', width:120, templet: '#switchTpl2'}
                                    , {field:'create_time',title:'申请时间'}
                                    , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#barDemo'}
                                ]]
                            });
                        }
                        tbRend("{{url('/admin/user2/real_list')}}");

                        //监听审核操作
                        form.on('switch(sexDemo)', function(obj){
                            var id = this.value;
                            
                            $.ajax({
                                url:'{{url('admin/user2/real_auth')}}',
                                type:'post',
                                dataType:'json',
                                data:{id:id},
                                success:function (res) {
                                    layer.msg(res.message);
                                    var account =  $("input[name='account']").val();
                                    var review_status_s =  $("select[name='review_status_s']").val();
                                    var user_id = $("input[name='user_id']").val();
                                    tbRend("{{url('/admin/user2/real_list')}}?account=" + account + "&review_status_s=" + review_status_s+ "&user_id=" +user_id);
                                }
                            });
                        });

                        //监听工具条
                        table.on('tool(userlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;
                            if (layEvent === 'delete') { //删除
                                layer.confirm('真的要删除吗？', function (index) {
                                    //向服务端发送删除指令
                                    $.ajax({
                                        url: "{{url('admin/user2/real_del')}}",
                                        type: 'post',
                                        dataType: 'json',
                                        data: {id: data.id},
                                        success: function (res) {
                                            if (res.type == 'ok') {
                                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                                layer.close(index);
                                            } else {
                                                layer.close(index);
                                                layer.alert(res.message);
                                            }
                                        }
                                    });
                                });
                            }else if (layEvent === 'detail'){ //详情
                                layer_show('认证详情','{{url('admin/user2/real_info')}}?id='+data.id);
                            }
                            if (obj.event === 'auth_suc') {
                                layer.confirm('确定通过?', function (index) {
                                    $.ajax({
                                        url: '{{url('admin/user2/real_auth')}}',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {id: data.id, action: 'suc'},
                                        success: function (res) {
                                            layer.msg(res.message);
                                            var account = $("input[name='account']").val();
                                            var review_status_s = $("select[name='review_status_s']").val();
                                            var user_id = $("input[name='user_id']").val();
                                            tbRend("{{url('/admin/user2/real_list')}}?account=" + account + "&review_status_s=" + review_status_s + "&user_id=" + user_id);
                                        }
                                    });

                                });

                            }
                            if (obj.event === 'auth_fail') {
                                layer.confirm('确定拒绝?', function (index) {
                                    $.ajax({
                                        url: '{{url('admin/user2/real_auth')}}',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {id: data.id, action: 'fail'},
                                        success: function (res) {
                                            layer.msg(res.message);
                                            var account = $("input[name='account']").val();
                                            var review_status_s = $("select[name='review_status_s']").val();
                                            var user_id = $("input[name='user_id']").val();
                                            tbRend("{{url('/admin/user2/real_list')}}?account=" + account + "&review_status_s=" + review_status_s + "&user_id=" + user_id);
                                        }
                                    });
                                });
                            }
                            if(obj.event === 'showimg'){
                                layer.open({
                                    type: 1,
                                    title: false,
                                    closeBtn: 0,
                                    skin: 'layui-layer-nobg', //没有背景色
                                    shadeClose: true,
                                    content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + data.front_pic+ '">'
                                });
                            }
                            if(obj.event === 'showimg1'){
                                layer.open({
                                    type: 1,
                                    title: false,
                                    closeBtn: 0,
                                    skin: 'layui-layer-nobg', //没有背景色
                                    shadeClose: true,
                                    content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + data.reverse_pic+ '">'
                                });
                            }
                            if(obj.event === 'showimg2'){
                                layer.open({
                                    type: 1,
                                    title: false,
                                    closeBtn: 0,
                                    skin: 'layui-layer-nobg', //没有背景色
                                    shadeClose: true,
                                    content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + data.hand_pic+ '">'
                                });
                            }
                        });
                    });
                }
            </script>

@endsection