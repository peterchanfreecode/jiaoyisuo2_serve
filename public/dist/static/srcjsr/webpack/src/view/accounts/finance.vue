<template>
  <div :class="skins=='days'?'finace':'finace balck'">
    <div class="top">
      <div class="flex">
        <p class="lever-circle"></p>
        <p>{{$t('miscro.contractValuation')}}</p>
        <p>{{leverBalance}}（CNY）</p>
      </div>
      <div class="flex">
        <p class="second-circle"></p>
        <p>{{$t('miscro.secondValuation')}}</p>
        <p>{{microBalance}}（CNY）</p>
      </div>
      <div class="flex">
        <p class="miscro-circle"></p>
        <p>{{$t('miscro.falshValuation')}}</p>
        <p>{{matchBalance}}（CNY）</p>
      </div>
      <div class="flex">
        <p class="legal-circle"></p>
        <p>{{$t('miscro.c2cValuation')}}</p>
        <p>{{legalBalance}}（CNY）</p>
      </div>
      <!-- <div class="to_right">
        <router-link to="/coinCurrencyFlash">币币闪兑</router-link>
        <router-link to="/exchange">合约保险</router-link>
        
      </div> -->
    </div>
    <div class="content">
      <div class="flex between tabs">
        <div class="content-tab flex">
          <p
            :class="[{'active':types=='lever'}]"
            @click="selectType('lever',list[0].currency)"
          >{{$t('asset.leverAccount')}}</p>
          <p
            :class="[{'active':types=='micro'}]"
            @click="selectType('micro',list[0].currency)"
          >{{$t('asset.miscroAccount')}}</p>
          <p
            :class="[{'active':types=='match'}]"
            @click="selectType('match',list[0].currency)"
          >{{$t('asset.flashAccount')}}</p>
          <p
            :class="[{'active':types=='legal'}]"
            @click="selectType('legal',list[0].currency)"
          >{{$t('asset.fince_account')}}</p>
        </div>
        <!-- <p class="records" @click="recordList">财务记录</p> -->
      </div>
      <ul class="list">
        <li class="flex">
          <p>{{$t('asset.currency')}}</p>
          <p>{{$t('asset.canuse')}}</p>
          <p>{{$t('asset.frezz')}}</p>
          <p>{{$t('asset.conversion')}}(CNY)</p>
          <div></div>
        </li>
        <li
          class="flex"
          v-for="item in list"
          :key="item.id"
          v-if="types=='lever'&&item.is_lever == 1"
        >
          <p>{{item.currency_name}}</p>
          <p>{{item.lever_balance}}</p>
          <p>{{item.lock_lever_balance}}</p>
          <p>{{item.lever_balance * item.cny_price |numFilters(4)}}</p>
          <div class="btns">
            <button
              type="button"
              @click="transferBtn(item.currency_name,item.currency,1)"
            >{{$t('asset.transfer')}}</button>
            <button type="button" @click="records(item.currency,0)">{{$t('asset.record')}}</button>
          </div>
          <!-- <div class="btns" v-else>
            <button type="button" @click="chargeBtn(item.currency_name,item.currency)">充币</button>
            <button type="button" @click="withdrawBtn(item.currency_name,item.currency)">提币</button>
            <button type="button" @click="transferBtn(item.currency_name,item.currency,2)">划转</button>
          </div>-->
        </li>
        <li
          class="flex"
          v-for="item in list"
          :key="item.id"
          v-if="types=='micro'&&item.is_micro == 1"
        >
          <p>{{item.currency_name}}</p>
          <p>{{item.micro_balance}}</p>
          <p>{{item.lock_micro_balance}}</p>
          <p>{{item.micro_balance * item.cny_price |numFilters(4)}}</p>
          <div class="btns">
            <button
              v-if="item.currency_name=='USDT'"
              type="button"
              @click="transferBtn(item.currency_name,item.currency,1)"
            >{{$t('asset.transfer')}}</button>
            <button type="button" @click="records(item.currency,1)">{{$t('asset.record')}}</button>
          </div>
          <!-- <div class="btns">
            <button type="button" @click="chargeBtn(item.currency_name,item.currency)">充币</button>
            <button type="button" @click="withdrawBtn(item.currency_name,item.currency)">提币</button>
            <button type="button" @click="transferBtn(item.currency_name,item.currency,2)">划转</button>
          </div>-->
        </li>
        <li
          class="flex"
          v-for="item in list"
          :key="item.id"
          v-if="types=='match'&&item.is_match == 1"
        >
          <p>{{item.currency_name}}</p>
          <p>{{item.change_balance}}</p>
          <p>{{item.lock_change_balance}}</p>
          <p>{{item.change_balance * item.cny_price |numFilters(4)}}</p>
          <!-- <div class="btns" v-if="types=='lever'">
            <button type="button" @click="transferBtn(item.currency_name,item.currency,1)">划转</button>
          </div>-->
          <div class="btns">
            <button
              type="button"
              @click="chargeBtn(item.currency_name,item.currency)"
            >{{$t('asset.charging')}}</button>
            <button
              type="button"
              @click="withdrawBtn(item.currency_name,item.currency)"
            >{{$t('asset.withdraw')}}</button>
            <button
              type="button"
              v-if="item.currency_name=='USDT'"
              @click="transferBtn(item.currency_name,item.currency,2)"
            >{{$t('asset.transfer')}}</button>
            <button type="button" @click="records(item.currency,2)">{{$t('asset.record')}}</button>
          </div>
        </li>
        <li
          class="flex"
          v-for="item in list"
          :key="item.id"
          v-if="types=='legal'&&item.is_legal == 1"
        >
          <p>{{item.currency_name}}</p>
          <p>{{item.legal_balance}}</p>
          <p>{{item.lock_legal_balance}}</p>
          <p>{{item.legal_balance * item.cny_price |numFilters(4)}}</p>
          <!-- <div class="btns" v-if="types=='lever'">
            <button type="button" @click="transferBtn(item.currency_name,item.currency,1)">划转</button>
          </div>-->
          <div class="btns">
            <button
              type="button"
              @click="transferBtn(item.currency_name,item.currency,2)"
            >{{$t('asset.transfer')}}</button>
            <button type="button" @click="records(item.currency,2)">{{$t('asset.record')}}</button>
          </div>
        </li>
      </ul>
      <!-- 记录 -->
      <div class="records">
        <h4>{{$t('asset.moneyRecord')}}</h4>
        <ul class="record-list">
          <li class="flex">
            <p class="width1">{{$t('td.num')}}</p>
            <p class="width2">{{$t('asset.record')}}</p>
            <p class="width3">{{$t('td.time')}}</p>
          </li>
          <li class="flex" v-for="item in recordList" :key="item.id">
            <p class="width1">{{item.value}}</p>
            <p class="width2">{{item.info}}</p>
            <p class="width3">{{item.created_time}}</p>
          </li>
        </ul>
        <el-pagination
          layout="prev, pager, next"
          v-if="total > 10"
          :total="total"
          @current-change="handleCurrentChange"
        ></el-pagination>
      </div>
    </div>
    <!-- 充币 -->
    <el-dialog title :visible.sync="charge" width="400px" center>
      <div class="transfer-content">
        <h3>{{$t('asset.address_charge')}}</h3>
        <div class="charge-list">
          <div class="ewm_img tc mt10" id="code"></div>
          <div class="mt10 tc">
            <p class="ft14 excharge_address">{{excharge_address}}</p>
            <p id="copy" @click="copy" class="copy ft14 tc">{{$t('asset.copy')}}</p>
          </div>
          <div class="charge-footer ft10">
            <li>* {{$t('asset.a01')}}{{currencyName}}{{$t('asset.a02')}}{{$t('asset.a03')}}</li>
            <li>* {{$t('asset.a06')}}100{{currencyName}}。</li>
            <li>* {{$t('asset.a10')}}</li>
            <li>* {{$t('asset.a11')}}</li>
          </div>
        </div>
      </div>
    </el-dialog>
    <!-- 提币 -->
    <el-dialog title :visible.sync="withdraw" width="400px" center>
      <div class="transfer-content">
        <h3>{{$t('asset.withdraw')}}</h3>
        <div class="withdraw-list">
          <div class="flex">
            <p class="transfer-left">{{$t('asset.canbalance')}}</p>
            <p class="transfer-right">{{withdrawTotal}}{{currencyName}}</p>
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.address_width')}}</p>
            <input
              class="transfer-right withdraw-input"
              type="text"
              :placeholder="$t('asset.enterAdderses')"
              v-model="withdrawAddress"
            >
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.nums')}}</p>
            <input
              class="transfer-right withdraw-input"
              type="text"
              :placeholder="pala"
              v-model="withdrawNUm"
            >
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.ratenum')}}</p>
            <p class="transfer-right">{{withdrawRate}} {{currencyName}}</p>
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.havenum')}}</p>
            <p
              class="transfer-right"
            >{{(withdrawNUm - withdrawRate) > 0?(withdrawNUm - withdrawRate):'0.00' |numFilters(8)}} {{currencyName}}</p>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="withdrawSumbit">{{$t('cuy.confirm')}}</el-button>
      </span>
    </el-dialog>
    <!-- 划转 -->
    <el-dialog title :visible.sync="dialogVisible" width="600px" center>
      <div class="transfer-content">
        <h3>{{$t('asset.assets_hua')}}</h3>
        <div class="transfer-list">
          <div class="flex">
            <p class="transfer-left">{{$t('asset.transferCurrency')}}</p>
            <div class="transfer-right">
              <el-dropdown trigger="click" @command="selectedCurrency">
                <span class="el-dropdown-link">
                  {{value3}}
                  <i class="el-icon-arrow-down el-icon--right"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item
                    v-for="item in currencyList"
                    :key="item.id"
                    :command="item"
                  >{{item.name}}</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </div>
            <!-- <p class="transfer-right">{{currencyName}}</p> -->
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.transferDirection')}}</p>
            <div class="flex transfer-right">
              <div class="transfer-name">
                <el-dropdown trigger="click" @command="selectedOne">
                  <span class="el-dropdown-link">
                    {{value1}}
                    <i class="el-icon-arrow-down el-icon--right"></i>
                  </span>
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item
                      v-for="(item,index) in accountList"
                      :key="index"
                      :command="item.type"
                    >{{item.texts}}</el-dropdown-item>
                    <!-- <el-dropdown-item command="levers">{{$t('asset.leverAccount')}}</el-dropdown-item>
                    <el-dropdown-item command="micros">{{$t('asset.miscroAccount')}}</el-dropdown-item>
                    <el-dropdown-item command="changes">{{$t('asset.flashAccount')}}</el-dropdown-item>-->
                  </el-dropdown-menu>
                </el-dropdown>
              </div>
              <!-- <p class="transfer-name">{{transferType==2?text1:text2}}</p> -->
              <p class="transfer-text">{{$t('asset.turn')}}</p>
              <div class="transfer-name">
                <el-dropdown trigger="click" @command="selectedTwo">
                  <span class="el-dropdown-link">
                    {{value2}}
                    <i class="el-icon-arrow-down el-icon--right"></i>
                  </span>
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item
                      v-for="(item,index) in accountList"
                      :key="index"
                      :command="item.type"
                    >{{item.texts}}</el-dropdown-item>
                    <!-- <el-dropdown-item command="lever">{{$t('asset.leverAccount')}}</el-dropdown-item>
                    <el-dropdown-item command="micro">{{$t('asset.miscroAccount')}}</el-dropdown-item>
                    <el-dropdown-item command="change">{{$t('asset.flashAccount')}}</el-dropdown-item>-->
                  </el-dropdown-menu>
                </el-dropdown>
              </div>

              <!-- <p class="icons" @click="tabClick">
                <span class="iconfont iconexchange4jiaohuan"></span>
              </p>-->
            </div>
          </div>
          <div class="flex">
            <p class="transfer-left">{{$t('asset.tfnum')}}</p>
            <p class="transfer-right transfer-input flex between">
              <input type="text" :placeholder="$t('asset.pNum')" v-model="trasferNum">
              <span @click="nums">{{$t('asset.knum')}}：{{transferBalance}}</span>
            </p>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="transferSumbit">{{$t('cuy.confirm')}}</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import "@/lib/clipboard.min.js";
import "@/lib/jquery.qrcode.min.js";
export default {
  name: "finance",
  data() {
    return {
      list: [],
      financeData: {},
      leverBalance: "",
      microBalance: "",
      matchBalance: "",
      legalBalance: "",
      token: "",
      types: "lever",
      type: 0,
      page: 1,
      currency: "",
      recordList: [],
      total: 0,
      dialogVisible: false,
      availableBalance: "",
      currencyName: "",
      text1: "资产账户",
      text2: this.$t("asset.leverAccount"),
      transferType: 2,
      legalAvailableBalance: "",
      leverAvailableBalance: "",
      microAvailableBalance: "",
      matchAvailableBalance: "",
      trasferNum: "",
      charge: false,
      excharge_address: "",
      withdraw: false,
      withdrawAddress: "",
      withdrawNUm: "",
      withdrawRate: "",
      withdrawTotal: "",
      pala: "",
      value1: this.$t("asset.leverAccount"),
      value2: this.$t("asset.miscroAccount"),
      balances: "",
      skins: localStorage.getItem("skin") || "days",
      currencyList: [],
      value3: "",
      accountList: [],
      value3Id: "",
      transferBalance: ""
    };
  },
  beforeCreate() {
    if (
      localStorage.getItem("skin") &&
      localStorage.getItem("skin") == "nights"
    ) {
      document
        .querySelector("html")
        .setAttribute("style", "background-color:#000;");
    } else {
      document
        .querySelector("html")
        .setAttribute("style", "background-color:#f8f6f6;");
    }
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  methods: {
    // 财务记录
    records(ids, types) {
      var that = this;
      that.currency = ids;
      that.type = types;
      that.init();
    },
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "wallet/legal_log",
        method: "post",
        data: {
          currency: that.currency,
          type: that.type,
          page: that.page
        },
        headers: { Authorization: that.token }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.recordList = res.data.message.list;
            that.total = Number(res.data.message.count);
          } else {
            return;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    selectType(type, ids) {
      var that = this;
      that.types = type;
      that.currency = ids;
      if (type == "lever") {
        that.type = 0;
        that.list = that.financeData.lever_wallet.balance;
      } else if (type == "micro") {
        that.type = 1;
        that.list = that.financeData.micro_wallet.balance;
      } else if (type == "match") {
        that.type = 2;
        that.list = that.financeData.change_wallet.balance;
      } else if (type == "legal") {
        that.type = 2;
        that.list = that.financeData.legal_wallet.balance;
      }
      that.page = 1;
      that.init();
    },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.page = val;
      that.init();
    },
    // 划转
    transferBtn(name, ids, num) {
      var that = this;
      that.currencyName = name;
      that.currency = ids;
      that.dialogVisible = true;
      that.transferType = num;
      if (that.transferType == 2) {
        that.availableBalance = that.legalAvailableBalance;
      } else {
        that.availableBalance = that.leverAvailableBalance;
      }
    },
    tabClick() {
      var that = this;
      that.transferType = that.transferType == 2 ? "1" : "2";
      if (that.transferType == 2) {
        that.availableBalance = that.legalAvailableBalance;
      } else {
        that.availableBalance = that.leverAvailableBalance;
      }
    },
    nums() {
      var that = this;
      that.trasferNum = that.availableBalance;
    },
    // 划转提交
    transferSumbit() {
      var that = this;
      var froms = "";
      var tos = "";
      if (that.trasferNum) {
        if (that.value1 == that.$t("asset.leverAccount")) {
          froms = "lever";
        } else if (that.value1 == that.$t("asset.miscroAccount")) {
          froms = "micro";
        } else if (that.value1 == that.$t("asset.flashAccount")) {
          froms = "change";
        }else if (that.value1 == that.$t("asset.fince_account")) {
          froms = "legal";
        }
        if (that.value2 == that.$t("asset.leverAccount")) {
          tos = "lever";
        } else if (that.value2 == that.$t("asset.miscroAccount")) {
          tos = "micro";
        } else if (that.value2 == that.$t("asset.flashAccount")) {
          tos = "change";
        }else if (that.value2 == that.$t("asset.fince_account")) {
          tos = "legal";
        }
        that.dialogVisible = false;
        this.$http({
          url: "/api/" + "wallet/change",
          method: "post",
          data: {
            currency_id: that.value3Id,
            from_field: froms,
            to_field: tos,
            number: that.trasferNum
          },
          headers: { Authorization: that.token }
        })
          .then(res => {
            layer.msg(res.data.message);
            if (res.data.type == "ok") {
              that.trasferNum = "";
              setTimeout(function() {
                window.location.reload();
              }, 1000);
            } else {
              return;
            }
          })
          .catch(error => {
            console.log(error);
          });
      } else {
        layer.msg(that.$t("asset.pNum"));
      }
    },
    selectedOne(command) {
      var that = this;
      if (command == "lever") {
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "lever") {
            j = i;
          }
        }
        that.value1 = that.$t("asset.leverAccount");
        for (var i = 0; i < that.currencyList.length; i++) {
          if (that.currencyList[i].id == that.currency) {
            that.transferBalance = that.currencyList[i].wallet.lever_balance;
          }
        }
        if (that.value2 == that.$t("asset.leverAccount")) {
          that.value2 = that.accountList[j].texts;
        }
      } else if (command == "micro") {
        that.value1 = that.$t("asset.miscroAccount");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "micro") {
            j = i;
          }
        }
        for (var i = 0; i < that.currencyList.length; i++) {
          if (that.currencyList[i].id == that.currency) {
            that.transferBalance = that.currencyList[i].wallet.micro_balance;
          }
        }
        if (that.value2 == that.$t("asset.miscroAccount")) {
          that.value2 = that.accountList[j].texts;
        }
      } else if (command == "match") {
        that.value1 = that.$t("asset.flashAccount");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "match") {
            j = i;
          }
        }
        for (var i = 0; i < that.currencyList.length; i++) {
          if (that.currencyList[i].id == that.currency) {
            that.transferBalance = that.currencyList[i].wallet.change_balance;
          }
        }
        if (that.value2 == that.$t("asset.flashAccount")) {
          that.value2 = that.accountList[j].texts;
        }
      } else if (command == "legal") {
        that.value1 = that.$t("asset.fince_account");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "legal") {
            j = i;
          }
        }
        for (var i = 0; i < that.currencyList.length; i++) {
          if (that.currencyList[i].id == that.currency) {
            that.transferBalance = that.currencyList[i].wallet.legal_balance;
          }
        }
        if (that.value2 == that.$t("asset.fince_account")) {
          that.value2 = that.accountList[j].texts;
        }
      }
    },
    selectedTwo(command) {
      var that = this;
      if (command == "lever") {
        that.value2 = that.$t("asset.leverAccount");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "lever") {
            j = i;
          }
        }
        if (that.value1 == that.$t("asset.leverAccount")) {
          that.value1 = that.accountList[j].texts;
          for (var i = 0; i < that.currencyList.length; i++) {
            if (that.currencyList[i].id == that.currency) {
              if (that.accountList[j].type == "micro") {
                that.transferBalance =
                  that.currencyList[i].wallet.micro_balance;
              } else if (that.accountList[j].type == "match") {
                that.transferBalance =
                  that.currencyList[i].wallet.change_balance;
              } else if (that.accountList[j].type == "legal") {
                that.transferBalance =
                  that.currencyList[i].wallet.legal_balance;
              }
            }
          }
        }
      } else if (command == "micro") {
        that.value2 = that.$t("asset.miscroAccount");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "micro") {
            j = i;
          }
        }
        if (that.value1 == that.$t("asset.miscroAccount")) {
          that.value1 = that.accountList[j].texts;
          for (var i = 0; i < that.currencyList.length; i++) {
            if (that.currencyList[i].id == that.currency) {
              if (that.accountList[j].type == "lever") {
                that.transferBalance =
                  that.currencyList[i].wallet.lever_balance;
              } else if (that.accountList[j].type == "match") {
                that.transferBalance =
                  that.currencyList[i].wallet.change_balance;
              } else if (that.accountList[j].type == "legal") {
                that.transferBalance =
                  that.currencyList[i].wallet.legal_balance;
              }
            }
          }
        }
      } else if (command == "match") {
        that.value2 = that.$t("asset.flashAccount");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "match") {
            j = i;
          }
        }
        if (that.value1 == that.$t("asset.flashAccount")) {
          that.value1 = that.accountList[j].texts;
          for (var i = 0; i < that.currencyList.length; i++) {
            if (that.currencyList[i].id == that.currency) {
              if (that.accountList[j].type == "lever") {
                that.transferBalance =
                  that.currencyList[i].wallet.lever_balance;
              } else if (that.accountList[j].type == "micro") {
                that.transferBalance =
                  that.currencyList[i].wallet.micro_balance;
              } else if (that.accountList[j].type == "legal") {
                that.transferBalance =
                  that.currencyList[i].wallet.legal_balance;
              }
            }
          }
        }
      } else if (command == "legal") {
        that.value2 = that.$t("asset.fince_account");
        var j = 0;
        for (var i = 0; i < that.accountList.length; i++) {
          if (that.accountList[i].type != "legal") {
            j = i;
          }
        }
        if (that.value1 == that.$t("asset.fince_account")) {
          that.value1 = that.accountList[j].texts;
          for (var i = 0; i < that.currencyList.length; i++) {
            if (that.currencyList[i].id == that.currency) {
              if (that.accountList[j].type == "lever") {
                that.transferBalance =
                  that.currencyList[i].wallet.lever_balance;
              } else if (that.accountList[j].type == "match") {
                that.transferBalance =
                  that.currencyList[i].wallet.change_balance;
              } else if (that.accountList[j].type == "micro") {
                that.transferBalance =
                  that.currencyList[i].wallet.micro_balance;
              }
            }
          }
        }
      }
    },
    // 币种选择
    selectedCurrency(command) {
      var that = this;
      that.value3 = command.name;
      that.value3Id = command.id;

      for (var i = 0; i < that.currencyList.length; i++) {
        if (that.currencyList[i].id == that.value3Id) {
          var arr2 = [];
          if (that.currencyList[i].is_legal == 1) {
            var obj = {
              type: "legal",
              texts: that.$t("asset.fince_account")
            };
            arr2.push(obj);
          }
          
          if (that.currencyList[i].is_micro == 1) {
            var obj = {
              type: "micro",
              texts: that.$t("asset.miscroAccount")
            };
            arr2.push(obj);
          }
          if (that.currencyList[i].is_lever == 1) {
            var obj = {
              type: "lever",
              texts: that.$t("asset.leverAccount")
            };
            arr2.push(obj);
          }
          if (that.currencyList[i].is_match == 1) {
            var obj = {
              type: "match",
              texts: that.$t("asset.flashAccount")
            };
            arr2.push(obj);
          }
          
          that.accountList = arr2;
          console.log(that.accountList);
          that.value1 = that.accountList[0].texts;
          that.value2 = that.accountList[1].texts;
          if (that.accountList[0].type == "lever") {
            that.transferBalance = that.currencyList[i].wallet.lever_balance;
          } else if (that.accountList[0].type == "match") {
            that.transferBalance = that.currencyList[i].wallet.change_balance;
          } else if (that.accountList[0].type == "micro") {
            that.transferBalance = that.currencyList[i].wallet.micro_balance;
          } else if (that.accountList[0].type == "legal") {
            that.transferBalance = that.currencyList[i].wallet.legal_balance;
          }
        }
      }
    },
    // 复制
    copy() {
      var that = this;
      var clipboard = new Clipboard(".copy", {
        text: function() {
          return that.excharge_address;
        }
      });
      clipboard.on("success", function(e) {
        that.flags = true;
        layer.msg(that.$t("set.copysuccess"));
      });
      clipboard.on("error", function(e) {
        that.flags = false;
        // layer.msg("请重新复制");
      });
    },
    // 充币
    chargeBtn(name, ids) {
      var that = this;
      that.currencyName = name;
      that.currency = ids;
      that.charge = true;
      $("#code").html("");
      this.$http({
        url: "/api/" + "wallet/get_in_address",
        method: "post",
        data: { currency: ids },
        headers: { Authorization: that.token }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.excharge_address = res.data.message;
            // 生成二维码
            $("#code").qrcode({
              width: 120, //宽度
              height: 120, //高度
              text: res.data.message
            });
          } else {
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // 提币
    withdrawBtn(name, ids) {
      var that = this;
      that.currencyName = name;
      that.currency = ids;
      that.withdraw = true;
      this.$http({
        url: "/api/" + "wallet/get_info",
        method: "post",
        data: { currency: ids },
        headers: { Authorization: that.token }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.withdrawRate = res.data.message.rate;
            that.withdrawTotal = res.data.message.legal_balance;
            that.pala = that.$t("asset.minnum") + res.data.message.min_number;
          } else {
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // 提币提交
    withdrawSumbit() {
      var that = this;
      if (!that.withdrawAddress) {
        layer.msg(that.$t("asset.enterAdderses"));
        return false;
      }
      if (!that.withdrawNUm) {
        layer.msg(that.$t("asset.enterNum"));
        return false;
      }
      this.$http({
        url: "/api/" + "wallet/out",
        method: "post",
        data: {
          currency: that.currency,
          number: that.withdrawNUm,
          rate: that.withdrawRate,
          address: that.withdrawAddress
        },
        headers: { Authorization: that.token }
      })
        .then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
            setTimeout(function() {
              window.location.reload();
            }, 1000);
          } else {
          }
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  created() {
    var that = this;
    this.token = localStorage.getItem("token") || "";
  },

  mounted() {
    var that = this;
    var clipboard = new Clipboard(".copy");
    clipboard.on("success", function(e) {
      layer.alert(that.$t("set.copysuccess"));
    });
    clipboard.on("error", function(e) {
      // alert("复制失败");
    });
    this.$http({
      url: "/api/" + "wallet/list",
      method: "post",
      data: {},
      headers: { Authorization: that.token }
    })
      .then(res => {
        if (res.data.type == "ok") {
          that.leverBalance = res.data.message.lever_wallet.totle;
          that.legalBalance = res.data.message.legal_wallet.totle;
          that.microBalance = res.data.message.micro_wallet.totle;
          that.matchBalance = res.data.message.change_wallet.totle;
          that.list = res.data.message.lever_wallet.balance;
          that.status = res.data.message.is_open_CTbi;
          that.financeData = res.data.message;
          that.balances = that.list[0].lever_balance;
          for (
            var i = 0;
            i < res.data.message.lever_wallet.balance.length;
            i++
          ) {
            if (res.data.message.lever_wallet.balance[i].is_lever == 1) {
              that.currency = res.data.message.lever_wallet.balance[i].currency;
              // that.leverAvailableBalance = res.data.message.lever_wallet.balance[i].lever_balance;
            }
          }
          // for (var i = 0;i < res.data.message.legal_wallet.balance.length;i++) {
          //   if (res.data.message.legal_wallet.balance[i].currency_name == "USDT") {
          //     that.legalAvailableBalance =
          //       res.data.message.legal_wallet.balance[i].legal_balance;
          //     that.availableBalance =
          //       res.data.message.legal_wallet.balance[i].legal_balance;
          //   }
          // }
          that.init();
        } else {
          return;
        }
      })
      .catch(error => {
        console.log(error);
      });
    this.$http({
      url: "/api/" + "currency/user_currency_list",
      method: "get",
      data: {},
      headers: { Authorization: that.token }
    })
      .then(res => {
        if (res.data.type == "ok") {
          var datas = res.data.message;
          if (datas.length > 0) {
            var arr = [];
            for (var i = 0; i < datas.length; i++) {
              var nums =
                datas[i].is_legal -
                0 +
                (datas[i].is_lever - 0) +
                (datas[i].is_match - 0) +
                (datas[i].is_micro - 0);
              if (nums > 1) {
                arr.push(datas[i]);
              }
            }
            that.value3 = arr[0].name;
            that.value3Id = arr[0].id;
            var arr2 = [];
            if (arr[0].is_legal == 1) {
              var obj = {
                type: "legal",
                texts: that.$t("asset.fince_account")
              };
              arr2.push(obj);
            }
            
            if (arr[0].is_micro == 1) {
              var obj = {
                type: "micro",
                texts: that.$t("asset.miscroAccount")
              };
              arr2.push(obj);
            }
            if (arr[0].is_lever == 1) {
              var obj = {
                type: "lever",
                texts: that.$t("asset.leverAccount")
              };
              arr2.push(obj);
            }
            if (arr[0].is_match == 1) {
              var obj = {
                type: "match",
                texts: that.$t("asset.flashAccount")
              };
              arr2.push(obj);
            }
            
            that.accountList = arr2;
            that.currencyList = arr;
            that.value1 = arr2[0].texts;
            that.value2 = arr2[1].texts;
            if (arr2[0].type == "lever") {
              that.transferBalance = arr[0].wallet.lever_balance;
            } else if (arr2[0].type == "micro") {
              that.transferBalance = arr[0].wallet.micro_balance;
            } else if (arr2[0].type == "match") {
              that.transferBalance = arr[0].wallet.change_balance;
            } else if (arr2[0].type == "legal") {
              that.transferBalance = arr[0].wallet.legal_balance;
            }
          }
          that.init();
        } else {
          return;
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
<style scoped>
.finace {
  width: 100%;
  margin-top: 20px;
}
.finace .top {
  width: 100%;
  background-color: #fff;
  border-radius: 2px;
  padding: 20px 0;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  font-size: 15px;
  padding-left: 92px;
  padding-right: 30px;
  position: relative;
}
.balck .top {
  background-color: #1e2b34;
  color: #fff;
}
.balck .content {
  background-color: #1e2b34;
  color: #fff;
}
.balck .tabs {
  border-bottom: 1px solid #000;
}
.balck .records li:first-child {
  border-bottom: 1px solid #000;
}
.top div {
  line-height: 46px;
}
.top .lever-circle {
  width: 10px;
  height: 10px;
  background-color: #aa82f7;
  border-radius: 50%;
  margin-top: 18px;
  margin-right: 20px;
}
.top .second-circle {
  width: 10px;
  height: 10px;
  background-color: #ec7c78;
  border-radius: 50%;
  margin-top: 18px;
  margin-right: 20px;
}
.top .miscro-circle {
  width: 10px;
  height: 10px;
  background-color: #f148ed;
  border-radius: 50%;
  margin-top: 18px;
  margin-right: 20px;
}
.top .legal-circle {
  width: 10px;
  height: 10px;
  background-color: #487ef1;
  border-radius: 50%;
  margin-top: 18px;
  margin-right: 20px;
}
.top p {
  margin-right: 40px;
}
.top .to_right {
  height: 100%;
  width: 120px;
  position: absolute;
  top: 0;
  right: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.top .to_right a {
  color: #f0b90b;
}
.content {
  margin-top: 10px;
  background-color: #fff;
  width: 100%;
  box-shadow: 0 0 29px 0 rgba(32, 36, 48, 0.08);
  min-height: 500px;
  margin-bottom: 30px;
}
.tabs {
  padding: 0 15px;
  line-height: 40px;
  border-bottom: 1px solid #e4e7ed;
}
.content-tab {
  height: 40px;
  line-height: 40px;
}
.content-tab p {
  margin-right: 40px;
  cursor: pointer;
}
.content-tab .active {
  border-bottom: 2px solid #f0b90b;
}
.list {
  width: 100%;
  padding: 15px;
}
.list li p {
  width: 20%;
  line-height: 30px;
}
.list li div {
  width: 20%;
}
.btns button {
  border: none;
  background-color: rgba(0, 0, 0, 0);
  color: #f0b90b;
  text-decoration: underline;
  font-size: 14px;
  cursor: pointer;
  margin-right: 15px;
}
.records {
  padding: 0 15px;
}
.records h4 {
  font-size: 16px;
  margin-bottom: 15px;
}
.records li {
  padding: 10px 0;
}
.records li:first-child {
  border-bottom: 1px solid #ebeef5;
}
.width1 {
  width: 15%;
}
.width2 {
  width: 60%;
}
.width3 {
  width: 25%;
}
.records >>> .el-pagination {
  text-align: center;
  margin-top: 20px;
  padding-bottom: 40px;
}
.records >>> .el-pagination .btn-prev,
.records >>> .el-pagination .btn-next {
  display: none;
}
.transfer {
  display: none;
}
.transfer-content h3 {
  text-align: center;
  color: #f0b90b;
  font-size: 18px;
}
.transfer-left {
  width: 80px;
  color: #606266;
  line-height: 40px;
  padding: 0 12px 0 0;
  box-sizing: border-box;
}
.transfer-right {
  height: 40px;
  line-height: 40px;
}
.transfer-name {
  line-height: 28px;
  height: 28px;
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  padding: 0 20px;
  margin-right: 5px;
  position: relative;
  top: 6px;
}
.transfer-text {
  margin-right: 5px;
}
.icons {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background-color: #f0b90b;
  position: relative;
  top: 9px;
  left: 5px;
  cursor: pointer;
}
.icons .iconfont {
  color: #fff;
  font-size: 10px;
  position: absolute;
  top: -7px;
  left: 5px;
}
.transfer-input {
  width: 430px;
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  line-height: 28px;
  height: 28px;
  padding: 0 15px;
  margin-top: 6px;
}
.transfer-input span {
  color: #f0b90b;
  cursor: pointer;
}
.finace >>> .el-button {
  background-color: #f0b90b;
  width: 140px;
  border: none;
}
.copy {
  margin-top: 10px;
  color: #f0b90b;
  cursor: pointer;
}
.finace >>> .el-dialog--center .el-dialog__body {
  padding: 20px 15px;
}
.charge-footer {
  font-size: 10px;
  margin-top: 10px;
}
.charge-footer li {
  line-height: 1.3;
}
.withdraw-input {
  width: 270px;
  height: 28px;
  line-height: 28px;
  border: 1px solid #dcdfe6;
  margin-top: 6px;
  padding: 0 15px;
  border-radius: 4px;
}
.finace >>> .el-pager li {
  background-color: rgba(0, 0, 0, 0);
  color: #fff;
}
.finace >>> .el-pager .active {
  color: #f0b90b;
}
</style>





// WEBPACK FOOTER //
// src/view/accounts/finance.vue