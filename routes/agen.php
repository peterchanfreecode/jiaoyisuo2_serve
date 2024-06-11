<?php

Route::get('agen/index', 'Agen\AgentController@index');//首页
Route::get('agen', 'Agen\AgentController@login');//登录页
Route::post('agen/login', 'Agen\MemberController@login');//登录
Route::any('agen/order_excel', 'Agen\OrderController@order_excel');//导出订单记录Excel
Route::any('agen/users_excel', 'Agen\OrderController@user_excel');//导出用户记录Excel
Route::any('agen/dojie', 'Agen\ReportController@dojie');//阶段订单图表

//管理后台
Route::group(['prefix' => 'agen', 'middleware' => ['agent_auth']], function () {

    //========================new！！！==================
    Route::get('home', 'Agen\ReportController@home');//主页
    Route::get('user/index', 'Agen\UserController@index');//用户管理列表
    Route::get('salesmen/index', 'Agen\UserController@salesmenIndex');//代理商管理列表
    Route::get('salesmen/add', 'Agen\UserController@salesmenAdd');//添加代理商页面
    Route::get('salesmen/address', 'Agen\UserController@salesmenAddress');//添加代理商页面
    Route::post('salesmen/saveaddress', 'Agen\UserController@salesmenAddressSave');
    Route::get('transfer/index', 'Agen\UserController@transferIndex');//出入金列表页
    Route::get('set_password', 'Agen\MemberController@setPas');//修改密码
    Route::get('set_info', 'Agen\MemberController@setInfo');//基本信息
    Route::get('salesmen/update_agent', 'Agen\UserController@update_agent');
    Route::any('salesmen/update_agent_add', 'Agen\UserController@update_agent_add');
    Route::get('order_statistics', 'Agen\ReportController@orderSt');//订单统计
    Route::get('user_statistics', 'Agen\ReportController@userSt');//用户统计
    Route::get('money_statistics', 'Agen\ReportController@moneySt');//收益统计
    //==========================
    //首页
    Route::any('get_statistics', 'Agen\AgentIndexController@getStatistics');//首页获取统计信息

    Route::post('change_password', 'Agen\MemberController@changePWD');//修改密码

    Route::get('user_info', 'Agen\MemberController@getUserInfo');//获取用户信息
    Route::post('save_user_info', 'Agen\MemberController@saveUserInfo');//保存用户信息
    Route::any('lists', 'Agen\MemberController@lists');//代理商列表
    Route::post('addagent', 'Agen\MemberController@addAgent');//添加代理商
    Route::post('addsonagent', 'Agen\MemberController@addSonAgent');//给代理商添加代理商
    Route::post('update', 'Agen\MemberController@updateAgent');//添加代理商
    Route::post('searchuser', 'Agen\MemberController@searchuser');//查询用户
    Route::post('search_agent_son', 'Agen\MemberController@search_agent_son');//查询用户
    Route::any('del_agent', 'Agen\MemberController@del_agent');
    Route::any('google_code', 'Agen\MemberController@google_code');
    Route::any('logout', 'Agen\MemberController@logout');//退出登录
    Route::any('menu', 'Agen\MemberController@getMenu');//获取指定身份的菜单

    Route::post('jie', 'Agen\ReportController@jie');//阶段订单图表

    Route::post('day', 'Agen\ReportController@day');//阶段订单图表

    Route::post('order', 'Agen\ReportController@order');//阶段订单图表
    Route::post('order_num', 'Agen\ReportController@order_num');//阶段订单图表
    Route::post('order_money', 'Agen\ReportController@order_money');//阶段订单图表

    Route::post('user', 'Agen\ReportController@user');//阶段用户图表
    Route::post('user_num', 'Agen\ReportController@user_num');//阶段订单图表
    Route::post('user_money', 'Agen\ReportController@user_money');//阶段订单图表

    Route::post('agental', 'Agen\ReportController@agental');//阶段订单图表
    Route::post('agental_t', 'Agen\ReportController@agental_t');//阶段订单图表
    Route::post('agental_s', 'Agen\ReportController@agental_s');//阶段订单图表


    Route::get('order/lever_index', 'Agen\OrderController@leverIndex');//杠杆订单页面

    Route::post('order/list', 'Agen\OrderController@order_list');//团队所有订单

    Route::get('order/info', 'Agen\OrderController@order_info');//订单详情
    //秒合约
    Route::get('order/micro_index', 'Agen\OrderController@microIndex');
    Route::get('micro/currency_show', 'Agen\OrderController@microCurrency');
    Route::post('micro/list', 'Agen\OrderController@microList');

    Route::prefix('common')->namespace('Agen')->group(function () {
        Route::get('legal_currency', 'CommonController@legalCurrency');
    });

    //撮合订单
    Route::get('order/transaction_index', 'Agen\OrderController@transactionIndex');

    Route::get('order/transaction_list', 'Agen\OrderController@transactionList');

    Route::get('order/jie_index', 'Agen\OrderController@jieIndex');


    Route::post('jie/list', 'Agen\OrderController@jie_list');//团队所有结算
    Route::any('jie/export', 'Agen\OrderController@jie_export');//团队所有结算
    Route::post('jie/info', 'Agen\OrderController@jie_info');//结算详情

    Route::post('get_order_account', 'Agen\OrderController@get_order_account');
    Route::post('get_user_num', 'Agen\UserController@get_user_num');
    Route::post('get_my_invite_code', 'Agen\UserController@get_my_invite_code');


    Route::any('user/lists', 'Agen\UserController@lists');//用户列表
    Route::any('lever_transaction/lists', 'Agen\LeverTransactionController@lists');//用户的订单
    Route::any('account/money_log', 'Agen\AccountController@moneyLog');//结算
    Route::any('agent/info', 'Agen\AgentController@info');//代理商信息

    //划转出入列表
    Route::any('user/huazhuan_lists', 'Agen\UserController@huazhuan_lists');//用户列表

    //出入金（充币、提币)
    Route::get('recharge/index', 'Agen\CapitalController@rechargeIndex');
    Route::any('recharge/apply', 'Agen\CapitalController@rechargeApply');
    Route::any('recharge/req_show', 'Agen\CapitalController@req_show');
    Route::any('recharge/pass', 'Agen\CapitalController@passReq');
    Route::any('recharge/refuse', 'Agen\CapitalController@refuseReq');
    Route::get('withdraw/index', 'Agen\CapitalController@withdrawIndex');
    Route::get('capital/recharge', 'Agen\CapitalController@rechargeList');
    Route::get('capital/apply', 'Agen\CapitalController@applyList');
    Route::get('capital/withdraw', 'Agen\CapitalController@withdrawList');
    Route::any('user/flow', 'Agen\CapitalController@flow');//
    Route::any('user/flow_lists', 'Agen\CapitalController@flow_lists');//
    Route::get('capital/withdraw_show', 'Agen\CapitalController@withdraw_show');
    Route::any('capital/withdraw_done', 'Agen\CapitalController@withdraw_done');
    //用户资金
    Route::get('user/users_wallet', 'Agen\CapitalController@wallet');
    Route::get('users_wallet_total', 'Agen\CapitalController@wallettotalList');
    Route::get('user/conf', 'Agen\CapitalController@conf');
    Route::post('user/conf', 'Agen\CapitalController@postConf');//调节钱包账户
    //用户订单
    Route::get('user/lever_order', 'Agen\OrderController@userLeverIndex');
    Route::get('user/lever_order_list', 'Agen\OrderController@userLeverList');

    //结算 提现到账
    Route::post('wallet_out/done', 'Agen\CapitalController@walletOut');
    //用户点控
    Route::get('user/risk', 'Agen\UserController@risk');
    Route::post('user/postRisk', 'Agen\UserController@postRisk');
    Route::any('user/update_user_agent', 'Agen\UserController@update_user_agent');//用户管理列表
    Route::any('user/doLock', 'Agen\UserController@doLock');
    Route::any('user/update_password', 'Agen\UserController@update_password');
    Route::any('user/do_update_password', 'Agen\UserController@do_update_password');
    Route::any('user/user_mic', 'Agen\UserController@user_mic');
    Route::any('user/postMicro', 'Agen\UserController@postMicro');
    Route::any('agent_report/index', 'Agen\AgentReportController@index');
    Route::any('agent_report/list', 'Agen\AgentReportController@lists');
    //初级认证
    Route::get('user/real_index', 'Agen\UserRealController@index');
    Route::get('user/real_list', 'Agen\UserRealController@list');
    Route::get('user/real_info', 'Agen\UserRealController@detail');
    Route::post('user/real_del', 'Agen\UserRealController@del');
    Route::post('user/real_auth', 'Agen\UserRealController@auth');
    // 高级认证
    Route::get('user2/real_index', 'Agen\UserReal2Controller@index');
    Route::get('user2/real_list', 'Agen\UserReal2Controller@list');
    Route::get('user2/real_info', 'Agen\UserReal2Controller@detail');
    Route::post('user2/real_del', 'Agen\UserReal2Controller@del');
    Route::post('user2/real_auth', 'Agen\UserReal2Controller@auth');
});
