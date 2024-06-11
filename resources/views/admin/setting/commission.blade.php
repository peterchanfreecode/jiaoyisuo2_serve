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
                    <li hidden>奖励设置</li>
                    <li class="layui-this">邀请奖励</li>
                    <li>裂变抽佣</li>
                    <li>新币裂变配置</li>
                </ul>
                <div class="layui-tab-content">
                    <!--通证设置开始-->
                    <div class="layui-tab-item">
                        <div class="layui-form-item">
                            <label class="layui-form-label">历史盈亏释放比例</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <input type="text" name="profit_loss_release" class="layui-input" value="{{$setting['profit_loss_release'] ?? 0 }}" placeholder="通证兑换USDT比例设置">
                                </div>
                                <div class="layui-form-mid layui-word-aux">千分比例</div>
                                <div class="layui-form-mid layui-word-aux">历史盈亏释放比例</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">通证兑换USDT比例</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <input type="text" name="candy_tousdt" class="layui-input" value="{{$setting['candy_tousdt'] ?? 0 }}" placeholder="通证兑换USDT比例设置">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%</div>
                                <div class="layui-form-mid layui-word-aux">1通证能兑换多少USDT</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">手续费结算比例</label>
                            <div class="layui-input-block">
                            @include('admin.setting.lever_tradefee_settle')
                            </div>
                        </div>

                        <div class="layui-form-item" hidden>
                            <label class="layui-form-label">手续费结算笔数要求</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <input type="text" name="lever_fee_need_trades" class="layui-input" value="{{$setting['lever_fee_need_trades'] ?? ''}}" placeholder="各级手续费结算比例">
                                </div>
                                <div class="layui-form-mid layui-word-aux"></div>
                                <div class="layui-form-mid layui-word-aux">拿手续费的用户自身最低交易笔数,符合条件才能拿到奖励,用英文逗号分隔,例如:1,2,3</div>
                            </div>
                        </div>
                        <div class="layui-form-item" hidden>
                            <label class="layui-form-label">手续费结算各级比例</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <input type="text" name="lever_fee_reward_ratio" class="layui-input" value="{{$setting['lever_fee_reward_ratio'] ?? '' }}" placeholder="各级手续费结算比例">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%</div>
                                <div class="layui-form-mid layui-word-aux">每级比例用英文逗号分隔,例如:8,2,5</div>
                            </div>
                        </div>
                    </div>

                    <!-- 邀请奖励 -->
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form-item">
                            <label class="layui-form-label">第1级</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="invite_0_min" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_0_min'])){{$setting['invite_0_min']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">至(含)</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_0_max" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_0_max'])){{$setting['invite_0_max']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">每人</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_0_price" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_0_price'])){{$setting['invite_0_price']}}@endif">
                                </div>
                                <div class="layui-form-mid">美元</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第2级</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="invite_1_min" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_1_min'])){{$setting['invite_1_min']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">至(含)</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_1_max" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_1_max'])){{$setting['invite_1_max']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">每人</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_1_price" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_1_price'])){{$setting['invite_1_price']}}@endif">
                                </div>
                                <div class="layui-form-mid">美元</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第3级</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="invite_2_min" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_2_min'])){{$setting['invite_2_min']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">至(含)</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_2_max" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_2_max'])){{$setting['invite_2_max']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">每人</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_2_price" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_2_price'])){{$setting['invite_2_price']}}@endif">
                                </div>
                                <div class="layui-form-mid">美元</div>
                            </div>
                        </div>                        
                        <div class="layui-form-item">
                            <label class="layui-form-label">第4级</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="invite_3_min" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_3_min'])){{$setting['invite_3_min']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">至(含)</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_3_max" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_3_max'])){{$setting['invite_3_max']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">每人</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="invite_3_price" autocomplete="off" class="layui-input" value="@if(isset($setting['invite_3_price'])){{$setting['invite_3_price']}}@endif">
                                </div>
                                <div class="layui-form-mid">美元</div>
                            </div>
                        </div>

                    </div>

                    <!--裂变抽佣设置开始-->
                    <div class="layui-tab-item">
                        <div class="layui-form-item">
                            <label class="layui-form-label">第1级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">抽佣比例</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="one_level_rebate_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['one_level_rebate_rate'])){{$setting['one_level_rebate_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="one_level_rebate_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['one_level_rebate_lower_num'])){{$setting['one_level_rebate_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第2级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">抽佣比例</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="two_level_rebate_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['two_level_rebate_rate'])){{$setting['two_level_rebate_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="two_level_rebate_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['two_level_rebate_lower_num'])){{$setting['two_level_rebate_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人</div>

                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第3级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">抽佣比例</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="three_level_rebate_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['three_level_rebate_rate'])){{$setting['three_level_rebate_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="three_level_rebate_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['three_level_rebate_lower_num'])){{$setting['three_level_rebate_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人</div>
                            </div>
                        </div>
                    </div>

                    <!--新币裂变设置开始-->
                    <div class="layui-tab-item">
                        <div class="layui-form-item">
                            <label class="layui-form-label">第1级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="one_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['one_level_lottery_rate'])){{$setting['one_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="one_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['one_level_lottery_lower_num'])){{$setting['one_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="one_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['one_level_lottery_currery'])){{$setting['one_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第2级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="two_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['two_level_lottery_rate'])){{$setting['two_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="two_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['two_level_lottery_lower_num'])){{$setting['two_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="two_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['two_level_lottery_currery'])){{$setting['two_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第3级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="three_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['three_level_lottery_rate'])){{$setting['three_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="three_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['three_level_lottery_lower_num'])){{$setting['three_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="three_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['three_level_lottery_currery'])){{$setting['three_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
<!--                        <div class="layui-form-item">
                            <label class="layui-form-label">第4级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="four_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['four_level_lottery_rate'])){{$setting['four_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="four_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['four_level_lottery_lower_num'])){{$setting['four_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="four_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['four_level_lottery_currery'])){{$setting['four_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第5级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="five_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['five_level_lottery_rate'])){{$setting['five_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="five_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['five_level_lottery_lower_num'])){{$setting['five_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="five_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['five_level_lottery_currery'])){{$setting['five_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第6级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="six_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['six_level_lottery_rate'])){{$setting['six_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="six_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['six_level_lottery_lower_num'])){{$setting['six_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="six_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['six_level_lottery_currery'])){{$setting['six_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">第7级</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">中签率增加</div>
                                <div class="layui-input-inline" style="width:65px;">
                                   <input type="number" name="seven_level_lottery_rate" autocomplete="off" class="layui-input" value="@if(isset($setting['seven_level_lottery_rate'])){{$setting['seven_level_lottery_rate']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">%(百分比),三级内代理人数</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="seven_level_lottery_lower_num" autocomplete="off" class="layui-input" value="@if(isset($setting['seven_level_lottery_lower_num'])){{$setting['seven_level_lottery_lower_num']}}@endif">
                                </div>
                                <div class="layui-form-mid layui-word-aux">人,三级内参加新币人数要求</div>
                                <div class="layui-input-inline" style="width:65px;">
                                    <input type="number" name="seven_level_lottery_currery" autocomplete="off" class="layui-input" value="@if(isset($setting['seven_level_lottery_currery'])){{$setting['seven_level_lottery_currery']}}@endif">
                                </div>
                                <div class="layui-form-mid">人</div>
                            </div>
                        </div>-->
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
        layui.use(['element', 'form', 'upload', 'layer', 'laydate'], function () {
            var element = layui.element
                ,layer = layui.layer
                ,form = layui.form
                ,laydate = layui.laydate
                ,$ = layui.$;

            form.on('submit(website_submit)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '/admin/setting/postadd',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.msg(res.message);
                    }
                });
                return false;
            });

            $('.layui-date').each(function (index, element) {
                //console.log(element)
                laydate.render({ 
                    elem: element
                    ,type: 'time'
                    ,format: 'HH:mm'
                });
            });
            
            var template = `
                <tr>
                    <td>
                        <div class="layui-inline">
                            <div class="layui-input-inline" style="width: 90px;">
                                <input class="layui-input" name="generation[]" value="" required lay-verify="required">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="layui-inline">
                            <div class="layui-input-inline" style="width: 90px;">
                                <input class="layui-input" name="reward_ratio[]" value="" required lay-verify="required">
                            </div>
                            <div class="layui-form-mid">
                                <span>%</span></div>
                            </div>
                        </td>
                        <td>
                            <div class="layui-input-inline" style="width: 90px;">
                                <input class="layui-input" name="need_has_trades[]" value="" required lay-verify="required">
                            </div>
                        </td>
                        <td>
                            <div class="layui-input-inline">
                            <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" lay-event="del">删除</button>
                            </div>
                    </td>
                </tr>`;
            $('#addLeverTradeOption').click(function () {
                $('#leverTradeFeeOption').append(template);
            });
            $('#leverTradeFeeOption').on('click', 'button[lay-event]', function () {
                var that = this
                    ,event = $(this).attr('lay-event')
                if (event == 'del') {
                    layer.confirm('真的确定要删除吗?' , {
                        title: '删除确认'
                        ,icon: 3
                    }, function (index) {
                        $(that).parent().parent().parent().remove();
                        layer.close(index);
                    });
                }
            });
        });
    </script>
@stop