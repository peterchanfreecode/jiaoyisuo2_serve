@extends('agent.layadmin')

@section('page-head')
    <style>
        .layui-form-label {
            width: 120px;
        }

        .layui-form-mid {
            float: none;
            margin-left: 150px;
        }

        .layui-input-block {
            margin-left: 150px;
        }
    </style>
@endsection

@section('page-content')

    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                 lay-filter="layadmin-userfront-formlist">
            </div>
            <div class="layui-card-body">
                <div class="layui-form" lay-filter="layuiadmin-form-useradmin" style="padding: 20px 0 0 0;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">username</label>
                        <div class="layui-input-block">

                            @if(isset($d['username']))
                                <input type="text" name="username" value="{{$d['username']}}"
                                       lay-verify="required" placeholder="Please enter username" autocomplete="off"
                                       class="layui-input"
                                       readonly>
                            @else
                                <input type="text" name="username" value=""
                                       lay-verify="required" placeholder="Please enter username" autocomplete="off"
                                       class="layui-input">
                            @endif

                            <input type="hidden" name="user_id" value="{{ isset($d['user_id'])?$d['user_id']:0 }}">
                            <input type="hidden" name="agent_id" value="{{ isset($d['agent_id'])?$d['agent_id']:0 }}">
                            <input type="hidden" name="id" value="{{ isset($d['id'])?$d['id']:0 }}">

                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">subordinate status</label>
                        <div class="layui-input-inline">
                            @if( (!isset($d['id']) || !$d['id']) && isset($d['son_level']))
                                <button class="layui-btn layui-btn-normal layui-btn-radius">Tier  {{$d['son_level']}} Agent
                                </button>
                            @elseif(isset($d['id']) && $d['id'])
                                <button class="layui-btn layui-btn-normal layui-btn-radius">{{$d['self_info']}}</button>
                            @else
                                <button class="layui-btn layui-btn-danger layui-btn-radius">Can't add agent</button>
                            @endif
                        </div>
                    </div>
