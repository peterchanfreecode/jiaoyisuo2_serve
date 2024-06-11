@extends('admin._layoutNew')
@section('page-head')
	<link rel="stylesheet" type="text/css" href="{{URL("layui/css/layui.css")}}" media="all">
	<link rel="stylesheet" type="text/css" href="{{URL("admin/common/bootstrap/css/bootstrap.css")}}" media="all">
	<link rel="stylesheet" type="text/css" href="{{URL("admin/common/global.css")}}" media="all">
	<link rel="stylesheet" type="text/css" href="{{URL("admin/css/personal.css")}}" media="all">
@endsection
@section('page-content')
	<form class="layui-form" method="POST">
		<input type="hidden" name="id"  value="@if (isset($result['id'])){{ $result['id'] }}@endif">
		<input type="hidden"  id="currey_id" value="@if (isset($result['currency'])){{ $result['currency'] }}@endif">
		{{ csrf_field() }}
		<div class="layui-form-item">
			<label class="layui-form-label">活动名称</label>
			<div class="layui-input-block">
				<input class="layui-input" name="title" lay-verify="required" placeholder="请输入活动名称" type="text" value="@if (isset($result['title'])){{$result['title']}}@endif">
			</div>
		</div>

{{--		<div class="layui-form-item layui-form-text">--}}
{{--			<label class="layui-form-label">图片</label>--}}
{{--			<div class="layui-input-block">--}}
{{--				<button class="layui-btn" type="button" id="upload_test">选择图片</button>--}}
{{--				<br>--}}
{{--				<img src="@if(!empty($result->upload_test)){{$result->upload_test}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->upload_test)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">--}}
{{--				<input type="hidden" name="upload_test" lay-verify="required" id="upload_test_value" value="@if(!empty($result->upload_test)){{$result->upload_test}}@endif">--}}
{{--			</div>--}}
{{--		</div>--}}

		<div class="layui-form-item">
			<label for="currency_id" class="layui-form-label">认购项目</label>
			<div class="layui-input-block">
				<select name="currency"  lay-filter="select_act" lay-verify="required" lay-search>
					<option value="" >请选择</option>
					@foreach ($currency as $c)
						<option  value="{{ $c["id"] }}" @if ((isset($result) && $result->currency ==$c["id"])) selected @endif>{{ $c["name"] }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="layui-form-item">
			<label for="currency_id" class="layui-form-label">结算币种</label>
			<div class="layui-input-block">
				<select name="pay_currency_id" lay-verify="required" lay-search>
					<option value="" >请选择</option>
					@foreach ($currency as $c)
						<option value="{{ $c["id"] }}" @if ((isset($result) && $result->pay_currency_id ==$c["id"])) selected @endif>{{ $c["name"] }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">发行价</label>
				<div class="layui-input-inline">
					<input class="layui-input price"   id="price" lay-verify="required" name="price" type="text" value="{{$result['price']}}">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">发行量</label>
				<div class="layui-input-inline">
					<input class="layui-input amount" lay-verify="required" name="amount" type="text" value="{{$result['amount']}}">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">发行时间</label>
				<div class="layui-input-inline">
					<input class="layui-input laydate" lay-verify="required|date" name="start_at" type="text" value="{{$result['start_at']}}">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">市场价</label>
				<div class="layui-input-inline">
					<input class="layui-input market_price"  lay-verify="required" name="market_price" type="text" value="{{$result['market_price']}}">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">剩余进度%</label>
				<div class="layui-input-inline">
					<input class="layui-input progress" lay-verify="progress" name="progress" type="text" value="{{$result['progress']}}">
				</div>
			</div>

			<div class="layui-inline">
				<label class="layui-form-label">可申购范围</label>
				<div class="layui-input-inline">
					<input class="layui-input min" lay-verify="required" name="min" type="text" value="{{$result['min']}}">
				</div>
				<span style="float: left">-&nbsp;&nbsp;</span>
				<div class="layui-input-inline">
					<input class="layui-input max" lay-verify="required" name="max" type="text" value="{{$result['max']}}">
				</div>
			</div>
		</div>

		<div class="layui-form-item">
			<div class="layui-inline">
				<label class="layui-form-label">公告时间</label>
				<div class="layui-input-inline">
					<input id="show_start" class="layui-input laydate" lay-verify="required|date" name="show_start" type="text" value="{{$result['show_start']}}">
				</div>
				<span style="float: left">-&nbsp;&nbsp;</span>
				<div class="layui-input-inline">
					<input id="show_end" class="layui-input laydate" lay-verify="required|date" name="show_end" type="text" value="{{$result['show_end']}}">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">筹集时间</label>
				<div class="layui-input-inline">
					<input class="layui-input laydate" lay-verify="required|date" name="raise_start" type="text" value="{{$result['raise_start']}}">
				</div>
				<span style="float: left">-&nbsp;&nbsp;</span>
				<div class="layui-input-inline">
					<input class="layui-input laydate" lay-verify="required|date" name="raise_end" type="text" value="{{$result['raise_end']}}">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">锁仓时间</label>
				<div class="layui-input-inline">
					<input class="layui-input laydate" {{--@if($result['lock_start']) disabled="disabled" @endif--}}  lay-verify="required|date" name="lock_start" type="text" value="{{$result['lock_start']}}">
				</div>
				<span style="float: left">-&nbsp;&nbsp;</span>
				<div class="layui-input-inline">
					<input class="layui-input laydate" lay-verify="required|date"  {{--@if($result['lock_end']) disabled="disabled" @endif --}}name="lock_end" type="text" value="{{$result['lock_end']}}">
				</div>
			</div>
{{--			<div class="layui-form-item">--}}
{{--				<label class="layui-form-label">活动状态</label>--}}
{{--				<div class="layui-input-inline">--}}
{{--					<select name="status" class="" lay-filter="browse_grant" lay-verify="required">--}}
{{--						<option value="0" @if(isset($result) && $result['status'] == 0) selected @endif>待开始</option>--}}
{{--						<option value="1" @if(isset($result) && $result['status'] == 1) selected @endif>进行中</option>--}}
{{--						<option value="2" @if(isset($result) && $result['status'] == 2) selected @endif>已结束</option>--}}
{{--					</select>--}}
{{--				</div>--}}
{{--			</div>--}}
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">备注</label>
			<div class="layui-input-block">
				<textarea placeholder="请输入备注" class="layui-textarea" name="summary">@if (isset($result['summary'])){{$result['summary']}}@endif</textarea>
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ URL('vendor/ueditor/1.4.3/ueditor.config.js') }}"></script>
<script type="text/javascript" src="{{ URL('vendor/ueditor/1.4.3/ueditor.all.js') }}"> </script>
<script type="text/javascript" src="{{ URL('vendor/ueditor/1.4.3/lang/zh-cn/zh-cn.js') }}"></script>
<script type="text/javascript" src="{{URL("/admin/js/new_currency.js?v=").time()}}"></script>

