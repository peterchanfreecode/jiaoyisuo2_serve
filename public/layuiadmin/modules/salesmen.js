/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */


layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,table = layui.table
        ,form = layui.form;

    //代理商管理
    table.render({
        elem: '#LAY-user-manage'
        ,url: '/agent/lists' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 100, title: '代理ID', sort: true }
            ,{field: 'user_id', width: 100, title: '用户ID', sort: true }
            ,{field: 'username', title: '代理账名', minWidth: 180 , event : "getsons",style:"color: #fff;background-color: #5FB878;"}
            ,{field: 'parent_agent_name', title: '上级代理', width: 180}
            ,{field: 'agent_name', title: '等级',width: 120}
            ,{field: 'is_lock', title: '是否锁定', templet: '#lockTpl'}
            ,{field: 'is_addson', title: '是否拉新', width: 90, templet: '#addsonTpl'}
            ,{field: 'reg_time', title: '加入时间', sort: true, width: 170}
            ,{title: '操作', minWidth: 700, align:'center',  toolbar: '#table-useradmin-webuser'}
        ]]
        ,page: true
        ,limit: 20
        ,height: 'full-200'
        ,text: '对不起，加载出现异常！'
        // ,headers: { //通过 request 头传递
        //     access_token: layui.data('layuiAdmin').access_token
        // }
        // ,where: { //通过参数传递
        //     access_token: layui.data('layuiAdmin').access_token
        // }
        ,done: function(res){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
            if (res !== 0 ){
                if (res.code === 1001) {
                    //清空本地记录的 token，并跳转到登入页
                    admin.exit();
                }
            }
        }
    });



    //管理员管理
    table.render({
        elem: '#LAY-user-back-manage'
        ,url: './json/useradmin/mangadmin.js' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 80, title: 'ID', sort: true}
            ,{field: 'loginname', title: '登录名'}
            ,{field: 'telphone', title: '手机'}
            ,{field: 'email', title: '邮箱'}
            ,{field: 'role', title: '角色'}
            ,{field: 'jointime', title: '加入时间', sort: true}
            ,{field: 'check', title:'审核状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
        ]]
        ,text: '对不起，加载出现异常！'
    });

    //监听工具条
    table.on('tool(LAY-user-back-manage)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                layer.close(index);
                layer.confirm('确定删除此管理员？', function(index){
                    console.log(obj)
                    obj.del();
                    layer.close(index);
                });
            });
        }else if(obj.event === 'edit'){
            admin.popup({
                title: '编辑管理员'
                ,area: ['420px', '450px']
                ,id: 'LAY-popup-user-add'
                ,success: function(layero, index){
                    view(this.id).render('user/administrators/adminform', data).done(function(){
                        form.render(null, 'layuiadmin-form-admin');

                        //监听提交
                        form.on('submit(LAY-user-back-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-user-back-manage'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        }
    });

    //角色管理
    table.render({
        elem: '#LAY-user-back-role'
        ,url: './json/useradmin/role.js' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 80, title: 'ID', sort: true}
            ,{field: 'rolename', title: '角色名'}
            ,{field: 'limits', title: '拥有权限'}
            ,{field: 'descr', title: '具体描述'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
        ]]
        ,text: '对不起，加载出现异常！'
    });

    //监听工具条
    table.on('tool(LAY-user-back-role)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此角色？', function(index){
                obj.del();
                layer.close(index);
            });
        }else if(obj.event === 'edit'){
            admin.popup({
                title: '添加新角色'
                ,area: ['500px', '480px']
                ,id: 'LAY-popup-user-add'
                ,success: function(layero, index){
                    view(this.id).render('user/administrators/roleform', data).done(function(){
                        form.render(null, 'layuiadmin-form-role');

                        //监听提交
                        form.on('submit(LAY-user-role-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-user-back-role'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        }
    });

    exports('salesmen', {})
});