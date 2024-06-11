@extends('admin._layoutNew')

@section('page-head')
@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label for="currency_id" class="layui-form-label">币种</label>
            <div class="layui-input-block">
                <select name="currency_id" lay-verify="required" lay-search>
                    @foreach ($currency as $c)
                        <option value="{{ $c->id }}" @if ((isset($result) && $result->currency_id == $c->id)) selected @endif>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">期限</label>
            <div class="layui-input-block">
                <input id="day" type="text" name="day" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->day}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">利息(百分比)</label>
            <div class="layui-input-block">
                <input type="text" name="rate" id="rate" required autocomplete="off" placeholder="" class="layui-input" value="{{$result->rate}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">违约结算比例(百分比)</label>
            <div class="layui-input-block">
                <input type="text" name="cancel_rate" id="cancel_rate" required autocomplete="off" placeholder="" class="layui-input" value="{{$result->cancel_rate}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小值</label>
            <div class="layui-input-block">
                <input type="text" name="save_min" id="save_min"  autocomplete="off" placeholder="" class="layui-input" value="{{$result->save_min}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(中文)</label>
            <div class="layui-input-block">
                <input id="level_zn" type="text" name="level_zn" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_zh}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(英文)</label>
            <div class="layui-input-block">
                <input id="level_en" type="text" name="level_en" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_en}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(德文)</label>
            <div class="layui-input-block">
                <input id="level_de" type="text" name="level_de" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_de}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(日文)</label>
            <div class="layui-input-block">
                <input id="level_jp" type="text" name="level_jp" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_jp}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(韩文)</label>
            <div class="layui-input-block">
                <input id="level_kr" type="text" name="level_kr" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_kr}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(法文)</label>
            <div class="layui-input-block">
                <input id="level_fra" type="text" name="level_fra" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_fra}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(泰语)</label>
            <div class="layui-input-block">
                <input id="level_kr" type="text" name="level_th" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_th}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(越语)</label>
            <div class="layui-input-block">
                <input id="level_fra" type="text" name="level_vi" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_vi}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(繁体)</label>
            <div class="layui-input-block">
                <input id="level_fra" type="text" name="level_hk" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_hk}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">等级(巴西)</label>
            <div class="layui-input-block">
                <input id="level_pt" type="text" name="level_pt" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->level_pt}}">
            </div>
        </div>
        <input type="hidden" name="id" value="{{$result->id}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
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
                    url:'{{url('admin/deposit/postAdd')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection