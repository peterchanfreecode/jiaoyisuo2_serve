<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent;
use App\Service\CurrencyService;
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use App\Models\AgentDomain;
class DefaultController extends Controller
{

    public function language(Request $request)
    {
        $lang = $request->get('lang', 'en');
        session()->put('lang', $lang);
        return $this->success($lang);
    }

    public function app_setting(Request $request)
    {
        $data = AppSetting::all(["key", "value"]);
        $arr = [];
        if ($data) {

            foreach ($data as $v) {
                if ($v["key"] != "s") {
                    $arr[$v["key"]] = $v["value"];
                }
            }
            $arr["ima_url"] = config('app.aws_url');
            unset($arr["email_code"]);
        }
        return $this->success($arr);
    }

    public function upload(Request $request)
    {

        if (!empty($_FILES["file"]["error"])) {
            return $this->error($_FILES["file"]["error"]);
        } else {
            if ($_FILES["file"]["size"] > 10485760) {
                return $this->error("文件大小超出");
            }
            $fp = fopen($_FILES["file"]["tmp_name"], "rb"); // 图片是否可读权限
            $filesize = $_FILES["file"]["size"];
            $content = @fread($fp, $filesize);
            $temp = strtolower($content);
            if (strpos($temp, 'script') !== false) {
                return $this->error("文件类型不对");
            }
            @fclose($fp);

            if ($_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/webp" || $_FILES["file"]["type"] == "image/jpeg") {
                $type = strtolower(substr($_FILES["file"]["name"], strrpos($_FILES["file"]["name"], '.') + 1)); // 得到文件类型，并且都转化成小写
                $path = 'upload/' . date('Ymd');
                $disk = Storage::disk('s3');
                $disk->directories();
                if ($type == "lob") {
                    $type = "png";
                }
                $arr = ["jpg", "png", "webp", "jpeg"];
                if (!in_array($type, $arr)) {
                    return $this->error("文件类型不对");
                }
                $wenjian_name = time() . rand(0, 999999) . "." . $type;

                $filename = $wenjian_name;
                // 转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
                $filename = iconv("UTF-8", "gb2312", $filename);
                $options = [
                    'ACL' => 'public-read',
                ];

                $path = $disk->putFileAs($path, $request->file('file'), $filename, $options);
                $path = "/" . $path;
                $full_path = config('app.aws_url') . $path;
                return $this->success(["path" => $path, "full_path" => $full_path]);
            } else {
                return $this->error("文件类型不对");
            }
        }
    }

    public function agent_kefu(Request $request)
    {
        $domain = $request->get('domain', '');
        $info = AppSetting::where("key", "chatUrl")->first();
		$agent_info = AgentDomain::where("agent_domain",$domain)->first();
        $agent_kefu =$agent_info->agent_kefu?? $info->value;
        
        return $this->success(["agent_kefu" => $agent_kefu]);
    }

    public function setwallet(Request $request)
    {
        $currency_id = $request->get('currency_id', '');
        if (!$currency_id) {
            return $this->error("参数错误");
        }
        CurrencyService::set_Wallet($currency_id);
        return $this->success("执行成功");
    }
}

?>