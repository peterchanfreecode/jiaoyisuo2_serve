@extends('admin._layoutNew')
@section('page-head')
    <style>
        li[hidden] {
            display: none;
        }
        .layui-form-label{
            width: 180px;
        }
        .layui-input-block{
            margin-left: 210px;
        }
    </style>
@stop
@section('page-content')

    <div class="larry-personal-body clearfix">
        <form class="layui-form col-lg-5">
            <div class="layui-tab">
                <ul class="layui-tab-title">
                    <li class="layui-this">链接设置</li>
                    <li>图片配置</li>
                </ul>
                <div class="layui-tab-content">
                    <!--通知设置开始-->
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form-item">
                            <label class="layui-form-label">网站名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="web_name" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['web_name'])){{$setting['web_name']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">APP名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="app_name" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['app_name'])){{$setting['app_name']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">socket链接</label>
                            <div class="layui-input-block">
                                <input type="text" name="socketApi" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['socketApi'])){{$setting['socketApi']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">客服链接 </label>
                            <div class="layui-input-block">
                                <input type="text" name="chatUrl" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['chatUrl'])){{$setting['chatUrl']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">安卓下载链接 </label>
                            <div class="layui-input-block">
                                <input type="text" name="downloadUrl" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['downloadUrl'])){{$setting['downloadUrl']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">IOS下载链接 </label>
                            <div class="layui-input-block">
                                <input type="text" name="ios_downloadUrl" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['ios_downloadUrl'])){{$setting['ios_downloadUrl']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">万能邮箱验证码 </label>
                            <div class="layui-input-block">
                                <input type="text" name="email_code" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['email_code'])){{$setting['email_code']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">客服电话 </label>
                            <div class="layui-input-block">
                                <input type="text" name="phone_system" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['phone_system'])){{$setting['phone_system']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">客服邮箱 </label>
                            <div class="layui-input-block">
                                <input type="text" name="email_system" autocomplete="off" class="layui-input"
                                       value="@if(isset($setting['email_system'])){{$setting['email_system']}}@endif">
                            </div>
                        </div>
<!--                        <div class="layui-form-item">
                            <label class="layui-form-label">谷歌安全码 </label>
                             <a class="layui-btn layui-btn-s" lay-submit lay-filter="get_code" >获取安全码</a>
                        </div>-->
                    </div>
                    <!--图片配置开始-->
                    <div class="layui-tab-item">
                        <div class="layui-form-item">
                            <label class="layui-form-label">下载页</label>
                            <div class="layui-input-block">
                                <button class="layui-btn" type="button" id="homes_test">选择图片</button>
                                <br>
                                <img src="@if(!empty($setting['homes'])){{$setting['homes']}}@endif" id="img_homes" class="thumbnail" style="display: @if(!empty($setting['homes'])){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                                <input type="hidden" name="homes" id="homes" value="@if(!empty($setting['homes'])){{$setting['homes']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">首页logo</label>
                            <div class="layui-input-block">
                                <button class="layui-btn" type="button" id="index_logo_test">选择图片</button>
                                <br>
                                <img src="@if(!empty($setting['index_logo'])){{$setting['index_logo']}}@endif" id="img_index_logo" class="thumbnail" style="display: @if(!empty($setting['index_logo'])){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                                <input type="hidden" name="index_logo" id="index_logo" value="@if(!empty($setting['index_logo'])){{$setting['index_logo']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">资产logo</label>
                            <div class="layui-input-block">
                                <button class="layui-btn" type="button" id="money_logo_test">选择图片</button>
                                <br>
                                <img src="@if(!empty($setting['money_logo'])){{$setting['money_logo']}}@endif" id="img_money_logo" class="thumbnail" style="display: @if(!empty($setting['money_logo'])){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                                <input type="hidden" name="money_logo" id="money_logo" value="@if(!empty($setting['money_logo'])){{$setting['money_logo']}}@endif">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">默认头像</label>
                            <div class="layui-input-block">
                                <button class="layui-btn" type="button" id="user_avatar_test">选择图片</button>
                                <br>
                                <img src="@if(!empty($setting['user_avatar'])){{$setting['user_avatar']}}@endif" id="img_user_avatar" class="thumbnail" style="display: @if(!empty($setting['user_avatar'])){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                                <input type="hidden" name="user_avatar" id="user_avatar" value="@if(!empty($setting['user_avatar'])){{$setting['user_avatar']}}@endif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="website_submit">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        layui.use(['element', 'form', 'upload', 'layer'], function () {
            var element = layui.element
                ,layer = layui.layer
                ,form = layui.form
                ,laydate = layui.laydate
                ,upload = layui.upload
                ,$ = layui.$;
            //执行实例
            var uploadInst = upload.render({
                elem: '#homes_test' //绑定元素
                ,url: '{{URL("admin/dgx_upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#homes").val(res.message.path)
                        $("#img_homes").show()
                        $("#img_homes").attr("src",res.message.full_path)

                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
            var uploadlogo_test = upload.render({
                elem: '#index_logo_test' //绑定元素
                ,url: '{{URL("admin/dgx_upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#index_logo").val(res.message.path)
                        $("#img_index_logo").show()
                        $("#img_index_logo").attr("src",res.message.full_path)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
            var uploadmoney_test = upload.render({
                elem: '#money_logo_test' //绑定元素
                ,url: '{{URL("admin/dgx_upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#money_logo").val(res.message.path)
                        $("#img_money_logo").show()
                        $("#img_money_logo").attr("src",res.message.full_path)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
            var uploaduser_avatar_test = upload.render({
                elem: '#user_avatar_test' //绑定元素
                ,url: '{{URL("admin/dgx_upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#user_avatar").val(res.message.path)
                        $("#img_user_avatar").show()
                        $("#img_user_avatar").attr("src",res.message.full_path)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });

            form.on('submit(website_submit)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '/admin/app_setting/postadd',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.msg(res.message);
                    }
                });
                return false;
            });

            form.on('submit(get_code)', function () {
                layer.prompt({
                        formType: 3,
                        title: '请输入谷歌安全码'
                        ,placeholder: '请输入谷歌安全码'
                        ,btn: ['确定', '取消']
                        ,btn2: function(index){
                            layer.close(index);
                        }
                        ,cancel: function(index){
                            layer.close(index);
                        }
                    },function(value, index){
                        layer.close(index);
                        $.ajax({
                            url: '/admin/app_setting/step_code',
                            contentType: "application/json",
                            method: 'POST',
                            data: JSON.stringify({step_code:value}),
                            success: function (data) {
                                layer.alert(data.message.msg);
                                if(data.message.imgUrl){
                                    const a = document.createElement('a');
                                    fetch(data.message.imgUrl)  // 跨域时会报错
                                        .then(res => res.blob())
                                        .then(blob => { // 将链接地址字符内容转变成blob地址
                                            a.href = URL.createObjectURL(blob);
                                            a.download = '二维码'; // 下载文件的名字
                                            document.body.appendChild(a);
                                            a.click();
                                            //在资源下载完成后 清除 占用的缓存资源
                                            window.URL.revokeObjectURL(a.href);
                                            document.body.removeChild(a);
                                        })
                                }

                            }
                        });
                        return true;

                    });


            });
        });
    </script>
@stop