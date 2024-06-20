<?php

use App\Models\UsersWallet;
use Illuminate\Http\Request;

Route::group(['middleware' => ['lang','xss']], function () {
    Route::any('api/area_code', 'Api\CurrencyController@area_code');//国家区号
    Route::post('api/set/lang', 'Api\DefaultController@language');
    Route::any('api/app_setting', 'Api\DefaultController@app_setting');
    Route::any('api/checklogin', 'Api\DefaultController@checklogin');
    Route::any('api/agent_kefu', 'Api\DefaultController@agent_kefu');

    Route::post('api/user/check_email', 'Api\LoginController@checkEmailCode');//验证邮件验证码
    Route::post('api/user/register', 'Api\LoginController@register');//注册
    Route::post('api/user/forget', 'Api\LoginController@forgetPassword');//忘记密码
    Route::post('api/user/login', 'Api\LoginController@login');//登录
    Route::post('api/news/list', 'Api\NewsController@getArticle');//获取文章列表
    Route::post('api/news/detail', 'Api\NewsController@get');//获取文章详情
    Route::post('api/news/getNewsByCatName', 'Api\NewsController@getNewsByCatName');//通过分类名获取信息
    Route::any('api/currency/quotation_new', 'Api\CurrencyController@newQuotation'); //币种列表带行情(支持交易对)
    Route::any('api/currency/new_timeshar', 'Api\CurrencyController@klineMarket')->middleware(['cross']); //K线分时数据，对接tradeingview
    Route::any('api/upload', 'Api\DefaultController@upload');//上传图片接口
    Route::get('api/wallet/flashAgainstList', 'Api\WalletController@flashAgainstList'); //兑换列表
    Route::any('api/setwallet', 'Api\DefaultController@setwallet');//上传图片接口

    Route::prefix('api')->post('user/real_name', 'Api\UserController@realName')->middleware([
        'demo_limit',
        'check_api'
    ]);//身份认证
    Route::prefix('api')->post('user/del_mark', 'Api\LoginController@del_mark');
    Route::prefix('api')->post('user/del_que', 'Api\LoginController@del_que');
    Route::prefix('api')->post('user/authLow', 'Api\UserController@authLow')->middleware([
        'demo_limit',
        'check_api'
    ]);// 初级认证

    Route::prefix('api')->post('user/authHigh', 'Api\UserController@authHigh')->middleware([
        'demo_limit',
        'check_api'
    ]);//高级认证
    Route::get('/login', 'Admin\DefaultController@login_index');
    Route::get('/admin', function () {
        return redirect('/login');
    });
    Route::prefix('admin')->any('check_google_key', 'Admin\DefaultController@check_google_key');
    Route::prefix('admin')->any('set_google_key', 'Admin\DefaultController@set_google_key');

    Route::get('/error', function () {
        return view('errors/403');
    });
});
Route::group(['prefix' => 'api', 'middleware' => ['check_api', 'xss' /*'check_user'*/]], function () {
    Route::get('user/info', 'Api\UserController@info');//我的
    Route::get('user/center', 'Api\UserController@userCenter');//个人中心
    Route::get('user/myinvite', 'Api\UserController@myInvite');//我的分享
    Route::any('user/rebate_flow', 'Api\UserController@rebate_flow');//返佣记录
    Route::any('user/rebate_info', 'Api\UserController@rebate_info');//裂变信息
    Route::any('user/currery_info', 'Api\UserController@currery_info');
    Route::any('user/set_pay_password', 'Api\UserController@set_pay_password');//设置资金密码
    Route::any('user/is_set_pay_pass', 'Api\UserController@is_set_pay_pass');//是否设置资金密码
    Route::any('user/update_password', 'Api\UserController@update_password');
    Route::get('mining', 'Api\UserController@mining'); //绑定邮箱
    // 质押配置
    Route::post('deposit/config', 'Api\DepositController@config');
    Route::post('deposit/detail', 'Api\DepositController@detail');
    Route::post('deposit/depositDetail', 'Api\DepositController@depositDetail');
    Route::post('deposit/order', 'Api\DepositController@order');
    Route::post('deposit/cancel', 'Api\DepositController@cancel');
    Route::post('deposit/buy', 'Api\DepositController@buy');
    Route::post('deposit/census', 'Api\DepositController@census');

    // IEO
    Route::post('project/lists', 'Api\NewCurrencyController@lists');
    Route::post('project/detail', 'Api\NewCurrencyController@detail');
    Route::post('project/buy', 'Api\NewCurrencyController@buy');
    Route::post('project/myOrder', 'Api\NewCurrencyController@myOrder');
    Route::any('charge_mention/log', 'Api\AccountController@chargeMentionMoney'); //充提记录
    Route::post('lever/my_trade', 'Api\LeverController@myTrade'); //我的交易记录

    //秒合约路由
    Route::get('microtrade/payable_currencies', 'Api\MicroOrderController@getPayableCurrencies'); //可支付的币种列表
    Route::get('microtrade/seconds', 'Api\MicroOrderController@getSeconds'); //到期时间
    Route::post('microtrade/submit', 'Api\MicroOrderController@submit')->middleware('validate_locked'); //提交下单
    Route::get('microtrade/lists', 'Api\MicroOrderController@lists')->middleware('validate_locked'); //下单记录
    Route::post('microtrade/get_mico_price', 'Api\MicroOrderController@get_mico_price')->middleware('validate_locked'); //http拉取时时价格
    Route::post('microtrade/get_mico_end', 'Api\MicroOrderController@get_mico_end')->middleware('validate_locked'); //http拉取订单
    Route::get('microtrade/change_pankou', 'Api\MicroOrderController@changePankou'); //修改盘口
    Route::get('microtrade/get_pankou', 'Api\MicroOrderController@getPanKou'); //获取盘口


    //资产
    Route::post('wallet/list', 'Api\WalletController@walletList');//用户账户资产信息
    Route::post('wallet/get_in_address', 'Api\WalletController@getWalletAddressIn')->middleware(['demo_limit']);//充币地址
    Route::any('wallet/get_address', 'Api\WalletController@getWalletAddress');//充币列表
    Route::post('wallet/charge_req', 'Api\WalletController@chargeReq');
    Route::post('wallet/get_info', 'Api\WalletController@getCurrencyInfo');//获取提币信息
    Route::post('wallet/out', 'Api\WalletController@postWalletOut')->middleware([
        'demo_limit',
        'validate_locked',
        'lever_hold_check'
    ]);//提交提币信息
    Route::post('wallet/detail', 'Api\WalletController@getWalletDetail');//用户账户资产详情
    Route::any('wallet/legal_log', 'Api\WalletController@legalLog');//财务记录
    Route::post('wallet/flashAgainst', 'Api\WalletController@flashAgainst')->middleware('validate_locked');//闪兑
    Route::get('wallet/myFlashAgainstList', 'Api\WalletController@myFlashAgainstList'); //我的闪兑列表
    //反馈建议
    Route::post('feedback/list', 'Api\FeedBackController@myFeedBackList');//反馈信息列表
    Route::post('feedback/detail', 'Api\FeedBackController@feedBackDetail');//反馈信息内容，包括回复信息
    Route::post('feedback/add', 'Api\FeedBackController@feedBackAdd');//添加反馈信息

    //交易记录
    Route::post('transaction_in', 'Api\TransactionController@TransactionInList');
    Route::post('transaction_out', 'Api\TransactionController@TransactionOutList');
    Route::post('transaction_complete', 'Api\TransactionController@TransactionCompleteList');
    Route::post('transaction_del', 'Api\TransactionController@TransactionDel');//取消交易
    Route::post('transaction/revoke', 'Api\TransactionController@revoke');//撤销委托

    //交易记录
    Route::post('transaction_in', 'Api\TransactionController@TransactionInList');
    Route::post('transaction_out', 'Api\TransactionController@TransactionOutList');
    Route::post('transaction_complete', 'Api\TransactionController@TransactionCompleteList');
    Route::post('transaction_del', 'Api\TransactionController@TransactionDel');//取消交易
    Route::post('transaction/deal', 'Api\TransactionController@deal');//deal
    Route::post('transaction/in', 'Api\TransactionController@in')->middleware('validate_locked');//买入
    Route::post('transaction/out', 'Api\TransactionController@out')->middleware('validate_locked');//卖出
    Route::post('lever/deal', 'Api\LeverController@deal'); //杠杆deal
    Route::post('lever/dealall', 'Api\LeverController@dealAll'); //杠杆全部
    Route::post('lever/submit',
        ['uses' => 'Api\LeverController@submit', 'middleware' => ['validate_locked']]); //杠杆下单
    Route::post('lever/close', ['uses' => 'Api\LeverController@close', 'middleware' => ['validate_locked']]); //杠杆平仓
    Route::post('lever/cancel', ['uses' => 'Api\LeverController@cancelTrade', 'middleware' => ['validate_locked']]); //撤销委托(取消)
    Route::post('lever/batch_close', ['uses' => 'Api\LeverController@batchCloseByType', 'middleware' => ['validate_locked']]); //一键平仓
    Route::post('lever/setstop', 'Api\LeverController@setStopPrice'); //设置止盈止损价
    Route::post('lever/my_trade', 'Api\LeverController@myTrade'); //我的交易记录
    Route::post('order/charge_list', 'Api\OrderController@charge_list'); //我的充值订单记录
    Route::post('order/wallet_out_list', 'Api\OrderController@wallet_out_list'); //我的提币订单记录

});
Route::post('/api/send_mail', 'Api\UserController@sendMail'); // 发送邮件
Route::post('/api/check_code', 'Api\UserController@checkCode'); // 发送邮件
Route::post('/admin/login', 'Admin\DefaultController@login');
Route::group(['prefix' => 'winadmin', 'middleware' => ['admin_auth']], function () {
    Route::get('/index', 'Admin\DefaultController@index');
});
Route::group(['prefix' => 'admin', 'middleware' => ['admin_auth']], function () {
    //后台管理员
    Route::get('manager/manager_index', function () {
        return view('admin.manager.index');
    });
    Route::get('test/post', 'Admin\TestController@post');//
    Route::get('manager/users', 'Admin\AdminController@users');
    Route::get('manager/add', 'Admin\AdminController@add');//添加管理员
    Route::post('manager/add', 'Admin\AdminController@postAdd');//添加管理员
    Route::post('manager/delete', 'Admin\AdminController@del');//删除管理员
    Route::get('manager/manager_roles', function () {
        return view('admin.manager.admin_roles');
    });//角色管理
    Route::get('manager/manager_roles_api', 'Admin\AdminRoleController@users');
    Route::get('manager/role_add', 'Admin\AdminRoleController@add');
    Route::post('manager/role_add', 'Admin\AdminRoleController@postAdd');
    Route::post('manager/role_delete', 'Admin\AdminRoleController@del');
    Route::get('manager/role_permission', 'Admin\AdminRolePermissionController@update');
    Route::post('manager/role_permission', 'Admin\AdminRolePermissionController@postUpdate');
    Route::get('/transaction/tran_index', 'Admin\TransactionController@index');
    Route::get('/transaction/list', 'Admin\TransactionController@lists');

    //系统充值地址
    Route::group(['prefix' => 'system_wallet'], function () {
        Route::get('list', 'Admin\SystemWalletController@list');//系统充值地址
        Route::any('list_data', 'Admin\SystemWalletController@listData');
        Route::any('add', 'Admin\SystemWalletController@add');
        Route::any('edit', 'Admin\SystemWalletController@edit');
        Route::post('delete', 'Admin\SystemWalletController@delete');
    });
    //质押订单
    Route::group(['prefix' => 'deposit_order'], function () {
        Route::get('list', 'Admin\DepositOrderController@list');
        Route::any('list_data', 'Admin\DepositOrderController@listData');
        Route::any('edit', 'Admin\DepositOrderController@edit');
        Route::any('postEdit', 'Admin\DepositOrderController@postEdit');
        Route::any('end_order', 'Admin\DepositOrderController@end_order');
    });
    // 质押配置
    Route::group(['prefix' => 'deposit'], function () {
        Route::get('config/view', 'Admin\DepositController@index');
        Route::get('lists', 'Admin\DepositController@lists');
        Route::get('add', 'Admin\DepositController@add');
        Route::post('postAdd', 'Admin\DepositController@postAdd');
        Route::post('del', 'Admin\DepositController@del');
    });
    // IEO
    Route::group(['prefix' => 'new_currency'], function () {
        Route::post('postAdd', 'Admin\NewCurrencyController@postAdd');
        Route::get('add', 'Admin\NewCurrencyController@add');
        Route::get('index', 'Admin\NewCurrencyController@index');
        Route::get('lists', 'Admin\NewCurrencyController@lists');
        Route::get('order', 'Admin\NewCurrencyController@order');
        Route::get('getOrder', 'Admin\NewCurrencyController@getOrder');
        Route::get('passView', 'Admin\NewCurrencyController@passView');
        Route::post('refuse', 'Admin\NewCurrencyController@refuse');
        Route::post('pass', 'Admin\NewCurrencyController@pass');
        Route::post('del', 'Admin\NewCurrencyController@del');
        Route::post('price', 'Admin\NewCurrencyController@get_price');
    });
    Route::post('main_info', 'Admin\MainController@info');//列表
    //系统设置
    Route::get('setting/index', 'Admin\SettingController@index');//设置首页
    Route::get('setting/commission', 'Admin\SettingController@commission');//设置首页
    Route::get('setting/list', 'Admin\SettingController@list');//设置首页
    Route::get('setting/add', 'Admin\SettingController@add');//设置奖金
    Route::post('setting/postadd', 'Admin\SettingController@postAdd');//设置奖金
    Route::get('setting/set_base', 'Admin\SettingController@base');//基础设置
    Route::post('setting/basesite', 'Admin\SettingController@setBase');//提交基础设置
    Route::get('setting/data/index', 'Admin\SettingController@dataSetting');//提交基础设置

    Route::get('app_setting/index', 'Admin\AppSettingController@index');//
    Route::post('app_setting/postadd', 'Admin\AppSettingController@postAdd');//
    Route::any('app_setting/step_code', 'Admin\AppSettingController@step_code');
    //投诉建议
    Route::get('feedback/detail', 'Admin\FeedBackController@feedBackDetail');
    Route::get('feedback/del', 'Admin\FeedBackController@feedBackDel');
    Route::post('feedback/reply', 'Admin\FeedBackController@reply');
    Route::get('feedback/index', 'Admin\FeedBackController@index');
    Route::get('feedback/list', 'Admin\FeedBackController@feedbackList');
    Route::get('feedback/csv', 'Admin\FeedBackController@csv')->middleware(['demo_limit']);
    Route::get('user/user_index', 'Admin\UserController@index');
    Route::get('user/list', 'Admin\UserController@lists');
    Route::get('user/users_wallet', 'Admin\UserController@wallet');
    Route::get('user/walletList', 'Admin\UserController@walletList');
    Route::post('user/wallet_lock', 'Admin\UserController@walletLock');//钱包锁定
    Route::get('user/conf', 'Admin\UserController@conf');
    Route::post('user/conf', 'Admin\UserController@postConf');//调节钱包账户
    Route::post('user/del', 'Admin\UserController@del')->middleware(['demo_limit']); //删除用户
    Route::post('user/delw', 'Admin\UserController@delw')->middleware(['demo_limit']); //删除指定id钱包
    Route::get('user/lock', 'Admin\UserController@lockUser');
    Route::post('user/lock', 'Admin\UserController@dolock');
    Route::post('user/bind', 'Admin\UserController@bind');
    Route::post('user/bind_email', 'Admin\UserController@bind_email');
    Route::post('user/score', 'Admin\UserController@score');
    Route::post('user/update_pass', 'Admin\UserController@update_pass');
    Route::post('user/update_pay_pass', 'Admin\UserController@update_pay_pass');
    Route::get('user/edit', 'Admin\UserController@edit');
    Route::get('user/changepan', 'Admin\UserController@changepan');
    Route::post('user/dochangepan', 'Admin\UserController@dochangepan');
    Route::post('user/edit', 'Admin\UserController@doedit');
    Route::post('user/real_user', 'Admin\UserController@real_user');
    Route::post('user/gold', 'Admin\UserController@gold');
    Route::post('user/withdraw', 'Admin\UserController@withdraw');
    Route::any('user/is_atelier', 'Admin\UserController@is_atelier');
    //初级认证
    Route::get('user/real_index', 'Admin\UserRealController@index');
    Route::get('user/real_list', 'Admin\UserRealController@list');
    Route::get('user/real_info', 'Admin\UserRealController@detail');
    Route::post('user/real_info', 'Admin\UserRealController@detail');
    Route::post('user/real_del', 'Admin\UserRealController@del');
    Route::post('user/real_auth', 'Admin\UserRealController@auth');
    Route::any('user/add_user', 'Admin\UserController@add_user');
    Route::any('user/do_add_user', 'Admin\UserController@do_add_user');

    // 高级认证
    Route::get('user2/real_index', 'Admin\UserReal2Controller@index');
    Route::get('user2/real_list', 'Admin\UserReal2Controller@list');
    Route::get('user2/real_info', 'Admin\UserReal2Controller@detail');
    Route::post('user2/real_del', 'Admin\UserReal2Controller@del');
    Route::post('user2/real_auth', 'Admin\UserReal2Controller@auth');

    //新闻路由
    Route::get('news_index', 'Admin\NewsController@index');
    Route::get('news_add', 'Admin\NewsController@add');
    Route::post('news_add', 'Admin\NewsController@postAdd');
    Route::get('news_edit/{id}', 'Admin\NewsController@edit');
    Route::post('news_edit/{id}', 'Admin\NewsController@postEdit');
    Route::get('news_del/{id}/{togetherDel?}', 'Admin\NewsController@del');
    //新闻分类路由
    Route::get('news_cate_index', 'Admin\NewsController@cateIndex');
    Route::get('news_cate_add', 'Admin\NewsController@cateAdd');
    Route::get('news_cate_list', 'Admin\NewsController@getCateList');
    Route::post('news_cate_add', 'Admin\NewsController@postCateAdd');
    Route::get('news_cate_edit/{id}', 'Admin\NewsController@cateEdit');
    Route::post('news_cate_edit/{id}', 'Admin\NewsController@postCateEdit');
    Route::get('news_cate_del/{id}', 'Admin\NewsController@cateDel');

    Route::get('account/account_index', 'Admin\AccountLogController@index');
    Route::get('account/list', 'Admin\AccountLogController@lists');
    Route::get('account/viewDetail', 'Admin\AccountLogController@view');
    Route::get('currency', 'Admin\CurrencyController@index');//首页
    Route::post('is_insurancable', 'Admin\CurrencyController@isInsurancable');
    Route::get('currency_add', 'Admin\CurrencyController@add')->middleware(['demo_limit']);//添加币种
    Route::post('currency_add', 'Admin\CurrencyController@postAdd')->middleware(['demo_limit']);//添加币种
    Route::get('currency_list', 'Admin\CurrencyController@lists');//币种
    Route::post('currency_del', 'Admin\CurrencyController@delete')->middleware(['demo_limit']);//删除币种
    Route::post('currency_display', 'Admin\CurrencyController@isDisplay');//币种显示
    Route::post('currency_execute', 'Admin\CurrencyController@executeCurrency');//币种显示
    Route::get('currency/match/{legal_id}', 'Admin\CurrencyController@match'); //交易对
    Route::get('currency/match_list/{legal_id}', 'Admin\CurrencyController@matchList'); //交易对列表
    Route::get('currency/match_add/{legal_id}', 'Admin\CurrencyController@addMatch'); //添加交易对页
    Route::post('currency/match_add/{legal_id}',
        'Admin\CurrencyController@postAddMatch')->middleware(['demo_limit']); //添加交易对
    Route::get('currency/match_edit/{id}', 'Admin\CurrencyController@editMatch'); //编辑交易对页
    Route::post('currency/match_edit/{id}', 'Admin\CurrencyController@postEditMatch'); //编辑交易对
    Route::any('currency/match_del/{id}', 'Admin\CurrencyController@delMatch')->middleware(['demo_limit']); //删除交易对
    Route::get('currency/micro_match', 'Admin\CurrencyController@microMatch'); //微交易交易对
    Route::get('currency/micro_match_list', 'Admin\CurrencyController@microMatchList'); //微交易交易对
    Route::post('currency/micro_risk', 'Admin\CurrencyController@microRisk');
    //提币
    Route::get('cashb', 'Admin\CashbController@index')->middleware(['demo_limit']);
    Route::get('cashb_list', 'Admin\CashbController@cashbList');
    Route::get('cashb_show', 'Admin\CashbController@show')->middleware(['demo_limit']);//提币详情页面
    Route::post('cashb_done', 'Admin\CashbController@done')->middleware(['demo_limit']);//确认提币成功
    Route::any('cashb_back', 'Admin\CashbController@back')->middleware(['demo_limit']);//执行退回申请
    Route::get('cashb_csv', 'Admin\CashbController@csv');
    Route::any('edit_adress', 'Admin\CashbController@edit_adress');
    //新充值
    Route::get('user/charge_req', 'Admin\UserController@chargeReq');//提币申请
    Route::get('user/charge_csv', 'Admin\UserController@chargeCsv');//提币申请
    Route::get('user/charge_list', 'Admin\UserController@chargeList');
    Route::post('user/pass_req', 'Admin\UserController@passReq');
    Route::post('user/refuse_req', 'Admin\UserController@refuseReq');
    Route::any('user/req_show', 'Admin\UserController@req_show');
    Route::any('user/auth', 'Admin\UserController@auth');

    //闪兑
    Route::get('flashagainst/index', 'Admin\FlashAgainstController@index');
    Route::get('flashagainst/list', 'Admin\FlashAgainstController@lists');
    Route::post('flashagainst/affirm', 'Admin\FlashAgainstController@affirm');
    Route::post('flashagainst/reject', 'Admin\FlashAgainstController@reject');

    //秒合约日志
    Route::get('micro_order', 'Admin\MicroController@order');
    Route::get('micro_order_list', 'Admin\MicroController@orderList');
    Route::get('micro_order_edit', 'Admin\MicroController@edit');
    Route::post('micro_order_edit', 'Admin\MicroController@editPost');
    Route::post('micro/batch_risk', 'Admin\MicroController@batchRisk');

    //邀请返佣
    Route::get('invite/account_return', 'Admin\InviteController@return');//邀请返佣
    Route::get('invite/return_list', 'Admin\InviteController@returnList');//邀请返佣列表
    Route::get('invite/childs', 'Admin\InviteController@childs');//会员邀请关系图
    Route::get('invite/share', 'Admin\InviteController@share');//邀请分享设置
    Route::post('invite/share', 'Admin\InviteController@postShare');//邀请分享设置提交
    Route::get('invite/getTree', 'Admin\InviteController@getTree');//
    Route::post('invite/del', 'Admin\InviteController@del');
    Route::get('invite/edit', 'Admin\InviteController@edit');
    Route::post('invite/edit', 'Admin\InviteController@doedit');
    Route::post('invite/bgdel', 'Admin\InviteController@bgdel');

    Route::any('exchange/index', 'Admin\Exchange@index');
    Route::get('user/quotation', 'Admin\UserController@quotation');
    Route::post('user/quotation', 'Admin\UserController@saveQuotation');
    Route::post('needle/add', function (Request $request) {
        $needle = new \App\Models\Needle();
        $needle->open = $request->input('open');
        $needle->close = $request->input('close');
        $needle->high = $request->input('high');
        $needle->low = $request->input('low');
        $needle->base = $request->input('base');
        $needle->target = $request->input('target');
        $needle->symbol = "{$needle->base}/{$needle->target}";
        $needle->itime = $request->input('itime');
        $needle->save();
        return response()->json(['type' => 'ok', 'message' => '添加成功']);
    });
    Route::delete('needle/del', function (Request $request) {
        \App\Models\Needle::find($request->input('id'))->delete();
        return response()->json(['type' => 'ok', 'message' => '添加成功']);
    });
    Route::any('needle/all_needle', 'Admin\Needle@index');
    Route::any('myquotation/all', 'Admin\MyQuotation@index');
    Route::get('myquotation/list', 'Admin\MyQuotation@lists');
    Route::post('myquotation/delete', 'Admin\MyQuotation@delete');
    Route::get('myquotation/reset', 'Admin\MyQuotation@reset');
    Route::get('needle/add', function () {
        return view('admin.needle.add');
    });
    //下单机器人
    Route::any('robot/add', 'Admin\RobotController@add');//添加机器人
    Route::any('robot/list', 'Admin\RobotController@list');//机器人列表
    Route::any('robot/list_data', 'Admin\RobotController@listData');//机器人列表
    Route::any('robot/delete', 'Admin\RobotController@delete');//删除机器人
    Route::any('robot/start', 'Admin\RobotController@start');//开启关闭机器人

    Route::any('robot/sche', 'Admin\RobotController@sche');//添加机器人
    Route::any('robot/sche_data', 'Admin\RobotController@scheData');//机器人列表
    Route::any('robot/sche_add', 'Admin\RobotController@scheAdd');//添加机器人
    Route::any('robot/sche_delete', 'Admin\RobotController@scheDelete');//添加机器人

    //秒合约秒数设置
    Route::get('/micro_seconds_index', function () {
        return view('admin.micro.seconds_index2', ['type' => request()->input('type', 1)]);
    });
    Route::get('micro_seconds_add', 'Admin\MicroController@secondsAdd');//添加设置
    Route::post('micro_seconds_add', 'Admin\MicroController@secondsPostAdd');//添加设置
    Route::get('micro_seconds_list', 'Admin\MicroController@secondsLists');
    Route::post('micro_seconds_status', 'Admin\MicroController@secondsStatus');
    Route::post('micro_seconds_del', 'Admin\MicroController@secondsDel');
    //秒合约日志
    Route::get('micro_order', 'Admin\MicroController@order');
    Route::get('micro_order_list', 'Admin\MicroController@orderList');
    Route::get('micro_order_edit', 'Admin\MicroController@edit');
    Route::post('micro_order_edit', 'Admin\MicroController@editPost');
    Route::post('micro/batch_risk', 'Admin\MicroController@batchRisk');

    //秒合约数量设置
    Route::get('micro_number_index', function () {
        return view('admin.micro.index');
    });
    Route::get('micro_number_add', 'Admin\MicroController@add');//添加设置
    Route::post('micro_number_add', 'Admin\MicroController@postAdd');//添加设置
    Route::get('micro_numbers_list', 'Admin\MicroController@lists');
    Route::post('micro_number_del', 'Admin\MicroController@del');
    //保险设置
    Route::get('/insurance_rules_index', function () {
        return view('admin.insurancerule.index');
    });
    Route::get('insurance_rules_add', 'Admin\InsuranceRuleController@add');//添加设置
    Route::post('insurance_rules_add', 'Admin\InsuranceRuleController@postAdd');//添加设置
    Route::get('insurance_rules_list', 'Admin\InsuranceRuleController@lists');
    Route::post('insurance_rules_del', 'Admin\InsuranceRuleController@del');
    Route::any('user/charge_req', 'Admin\UserController@chargeReq');//提币申请
    Route::any('email_code/index', 'Admin\EmailCodeController@index');
    Route::any('email_code/lists', 'Admin\EmailCodeController@lists');

    Route::any('daily_report/index', 'Admin\DailyReportController@index');
    Route::any('daily_report/list', 'Admin\DailyReportController@lists');

    Route::any('admin_ip/index', 'Admin\AdminIpController@index');
    Route::any('admin_ip/list', 'Admin\AdminIpController@lists');
    Route::any('admin_ip/add', 'Admin\AdminIpController@add');
    Route::any('admin_ip/postadd', 'Admin\AdminIpController@postAdd');
    Route::any('admin_ip/del', 'Admin\AdminIpController@del');
    Route::any('user_gold/list', 'Admin\UserGoldController@list');
    Route::any('user_gold/list_data', 'Admin\UserGoldController@listData');
    Route::post('dgx_upload', 'Admin\DefaultController@dgx_upload');
    Route::any('agent/index', 'Admin\AgentController@index');
    Route::any('agent/lists', 'Admin\AgentController@lists');
    Route::any('agent/add', 'Admin\AgentController@add');
    Route::any('agent/postAdd', 'Admin\AgentController@postAdd');
    Route::any('agent/del', 'Admin\AgentController@del');
    Route::any('exception/list', 'Admin\ExceptionController@list');

});
