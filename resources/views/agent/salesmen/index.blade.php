@extends('agent.layadmin')

@section('page-head')

@endsection

@section('page-content')

    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">用户ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">代理帐号</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">是否锁定</label>
                        <div class="layui-input-block">
                            <select name="is_lock">
                                <option value="2">不限</option>
                                <option value="1">锁定</option>
                                <option value="0">未锁定</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">是否拉新</label>
                        <div class="layui-input-block">
                            <select name="is_addson">
                                <option value="2">不限</option>
                                <option value="1">允许拉新</option>
                                <option value="0">禁止拉新</option>
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
                    <button class="layui-btn layuiadmin-btn-useradmin" data-type="add">添加下级代理商</button>
                    <button id="back_show_name" class=" layui-btn-normal layui-btn layuiadmin-btn-useradmin" lay-submit
                            lay-filter="back_parents" style="font-size: 15px">
                        返回顶级
                    </button>
                </div>

                <table id="LAY-user-manage" lay-filter="LAY-user-manage"></table>

                <script type="text/html" id="table-useradmin-webuser">

                    @{{#  if(d.level <3 ){ }}
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="getsons"><i
                                class="layui-icon"></i>查看下级</a>
                    @{{#  } }}
                    @if($is_admin== 1 )
                        @{{#  if(d.level >1 ){ }}
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="update_agent"><i
                                    class="layui-icon"></i>更换上级</a>
                        @{{#  } }}
                    @endif
                    @if($is_admin== 1 )

                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="del_agent"><i class="layui-icon"></i>删除代理</a>
                    @endif
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="seach_parent"><i class="layui-icon"></i>返回上级</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="google_code"><i class="layui-icon"></i>重置安全码</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="addsonagent"><i class="layui-icon layui-icon-add-1"></i>添加下级</a>
                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="this-sons"><i  class="layui-icon layui-icon-group"></i>查看会员</a>
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
        layui.use(['index', 'salesmen', 'table', 'layer'], function () {
            // console.log(layui.setter.base)
            var $ = layui.$
                , admin = layui.admin
                , view = layui.view
                , table = layui.table
                , form = layui.form;

            form.render(null, 'layadmin-userfront-formlist');

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
                    layer.show('编辑代理商', '/agent/salesmen/add', data);
                } else if(obj.event === 'update_agent'){
                    layer.show('更换上级', '/agent/salesmen/update_agent', data,600,400);
                } else if (obj.event === 'lock') {
                    var value = 0;
                    if (data.is_lock == 1) {
                        value = 0;
                    } else {
                        value = 1;
                    }
                    admin.req({
                        type: "POST",
                        url: '/agent/update',
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
                        url: '/agent/update',
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
                    parent.layui.index.openTabsPage("/agent/user/index?parent_id=" + parent_agent_id, '代理商会员');
                } else if (obj.event == 'addsonagent') {
                    layer.prompt({title: '请输入下级用户ID', formType: 0, btn: ['查询该用户', '取消']}, function (value, index) {
                        layer.close(index);
                        if (value.length == 0) {
                            layer.msg('用户名不能位空', {icon: 5});
                        } else {
                            admin.req({
                                type: "POST",
                                url: '/agent/search_agent_son',
                                dataType: "json",
                                data: {user_id: value, id: data.id},
                                done: function (result) { //返回数据根据结果进行相应的处理
                                    result.data.agent_id = data.id;
                                    layer.show('编辑代理商', '/agent/salesmen/add', result.data);
                                }
                            });
                        }
                    });
                } else if (obj.event == 'del_agent') {
                    var id = data.id;
                    layer.confirm('确定删除吗？', function (index) {
                        $.ajax({
                            url: '{{url('/agent/del_agent')}}',
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
                }else  if (obj.event == 'google_code') {
                    var id = data.id;
                    layer.confirm('确定重置吗？', function (index) {
                        $.ajax({
                            url: '{{url('/agent/google_code')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id},
                            success: function (res) {
                                if(res.code=="300"){
                                    layer.alert(res.data.msg);
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
                    layer.prompt({title: '请输入下级用户ID', formType: 0, btn: ['查询该用户', '取消']}, function (value, index) {
                        layer.close(index);
                        if (value.length == 0) {
                            layer.msg('用户ID不能位空', {icon: 5});
                        } else {
                            admin.req({
                                type: "POST",
                                url: '/agent/searchuser',
                                dataType: "json",
                                data: {user_id: value},
                                done: function (result) { //返回数据根据结果进行相应的处理
                                    layer.show('添加代理商', '/agent/salesmen/add', result.data);
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