<script>
	var list = "{{ json_encode($currency) }}";
			console.log(list);
	layui.use(['form','laydate'],function () {
		var form = layui.form
				,$ = layui.jquery
				,laydate = layui.laydate
				,index = parent.layer.getFrameIndex(window.name);
		//监听提交
		form.on('submit(submit)', function(data){
			var data = data.field;
			$.ajax({
				url:'{{url('admin/new_currency/postAdd')}}'
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
		form.on('select(select_act)', function (data) {
			$.ajax({
				url: '{{url('admin/new_currency/price')}}'
				,type:'post'
				,dataType:'json'
				,data: {currency_id:data.value},
				success: function (data) {
					if(data.type =="ok"){
						$("#price").val(data.message.price);
					}else{
						layer.msg(data.message);
					}
				}
			});
			form.render(); //重新渲染显示效果

		});
	});

	$(function () {
		var currery_id = $("#currey_id").val();
		if(currery_id){
			$.ajax({
				url: '{{url('admin/new_currency/price')}}'
				,type:'post'
				,dataType:'json'
				,data: {currency_id:currery_id},
				success: function (data) {
					if(data.type =="ok"){
						$("#price").val(data.message.price);
					}else{
						layer.msg(data.message);
					}
				}
			});
		}
	});
</script>
@endsection