<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="{{url('agen/index')}}">
            <span>Agent background system</span>
        </div>

        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu"
            lay-filter="layadmin-system-side-menu">
            <li data-name="home" class="layui-nav-item layui-nav-itemed">
                <a href="javascript:;" lay-tips="主页" lay-direction="2">
                    <i class="layui-icon layui-icon-home"></i>
                    <cite>home page</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd data-name="console" class="layui-this">
                        <a lay-href="{{url('agen/home')}}">console</a>
                    </dd>
                </dl>
            </li>
            <li data-name="user" class="layui-nav-item">
                <a href="javascript:;" lay-tips="User Management" lay-direction="2">
                    <i class="layui-icon layui-icon-user"></i>
                    <cite>User Management</cite>
                </a>
                <dl class="layui-nav-child">

                    <dd data-name="button">
                        <a lay-href="{{url('agen/user/index')}}">User Management</a>
                    </dd>
                    <dd data-name="button">
                        <a lay-href="{{url('agen/salesmen/index')}}">Agency management</a>
                    </dd>
                    <dd data-name="button">
                        <a lay-href="{{url('agen/user/real_index')}}">Primary certification</a>
                    </dd>
                    <dd data-name="button">
                        <a lay-href="{{url('agen/user2/real_index')}}">Advanced Certification</a>
                    </dd>
                </dl>
            </li>

            <li data-name="template" class="layui-nav-item">
                <a href="javascript:;" lay-tips="order management" lay-direction="2">
                    <i class="layui-icon layui-icon-template"></i>
                    <cite>order management</cite>
                </a>
                <!--                <dl class="layui-nav-child">
                                    <dd data-name="button">
                                        <a lay-href="/agent/order/lever_index">杠杆订单列表</a>
                                    </dd>
                                </dl>-->
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/order/transaction_index')}}">Currency Completion List</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/order/micro_index')}}">Second contract order list</a>
                    </dd>
                </dl>

            </li>
            <li data-name="template" class="layui-nav-item">
                <a href="javascript:;" lay-tips="Deposit and withdrawal management" lay-direction="2">
                    <i class="layui-icon layui-icon-template"></i>
                    <cite>Deposit and withdrawal management</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/recharge/apply')}}">Deposit list</a>
                    </dd>
                </dl>
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/withdraw/index')}}">Withdrawal list</a>
                    </dd>
                </dl>

            </li>

            <li data-name="template" class="layui-nav-item">
                <a href="javascript:;" lay-tips="Statistical report" lay-direction="2">
                    <i class="layui-icon layui-icon-chart"></i>
                    <cite>Statistical report</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/agent_report/index')}}">proxy report</a>
                    </dd>
                </dl>
            </li>

            <li data-name="template" class="layui-nav-item">
                <a href="javascript:;" lay-tips="set up" lay-direction="2">
                    <i class="layui-icon layui-icon-set"></i>
                    <cite>set up</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/set_password')}}">change Password</a>
                    </dd>
                </dl>

                <dl class="layui-nav-child">
                    <dd data-name="button">
                        <a lay-href="{{url('agen/set_info')}}">basic information</a>
                    </dd>
                </dl>

            </li>


        </ul>
    </div>
</div>
