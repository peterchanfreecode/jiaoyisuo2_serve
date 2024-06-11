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
                <label>用户ID</label>
                <div class="layui-input-inline">
                    <input type="text" name="id" placeholder="请输入用户ID" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>用户账号</label>
                <div class="layui-input-inline">
                    <input type="text" name="account" placeholder="请输入手机号或邮箱" autocomplete="off" class="layui-input"
                           value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>真实姓名</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" placeholder="真实姓名" autocomplete="off" class="layui-input" value="">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>风控类型</label>
                <div class="layui-input-inline" style="width: 90px">
                    <select name="risk" lay-verify="required" id="risk">
                        <option value="-2">全部</option>
                        <option value="0">无</option>
                        <option value="-1">亏损</option>
                        <option value="1">盈利</option>
                    </select>
                </div>
                <button class="layui-btn layui-btn-primary" id="btn-set" type="button"
                        style="padding:0px; margin-left: -4px; width: 30px;">
                    <i class="layui-icon layui-icon-set-fill"></i>
                </button>
            </div>
            <div class="layui-btn-group">
                <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"><i
                            class="layui-icon layui-icon-search"></i></button>
            </div>
        </div>
        <button style="margin-left: 50px;margin-top: 10px" class="layui-btn layui-btn-normal layui-btn-radius" lay-filter="add_user" id="add_user">添加会员</button>
    </div>

    <table id="userlist" lay-filter="userlist"></table>
@endsection

