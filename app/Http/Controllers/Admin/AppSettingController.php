<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Libraries\Step2auth\Step2AuthUtil;
use App\Traits\CheckGoogleSecurityCodeTraint;
use Illuminate\Support\Facades\Redis;

class AppSettingController extends Controller
{
    use CheckGoogleSecurityCodeTraint;

    public function index()
    {
        $settingList = AppSetting::all()->toArray();
        $setting = [];
        foreach ($settingList as $key => $value) {
            $setting[$value['key']] = $value['value'];
        }
        return view('admin.app_setting.base', ['setting' => $setting]);
    }

    public function postAdd(Request $request)
    {
        $data = $request->all();
        unset($data["file"]);
        try {
            foreach ($data as $key => $value) {
                $setting = AppSetting::where('key', $key)->first();

                if (!$setting) {
                    $setting = new AppSetting();
                    $setting->key = $key;
                }

                if (stristr($value, config('app.aws_url'))) {
                    $setting->value = str_replace(config('app.aws_url'), "", $value);
                } else {
                    $setting->value = $value;
                }

                $setting->save();
            }
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

 /*   public function step_code(Request $request)
    {
        $step_code = $request->input('step_code', 0);
        $key = Redis::get("google_key");
        $ga = new Step2AuthUtil();
        if (empty($step_code)) {
            return $this->error("请输入谷歌安全码");
        }
        if ($this->checkSecurityCode($step_code, $key) == false) {
            return $this->error("验证安全码失败");
        }
        try {
            $step2SecretKey = $ga->createSecret();
            Redis::set("google_key", $step2SecretKey);
            $googleVerifyQrCodeUrl = $ga->getQRCodeGoogleUrl(urlencode("blockchain"), $step2SecretKey);
            $msg = "<br/><img src='{$googleVerifyQrCodeUrl}'/>";
            $msg .= "<br/><br/><font color='red'>" . $step2SecretKey . "</font>";
            return $this->success(["msg"=>$msg,"imgUrl"=>$googleVerifyQrCodeUrl]);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }*/

}