<!--
                    <div class="layui-form-item">
                        <label class="layui-form-label">头寸比例（%）</label>
                        <div class="layui-input-block">
                            <script type="text/html" template>
                                <input type="text" name="pro_loss" value="{{ isset($d['pro_loss']) ? $d['pro_loss'] : 0 }}"
                                       lay-verify="pro_loss" placeholder="代理商的头寸比例" autocomplete="off"
                                       class="layui-input">
                            </script>
                        </div>
                        <script type="text/html" template>
                            <div class="layui-form-mid layui-word-aux">设置下级代理商的头寸比例，该值不能超过<span
                                        class="layui-badge">{{isset($d['max_pro_loss']) ? $d['max_pro_loss'] : 100}}</span>。如20.85%，则输入20.85
                            </div>
                        </script>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">手续费比例（%）</label>
                        <div class="layui-input-block">
                            <script type="text/html" template>
                                <input type="text" name="pro_ser" value="{{isset($d['pro_ser']) ? $d['pro_ser'] : 0}}"
                                       lay-verify="pro_ser" placeholder="手续费比例" autocomplete="off" class="layui-input">
                            </script>
                        </div>
                        <script type="text/html" template>
                            <div class="layui-form-mid layui-word-aux">设置下级代理商的手续费比例，该值不能超过<span
                                        class="layui-badge">{{isset($d['max_pro_ser']) ? $d['max_pro_ser'] : '100'}}</span>。如20.85%，则输入20.85
                            </div>
                        </script>
                    </div>-->

                    @if(!isset($d['id']))
                    <!-- <div class="layui-form-item">
                        <label class="layui-form-label">授权码</label>
                        <div class="layui-input-block">
                            <script type="text/html" template>
                                <input type="text" name="authorization_code" value=""
                                       lay-verify="authorization_code" placeholder="请输入授权码" autocomplete="off" class="layui-input">
                            </script>
                        </div>
                        <script type="text/html" template>
                            <div class="layui-form-mid layui-word-aux">添加用户为代理商时需要填写用户的授权码(安全中心的授权码)
                            </div>
                        </script>
                    </div> -->
                    @endif

                    <div class="layui-form-item" lay-filter="sex">
                        <label class="layui-form-label">Is it locked</label>
                        <div class="layui-input-block">
                            @if(isset($d['is_lock']))
                            <input type="radio" name="is_lock" value="0"
                                   title="No" {{ isset($d['is_lock']) && $d['is_lock'] == 0 ? 'checked' : '' }} >
                            <input type="radio" name="is_lock" value="1"
                                   title="Yes"{{ isset($d['is_lock']) && $d['is_lock'] == 1 ? 'checked' : '' }}>
                            @else
                                <input type="radio" name="is_lock" value="0" title="No" checked>
                                <input type="radio" name="is_lock" value="1" title="Yes" >
                            @endif
                        </div>
                        <div class="layui-form-mid layui-word-aux">When locked, the user cannot log in to the agent management platform</div>
                    </div>
                    <div class="layui-form-item" lay-filter="sex">
                        <label class="layui-form-label">Pull new allowed</label>
                        <div class="layui-input-block">
                            @if(isset($d['is_addson']))
                            <input type="radio" name="is_addson" value="0"
                                   title="prohibit" {{  $d['is_addson'] == 0 ? 'checked' : '' }} >
                            <input type="radio" name="is_addson" value="1"
                                   title="allow"{{  $d['is_addson'] == 1 ? 'checked' : '' }}>
                            @else
                                <input type="radio" name="is_addson" value="0" title="prohibit">
                                <input type="radio" name="is_addson" value="1" title="allow" checked>
                            @endif
                        </div>
                        <div class="layui-form-mid layui-word-aux">When adding new agents is prohibited, the user cannot add his own subordinate agents</div>
                    </div>
                    @if(isset($d['id']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">Agent password</label>
                        <div class="layui-input-block">
                            <script type="text/html" template>
                                <input type="text" name="agent_password" value=""
                                       lay-verify="authorization_code" placeholder="Change agent password" autocomplete="off" class="layui-input">
                            </script>
                        </div>
                        <script type="text/html" template>
                            <div class="layui-form-mid layui-word-aux">Only modify the password of subordinate agents
                            </div>
                        </script>
                    </div>
                    @endif

                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-inline">
                            <input type="button" lay-submit lay-filter="LAY-user-front-submit" value="confirm"
                                   class="layui-btn">
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        layui.use(['index', 'form', 'upload', 'layer'], function () {
            var $ = layui.$
                , form = layui.form
                , upload = layui.upload
                , admin = layui.admin
                , view = layui.view
            var index = parent.layer.getFrameIndex(window.name)//当前ifarm索引
            //自定义验证
            form.verify({
                nickname: function (value, item) { //value：表单的值、item：表单的DOM对象
                    if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
                        return 'Username cannot have special characters';
                    }
                    if (/(^\_)|(\__)|(\_+$)/.test(value)) {
                        return 'Username must not have underscores at the beginning and end\'_\'';
                    }
                    if (/^\d+\d+\d$/.test(value)) {
                        return 'Username cannot be all numbers';
                    }
                }

                //我们既支持上述函数式的方式，也支持下述数组的形式
                //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
                , pass: [
                    /^[\S]{6,12}$/
                    , 'Password must be 6 to 12 characters and no spaces'
                ]
            });

            /*var jsObject = @json($d);
             form.val("layuiadmin-form-useradmin", jsObject)*/

            form.on('submit(LAY-user-front-submit)', function (data) {
                var field = data.field; //获取提交的字段
                console.log(field);
                if(field.agent_id == 0){
                    var post_url = '/agen/addagent';
                }else{
                    var post_url = '/agen/addsonagent';
                }
                admin.req({
                    type: "POST",
                    url: post_url,
                    dataType: "json",
                    data: field,
                    done: function (result) { //返回数据根据结果进行相应的处理
                        layer.msg(result.msg, {
                                icon: 1,
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function () {
                                parent.layer.close(index);
                                //parent.window.location.reload();
                                parent.layui.table.reload('LAY-user-manage' , {
                                    done: function(res){ //这里要说明一下：done 是只有 response 的 code 正常才会执行。而 succese 则是只要 http 为 200 就会执行
                                        if (res !== 0 ){
                                            if (res.code === 1001) {
                                                //清空本地记录的 token，并跳转到登入页
                                                admin.exit();
                                            }
                                        }
                                    }
                                }); //重载表格
                            }
                        );
                    }
                });
            });

        })
    </script>
@endsection

<div id="this_all_sons">
    <table id="LAY-user-sons" lay-filter="LAY-user-sons"></table>
</div>