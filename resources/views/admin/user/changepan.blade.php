@extends('admin._layoutNew')

@section('page-head')
<style>
    .layui-form-label {
        width: 150px;
    }
    .layui-input-block {
        margin-left: 180px;
    }
</style>
@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">UID</label>
            <div class="layui-input-block">
                <input type="text" name="account_number" autocomplete="off" placeholder="" class="layui-input" value="{{$result->account_number}}" disabled>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">修改盘口</label>
            <div class="layui-input-block">
                <select name="pan_type" lay-verify="required" lay-filter="pan_type">
                    <option value=""></option>
                    <option value="1" {{ $result->pan_type  == 1 ? 'selected' : '' }} >A</option>
                    <option value="2" {{ $result->pan_type  == 2 ? 'selected' : '' }} >B</option>
                    <option value="3" {{ $result->pan_type  == 3 ? 'selected' : '' }} >C</option>
 
                </select>
            </div>
        </div>
   
        <input type="hidden" name="id" value="{{$result->id}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script>
        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/user/dochangepan')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : {
                        pan_type: data.pan_type,
                        id: data.id,
                    }
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            layer.msg(res.message);
                            setTimeout(()=>{
                                parent.layer.close(index);
                                parent.window.location.reload();
                            }, 1000);
                            
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection