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
                        <label class="layui-form-label">user account</label>
                        <div class="layui-input-block">
                            <input type="text" name="account" placeholder="User phone number or email" autocomplete="off"
                                   class="layui-input" value="">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">User ID</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_id" placeholder="User ID" autocomplete="off" class="layui-input"
                                   value="">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">Approval Status</label>
                        <div class="layui-input-block" style="width:130px;">
                            <select name="review_status_s" id="review_status_s" class="layui-input">
                                <option value="-1">all</option>
                                <option value="0">to be reviewed</option>
                                <option value="1">Not reviewed</option>
                                <option value="2">Audited</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-useradmin" id="mobile_search" lay-submit
                                lay-filter="mobile_search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-carousel layadmin-backlog" style="background-color: #fff">
                    <table id="userlist" lay-filter="userlist"></table>
                </div>
            </div>

            <script type="text/html" id="barDemo">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="detail">Check</a>
                @if($level <= 1 )
                  @{{d.status==0 ? ' <a class="layui-btn layui-btn layui-btn-xs" lay-event="auth_suc">'+'pass'+'</a>': '' }}
                  @{{d.status==0 ? ' <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="auth_fail">'+'reject'+'</a>': '' }}
                @endif
            </script>

            <script type="text/html" id="switchTpl">
                <input type="checkbox" name="review_status" value="@{{d.id}}" lay-skin="switch" lay-text="yes|no"
                       lay-filter="sexDemo" @{{ d.review_status== 2 ? 'checked' : '' }} >
            </script>
            <script type="text/html" id="switchTpl2">
                @{{d.status==0 ? '<span class="layui-badge layui-bg-yellow">'+'to be reviewed'+'</span>' : '' }}
                @{{d.status==1 ? '<span class="layui-badge layui-bg-red">'+'Audit not passed'+'</span>' : '' }}
                @{{d.status==2 ? '<span class="layui-badge layui-bg-green">'+'examination passed'+'</span>' : '' }}
            </script>

            <script type="text/html" id="switchTpl3">
                <img src="@{{d.front_pic}}" lay-event="showimg" width="80px;"/>
            </script>

            <script type="text/html" id="switchTpl4">
                <img src="@{{d.reverse_pic}}" lay-event="showimg1" width="80px;"/>
            </script>
            <style>
                .layui-table-cell {
                    height: auto;
                }
            </style>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        window.onload = function () {
            document.onkeydown = function (event) {
                var e = event || window.event || arguments.callee.caller.arguments[0];
                if (e && e.keyCode == 13) { // enter 键
                    $('#mobile_search').click();
                }
            };
            layui.use(['element', 'form', 'layer', 'table'], function () {
                var element = layui.element;
                var layer = layui.layer;
                var table = layui.table;
                var $ = layui.$;
                var form = layui.form;


                form.on('submit(mobile_search)', function (obj) {
                    var account = $("input[name='account']").val();
                    var review_status_s = $("select[name='review_status_s']").val();
                    var user_id = $("input[name='user_id']").val();
                    tbRend("{{url('agen/user/real_list')}}?account=" + account + "&review_status_s=" + review_status_s + "&user_id=" + user_id);
                    return false;
                });

                function tbRend(url) {
                    table.render({
                        elem: '#userlist'
                        , url: url
                        , page: true
                        , limit: 20
                        , cols: [[
                            {field: 'id', title: 'Authentication ID'}
                            , {field: 'account', title: 'user account'}
                            , {field: 'user_id', title: 'User ID'}
                            , {field: 'type', title: 'type'}
                            , {field: 'name', title: 'actual name'}
                            , {field: 'front_pic', title: 'front', width: 120, templet: '#switchTpl3'}
                            , {field: 'reverse_pic', title: 'reverse side', width: 120, templet: '#switchTpl4'}
                            , {field: 'status', title: 'state', templet: '#switchTpl2'}
                            , {field: 'create_time', title: 'application time'}
                            , {fixed: 'right', title: 'operate', width: 280, align: 'center', toolbar: '#barDemo'}
                        ]]
                    });
                }

                tbRend("{{url('agen/user/real_list')}}");


                //监听工具条
                table.on('tool(userlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data;
                    var layEvent = obj.event;
                    var tr = obj.tr;
                    if (layEvent === 'detail') { //详情
                        layer.open({
                            title: 'Certification Details'
                            , type: 2
                            , content: '{{url('agen/user/real_info')}}?id=' + data.id
                            , area: ['900px', '700px']
                        });
                    }
                    if (obj.event === 'auth_suc') {
                        layer.confirm('OK to pass?', function (index) {
                            $.ajax({
                                url: '{{url('agen/user/real_auth')}}',
                                type: 'post',
                                dataType: 'json',
                                data: {id: data.id, action: 'suc'},
                                success: function (res) {
                                    layer.msg(res.msg);
                                    var account = $("input[name='account']").val();
                                    var review_status_s = $("select[name='review_status_s']").val();
                                    var user_id = $("input[name='user_id']").val();
                                    tbRend("{{url('agen/user/real_list')}}?account=" + account + "&review_status_s=" + review_status_s + "&user_id=" + user_id);
                                }
                            });

                        });

                    }
                    if (obj.event === 'auth_fail') {
                        layer.confirm('Confirmed to refuse?', function (index) {
                            $.ajax({
                                url: '{{url('agen/user/real_auth')}}',
                                type: 'post',
                                dataType: 'json',
                                data: {id: data.id, action: 'fail'},
                                success: function (res) {
                                    layer.msg(res.msg);
                                    var account = $("input[name='account']").val();
                                    var review_status_s = $("select[name='review_status_s']").val();
                                    var user_id = $("input[name='user_id']").val();
                                    tbRend("{{url('agen/user/real_list')}}?account=" + account + "&review_status_s=" + review_status_s + "&user_id=" + user_id);
                                }
                            });
                        });
                    }

                    if (obj.event === 'showimg') {
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: 0,
                            skin: 'layui-layer-nobg', //没有背景色
                            shadeClose: true,
                            content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + data.front_pic + '">'
                        });
                    }
                    if (obj.event === 'showimg1') {
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: 0,
                            skin: 'layui-layer-nobg', //没有背景色
                            shadeClose: true,
                            content: '<img style="display: inline-block; width: 100%; height: 100%;" src="' + data.reverse_pic + '">'
                        });
                    }
                });
            });
        }
    </script>

@endsection