@section('scripts')
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="users_wallet">钱包</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="lock_user">锁定</a>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="gold" >体验金</a>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="bind">绑定邀请人</a><br/>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="score">修改信用分</a>
        <a class="layui-btn  layui-btn-xs" lay-event="pass">修改登录密码</a>
        <a class="layui-btn  layui-btn-xs" lay-event="pay_pass">修改支付密码</a><br/>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="bind_email">邮箱绑定邀请人</a>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="auth">手动实名</a>
        <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="atelier">VIP</a>
    </script>
    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_real_user" value="@{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="is_real_user" @{{
               d.is_real_user== 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="switchWithdrawTpl">
        <input type="checkbox" name="is_withdraw" value="@{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="is_withdraw" @{{
               d.is_withdraw== 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="isAtelier">
        <input type="checkbox" name="is_atelier" value="@{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="is_atelier" @{{ d.is_atelier== 1 ? 'checked' : '' }} disabled>
    </script>
    <script type="text/html" id="status_t">
        <a class="layui-btn layui-btn-xs @{{ d.status == 1 ? 'layui-btn-danger' : 'layui-btn-primary' }} ">@{{ d.status
            == 1 ? '已锁定' : '正　常' }}</a>
    </script>

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
                , url: '/admin/user/list'
                , page: true
                , limit: 100
                , limits: [20, 50, 100, 200, 500, 1000]
                , height: 'full-60'
                , cols: [[
                    {field: '', type: 'checkbox'}
                    , {field: 'user_info', title: '帐号信息'}
                    , {field: 'time_info', title: '时间信息'}
                    , {field: 'yao_info', title: '邀请信息'}
                    , {field: 'is_real_user', title: '真实用户', width: 100, templet: "#switchTpl"}
                    , {field: 'withdraw', title: '提现', width: 100, templet: "#switchWithdrawTpl"}
                    , {field: 'invite_info', title: '推广信息'}
                    , {field: 'money_info', title: '资金信息',}
                    , {field: 'lock_info', title: '资产冻结',}
                    , {fixed: 'right', title: '操作', width: 380, align: 'center', toolbar: '#barDemo'}
                ]]
            });

            $('input[name=account]').keypress(function (event) {
                if (event.charCode == 13) {
                    $('#mobile_search').click();
                }
            });

            /*$('#add_user').click(function(){layer_show('添加会员', '/admin/user/add');});*/

            form.on('submit(mobile_search)', function (obj) {
                user_table.reload({
                    where: obj.field
                });
                return false;
            });
            $('#add_user').click(function () {
                layer_show('编辑会员', '/admin/user/add_user',"700","400");
            });

            //监听锁定操作
            form.on('switch(status)', function (obj) {
                var id = this.value;
                $.ajax({
                    url: '{{url('admin/user/lock')}}',
                    type: 'post',
                    dataType: 'json',
                    data: {id: id},
                    success: function (res) {
                        layer.msg(res.message);
                    }
                });
            });
            form.on('switch(is_real_user)', function(obj){
                var id = this.value;
                $.ajax({
                    url:'{{url('admin/user/real_user')}}',
                    type:'post',
                    dataType:'json',
                    data:{id:id},
                    success:function (res) {
                        if(res.error != 0){
                            layer.msg(res.message);
                        }
                    }
                });
            });
            form.on('switch(is_withdraw)', function(obj){
                var id = this.value;
                $.ajax({
                    url:'{{url('admin/user/withdraw')}}',
                    type:'post',
                    dataType:'json',
                    data:{id:id},
                    success:function (res) {
                        layer.msg(res.message);
                    }
                });
            });
            $('#btn-set').click(function () {
                var checkStatus = table.checkStatus('userlist');
                var risk = $('#risk').val();
                var ids = [];
                try {
                    if (checkStatus.data.length <= 0) {
                        throw '请先选择用户';
                    }
                    if (risk <= -2) {
                        throw '请选择风控类型';
                    }
                    checkStatus.data.forEach(function (item, index, arr) {
                        ids.push(item.id);
                    });
                    $.ajax({
                        url: '/admin/user/batch_risk'
                        , type: 'POST'
                        , data: {risk: risk, ids: ids}
                        , success: function (res) {
                            layer.msg(res.message, {
                                time: 2000,
                                end: function () {
                                    if (res.type == 'ok') {
                                        user_table.reload();
                                    }
                                }
                            });
                        }
                        , error: function (res) {
                            layer.msg('网络错误');
                        }
                    })

                } catch (error) {
                    layer.msg(error);
                }
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
                            url: "admin/user/del",
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
                } else if (layEvent === 'edit') { //编辑
                    layer_show('编辑会员', '/admin/user/edit?id=' + data.id);
                } else if (layEvent === 'users_wallet') {
                    var index = layer.open({
                        title: '钱包管理'
                        , type: 2
                        , content: '/admin/user/users_wallet?id=' + data.id
                        , maxmin: true
                    });
                    layer.full(index);
                } else if (layEvent == 'candy_change') {
                    var index = layer.open({
                        title: '通证调节'
                        , type: 2
                        , content: '/admin/user/candy_conf/' + data.id
                        , maxmin: true
                    });
                    layer.full(index);
                } else if (layEvent === 'lock_user') {
                    var index = layer.open({
                        title: '用户锁定'
                        , type: 2
                        , content: '/admin/user/lock?id=' + data.id
                        , maxmin: true
                        , area: ["380px", "430px"],
                    });
                } else if (layEvent === 'bind') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入邀请人用户ID'
                        , placeholder: '请输入邀请人用户ID'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/bind')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, bind_id: value},
                            success: function (res) {
                                if (res.message == "此用户已绑定邀请人") {
                                    layer.confirm('已存在邀请人,确定重新绑定？', function (index) {
                                        $.ajax({
                                            url: '{{url('admin/user/bind')}}',
                                            type: 'post',
                                            dataType: 'json',
                                            data: {id: id, bind_id: value, check_bind: 1},
                                            success: function (res) {
                                                layer.msg(res.message);
                                                user_table.reload();
                                            }
                                        });
                                    });
                                } else {
                                    user_table.reload();
                                }
                            }
                        })

                        return true;
                    });
                } else if (layEvent === 'bind_email') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入邀请人用户邮箱'
                        , placeholder: '请输入邀请人用户邮箱'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/bind_email')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, bind_id: value},
                            success: function (res) {
                                if (res.message == "此用户已绑定邀请人") {
                                    layer.confirm('已存在邀请人,确定重新绑定？', function (index) {
                                        $.ajax({
                                            url: '{{url('admin/user/bind_email')}}',
                                            type: 'post',
                                            dataType: 'json',
                                            data: {id: id, bind_id: value, check_bind: 1},
                                            success: function (res) {
                                                layer.msg(res.message);
                                                user_table.reload();
                                            }
                                        });
                                    });
                                } else {
                                    user_table.reload();
                                }
                            }
                        })

                        return true;
                    });
                }else if (layEvent === 'score') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入信用分'
                        , placeholder: '请输入信用分'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/score')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, score: value},
                            success: function (res) {
                                layer.msg(res.message);
                                user_table.reload();
                            }
                        })
                    });
                } else if (layEvent === 'gold') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入体验金额'
                        , placeholder: '请输入体验金额'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/gold')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, amount: value},
                            success: function (res) {
                                layer.msg(res.message);
                                user_table.reload();
                            }
                        })
                    });
                } else if (layEvent === 'pass') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入新登录密码'
                        , placeholder: '请输入新登录密码'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/update_pass')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, password: value},
                            success: function (res) {
                                layer.msg(res.message);
                                user_table.reload();
                            }
                        })
                    });
                } else if (layEvent === 'pay_pass') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入新支付密码'
                        , placeholder: '请输入新支付密码'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/update_pay_pass')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, password: value},
                            success: function (res) {
                                layer.msg(res.message);
                                user_table.reload();
                            }
                        })
                    });
                } else if (layEvent === 'auth') {
                    $.ajax({
                        url:'{{url('admin/user/auth')}}',
                        type: 'post',
                        dataType: 'json',
                        data: {id: data.id},
                        success: function (res) {
                            if (res.type == 'ok') {
                                layer.close(index);
                            } else {
                                layer.close(index);
                                layer.alert(res.message);
                            }
                        }
                    });
                }else if (layEvent === 'atelier') {
                    var id = data.id;
                    layer.prompt({
                        formType: 3,
                        title: '请输入VIP等级'
                        , placeholder: '请输入VIP等级'
                        , btn: ['确定', '取消']
                        , btn2: function (index) {
                            layer.close(index);
                        }
                        , cancel: function (index) {
                            layer.close(index);
                        }
                    }, function (value, index) {
                        layer.close(index);
                        $.ajax({
                            url: '{{url('admin/user/is_atelier')}}',
                            type: 'post',
                            dataType: 'json',
                            data: {id: id, vip: value},
                            success: function (res) {
                                layer.msg(res.message);
                                user_table.reload();
                            }
                        })
                    });
                }
            });
        });
    </script>
@endsection