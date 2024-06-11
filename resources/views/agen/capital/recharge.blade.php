
@extends('agent.layadmin')

@section('title', '充币列表')

@section('page-head')

@endsection

@section('page-content')

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">
                
                <div class="layui-inline">
                        <label class="layui-form-label">Currency</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="currency_id" >
                                <option value="-1" class="ww">all</option>
                                @foreach ($legal_currencies as $currency)
                                    <option value="{{$currency->id}}" class="ww">{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">User ID</label>
                    <div class="layui-input-block">
                        <input type="text" name="uid" placeholder="please enter" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">username</label>
                    <div class="layui-input-block">
                        <input type="text" name="account_number" placeholder="please enter" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">Agent</label>
                    <div class="layui-input-block" style="width:130px;">
                        <select name="belong_agent" >
                            <option value="" >all</option>
                            @foreach ($son_agents as $son)
                                <option value="{{$son->username}}">{{$son->username}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="LAY-user-front-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="LAY-user-manage" lay-filter="LAY-user-manage"></table>
            
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    layui.use(['index','table' , 'layer'], function() {
        var $ = layui.$
            ,admin = layui.admin
            ,view = layui.view
            ,table = layui.table
            ,form = layui.form
        //充币管理
        table.render({
            elem: '#LAY-user-manage'
            ,method : 'get'
            ,url: '{{url('agen/capital/recharge')}}'
            ,toolbar: true
            ,totalRow: true
            ,cols: [[
                {type: 'checkbox', fixed: 'left'}
                ,{field: 'id',width: 100, title: 'order ID', sort: true }
                ,{field: 'user_id', width: 100,title: 'User ID', sort: true }
                ,{field: 'currency_name', width: 100, title: 'Currency'}
                ,{field: 'account_number', minwidth: 200,title: 'username', totalRowText: 'Subtotal'}
                ,{field: 'belong_agent_name', minwidth: 200,title: 'Agent'}
                ,{field: 'value', title: 'Deposit amount', totalRow: true}
                ,{field: 'info', title: 'illustrate'}
                ,{field: 'created_time', title: 'Arrival time'}
            ]]
            ,page: true
            ,limit: 30
            ,height: 'full-240'
            ,text: 'Sorry, there was an error loading！'
            
            ,done: function(res) { //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                if (res !== 0 ){
                    if (res.code === 1001) {
                        //清空本地记录的 token，并跳转到登入页
                        admin.exit();
                    }
                }
            }
        });

        form.render(null, 'layadmin-userfront-formlist');

        //监听搜索
        form.on('submit(LAY-user-front-search)', function(data){
            var field = data.field;

            //执行重载
            table.reload('LAY-user-manage', {
                where: field
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
                ,done: function(res){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行

                    if (res.code === 1001) {
                        //清空本地记录的 token，并跳转到登入页
                        admin.exit();
                    }

                    if (res.code === 1){
                        layer.msg(res.msg ,{icon : 5});
                    }
                }
            });
        });

    });
</script>
@endsection
