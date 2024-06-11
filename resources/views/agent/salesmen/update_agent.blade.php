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
            <div class="layui-form layui-card-header layuiadmin-card-header-auto"  lay-filter="layadmin-userfront-formlist">
            </div>
            <div class="layui-card-body">
                <div class="layui-form" lay-filter="layuiadmin-form-useradmin" style="padding: 20px 0 0 0;">
                    <div class="layui-form-item">
                        <label  class="layui-form-label">上级代理</label>
                        <div class="layui-input-block">
                            <select name="agent_id" lay-verify="required" lay-search>
                                @foreach ($d as $c)
                                    <option value="{{ $c->id }}" >{{ $c->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-inline">
                            <input type="button" lay-submit lay-filter="LAY-user-front-submit" value="确认"
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

            form.on('submit(LAY-user-front-submit)', function (data) {
                var field = data.field; //获取提交的字段
                 var post_url = '/agent/salesmen/update_agent_add';

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