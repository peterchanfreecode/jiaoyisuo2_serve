@extends('agent.layadmin')

@section('page-head')
    <style>
        .layui-table-cell {
            height: auto;
        }
    </style>
@endsection

@section('page-content')

    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="please enter" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">account</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" placeholder="please enter" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">Is locked</label>
                        <div class="layui-input-block">
                            <select name="is_lock">
                                <option value="2">unlimited</option>
                                <option value="1">locking</option>
                                <option value="0">not locked</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">pull new</label>
                        <div class="layui-input-block">
                            <select name="is_addson">
                                <option value="2">unlimited</option>
                                <option value="1">allow</option>
                                <option value="0">prohibit</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-useradmin" lay-submit
                                lay-filter="LAY-user-front-search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="layui-card-body">
                <div style="padding-bottom: 10px;">
                    <button class="layui-btn layuiadmin-btn-useradmin" data-type="add">Add subordinate agents</button>
                    <button id="back_show_name" class=" layui-btn-normal layui-btn layuiadmin-btn-useradmin" lay-submit
                            lay-filter="back_parents" style="font-size: 15px">
                        back to top
                    </button>
                </div>

                <table id="LAY-user-manage" lay-filter="LAY-user-manage"></table>

                <script type="text/html" id="table-useradmin-webuser">

                    @{{#  if(d.level <3 ){ }}
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="getsons"><i
                                class="layui-icon"></i>View subordinates</a>
                    @{{#  } }}
                    @if($is_admin== 1 )
                        @{{#  if(d.level >1 ){ }}
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="update_agent"><i
                                    class="layui-icon"></i>replace superior</a>
                        @{{#  } }}
                    @endif
                    @if($is_admin== 1 )

                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="del_agent"><i
                                class="layui-icon"></i>remove agent</a>
                    @endif
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="seach_parent"><i
                                class="layui-icon"></i>back level</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>edit</a><br/>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="addsonagent"><i
                                class="layui-icon layui-icon-add-1"></i>add subordinates</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="this-sons"><i
                                class="layui-icon layui-icon-group"></i>View members</a>

                </script>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/html" id="lockTpl">
        @{{#  if(d.is_lock == 1){ }}
        <i class="layui-icon layui-icon-ok" style="font-size: 21px; color: green;" lay-event="lock"></i>
        @{{#  } else { }}
        <i class="layui-icon layui-icon-close" style="font-size: 21px; color: red;" lay-event="lock"></i>
        @{{#  } }}
    </script>
    <script type="text/html" id="addsonTpl">
        @{{# if(d.is_addson == 1){ }}
        <i class="layui-icon layui-icon-ok" style="font-size: 21px; color: green;" lay-event="addson"></i>
        @{{#  } else { }}
        <i class="layui-icon layui-icon-close" style="font-size: 21px; color: red;" lay-event="addson"></i>
        @{{#  } }}
    </script>

    <script>
        var parents = [];
        var id = "<?php echo $id?>";
        layui.use(['index',  'table', 'layer'], function () {
            // console.log(layui.setter.base)
            var $ = layui.$
                , admin = layui.admin
                , view = layui.view
                , table = layui.table
                , form = layui.form;

            form.render(null, 'layadmin-userfront-formlist');
            //代理商管理
            table.render({
                elem: '#LAY-user-manage'
                ,url: '/agen/lists' //模拟接口
                ,cols: [[
                    {type: 'checkbox'}
                    ,{field: 'id', width: 120, title: 'Agent ID', sort: true }
                    ,{field: 'user_id', width: 120, title: 'User ID', sort: true }
                    ,{field: 'username', title: 'agent account', minWidth: 180 , event : "getsons",style:"color: #fff;background-color: #5FB878;"}
                    ,{field: 'parent_agent_name', title: 'superior agent', width: 180}
                    ,{field: 'agent_name_en', title: 'grade',width: 120}
                    ,{field: 'is_lock', title: 'Is it locked', width:120,templet: '#lockTpl'}
                    ,{field: 'is_addson', title: 'Whether to pull new', width:150, templet: '#addsonTpl'}
                    ,{field: 'reg_time', title: 'Join time', sort: true, width: 170}
                    ,{title: 'operate', minWidth: 600, align:'center',  toolbar: '#table-useradmin-webuser'}
                ]]
                ,page: true
                ,limit: 20
                ,height: 'full-200'
                ,text: 'Sorry, there was an exception loading！'
                ,done: function(res){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                    if (res !== 0 ){
                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }
                    }
                }
            });
            //监听搜索
            form.on('submit(LAY-user-front-search)', function (data) {
                var field = data.field;
                //执行重载
                table.reload('LAY-user-manage', {
                    where: field
                    , page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行

                        if (res.code === 1001) {
                            //清空本地记录的 token，并跳转到登入页
                            admin.exit();
                        }

                        if (res.code === 1) {
                            layer.msg(res.msg, {icon: 5});
                        }
                    }
                });
            });
            form.on('submit(back_parents)', function (data) {
                //执行重载
                parents = [];
                table.reload('LAY-user-manage', {
                    where: {
                        parent_agent_id: id
                    }
                    , page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                        if (res !== 0) {
                            if (res.code === 1001) {
                                //清空本地记录的 token，并跳转到登入页
                                admin.exit();
                            }
                        }
                    }
                });
            });
            //监听工具条
            table.on('tool(LAY-user-manage)', function (obj) {
                var data = obj.data;
                if (obj.event === 'getsons') {
                    parents.push(data.parent_agent_id);
                    //执行重载
                    table.reload('LAY-user-manage', {
                        where: {
                            parent_agent_id: data.id
                        }
                        , page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                            if (res !== 0) {
                                if (res.code === 1001) {
                                    //清空本地记录的 token，并跳转到登入页
                                    admin.exit();
                                }
                            }
                        }
                    });
                } else if (obj.event === 'seach_parent') {
                    //执行重载
                    var i = parents.length;
                    var parent_id = parents[i - 1];
                    parents.pop();
                    table.reload('LAY-user-manage', {
                        where: {
                            parent_agent_id: parent_id
                        }
                        , page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                            if (res !== 0) {
                                if (res.code === 1001) {
                                    //清空本地记录的 token，并跳转到登入页
                                    admin.exit();
                                }
                            }
                        }
                    });

                } else if (obj.event === 'edit') {
                    layer.show('Edit Agency', '/agen/salesmen/add', data);
                } else if(obj.event === 'update_agent'){
                    layer.show('replace the superior', '/agen/salesmen/update_agent', data,600,400);
                } else if (obj.event === 'lock') {
                    var value = 0;
                    if (data.is_lock == 1) {
                        value = 0;
                    } else {
                        value = 1;
                    }
                    admin.req({
                        type: "POST",
                        url: '/agen/update',
                        dataType: "json",
                        data: {name: 'is_lock', value: value, agentid: data.id},
                        done: function (result) { //返回数据根据结果进行相应的处理
                            layui.layer.msg(result.msg, {icon: 6});
                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            layui.table.reload('LAY-user-manage', {

                                done: function (res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                                    if (res !== 0) {
                                        if (res.code === 1001) {
                                            //清空本地记录的 token，并跳转到登入页
                                            admin.exit();
                                        }
                                    }
                                }
                            }); //重载表格
                        }
                    });
                } else if (obj.event === 'addson') {
                    var value = 0;
                    if (data.is_addson == 1) {
                        value = 0;
                    } else {
                        value = 1;
                    }
                    admin.req({
                        type: "POST",
                        url: '/agen/update',
                        dataType: "json",
                        data: {name: 'is_addson', value: value, agentid: data.id},
                        done: function (result) { //返回数据根据结果进行相应的处理
                            layer.msg(result.msg, {icon: 6});
                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            layui.table.reload('LAY-user-manage'); //重载表格
                            layer.close(index); //执行关闭
                        }
                    });
                } else if (obj.event == 'this-sons') {
                    console.log(obj)
                    var parent_agent_id = obj.data.id
                    parent.layui.index.openTabsPage("/agen/user/index?parent_id=" + parent_agent_id, 'Agent member');
                } else if (obj.event == 'addsonagent') {
                    layer.prompt({title: 'Please enter the subordinate user ID', formType: 0, btn: ['query this user', 'Cancel']}, function (value, index) {
                        layer.close(index);
                        if (value.length == 0) {
                            layer.msg('Username cannot be empty', {icon: 5});
                        } else {
                            admin.req({
                                type: "POST",
                                url: '/agen/search_agent_son',
                                dataType: "json",
                                data: {user_id: value, id: data.id},
                                done: function (result) { //返回数据根据结果进行相应的处理
                                    result.data.agent_id = data.id;
                                    layer.show('Edit Agency', '/agen/salesmen/add', result.data);
                                }
                            });
                        }
                    });
                } else if (obj.event == 'del_agent') {
                    var id = data.id;
                    layer.confirm('confirm to delete？', function (index) {
                        $.ajax({
                            url: '{{url('/agen/del_agent')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id},
                            success: function (res) {
                                if(res.code=="0"){
                                    layer.alert(res.msg);
                                    layer.close(index);
                                    table.reload('LAY-user-manage');
                                }else{
                                    layer.alert(res.msg);
                                    layer.close(index);
                                }

                            }
                        })
                    });
                }

            });
            //事件
            var active = {
                batchdel: function () {
                    var checkStatus = table.checkStatus('LAY-user-manage')
                        , checkData = checkStatus.data; //得到选中的数据

                    if (checkData.length === 0) {
                        return layer.msg('请选择数据');
                    }

                    layer.prompt({
                        formType: 1
                        , title: '敏感操作，请验证口令'
                    }, function (value, index) {
                        layer.close(index);

                        layer.confirm('确定删除吗？', function (index) {
                            table.reload('LAY-user-manage');
                            layer.msg('已删除');
                        });
                    });
                }
                , add: function () {
                    layer.prompt({title: 'Please enter the subordinate user ID', formType: 0, btn: ['query this user', 'Cancel']}, function (value, index) {
                        layer.close(index);
                        if (value.length == 0) {
                            layer.msg('User ID cannot be empty', {icon: 5});
                        } else {
                            admin.req({
                                type: "POST",
                                url: '/agen/searchuser',
                                dataType: "json",
                                data: {user_id: value},
                                done: function (result) { //返回数据根据结果进行相应的处理
                                    layer.show('Add an agent', '/agen/salesmen/add', result.data);
                                }
                            });
                        }
                    });
                }
            };

            $('.layui-btn.layuiadmin-btn-useradmin').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        });
    </script>

@endsection

<div id="this_all_sons">
    <table id="LAY-user-sons" lay-filter="LAY-user-sons"></table>
</div>