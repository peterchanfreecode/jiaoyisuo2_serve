<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\AdminRolePermission;
use App\Models\Users;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Redis;
use App\Libraries\Step2auth\Step2AuthUtil;
use App\Traits\CheckGoogleSecurityCodeTraint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class DefaultController extends Controller
{
    use CheckGoogleSecurityCodeTraint;
    public function login(Request $request)
    {
        $username = Input::get('username', '');
        $password = Input::get('password', '');
        $google_code = Input::get('google_code', '');
        if (empty($username)) {
            return $this->error('用户名必须填写');
        }
        if (empty($password)) {
            return $this->error('密码必须填写');
        }
        if (empty($google_code)) {
            return $this->error('谷歌验证码必须填写');
        }
        $password = Users::MakePassword($password);
        $admin = Admin::where('username', $username)->where('password', $password)->first();
        $key = Redis::get("google_key");
        Log::info($key.'------');
        if(!$key){
            return $this->error('谷歌验证码未设置');
        }
       if ($this->checkSecurityCode($google_code,$key) == false) {
            return $this->error("验证安全码失败");
        }
        if (empty($admin)) {
            return $this->error('用户名密码错误');
        } else {
            $role = AdminRole::find($admin->role_id);
            if (empty($role)) {
                return $this->error('账号异常');
            } else {

                $key_req = "main_req_" . $admin->id;
                $key_wallet = "main_wallet_" . $admin->id;
                $key_real = "main_real_" . $admin->id;
                Redis::del($key_req);
                Redis::del($key_wallet);
                Redis::del($key_real);
                session()->put('admin_username', $admin->username);
                session()->put('admin_id', $admin->id);
                session()->put('admin_role_id', $admin->role_id);
                session()->put('admin_is_super', $role->is_super);
                //记录登录日志
                $ip = $request->header("x-real-ip") ?? request()->getClientIp();
                AdminLog::set_log($admin->id, "登录", $ip);
                return $this->success('登陆成功');
            }

        }
    }

    public function login_index()
    {
        session()->put('admin_username', '');
        session()->put('admin_id', '');
        session()->put('admin_role_id', '');
        session()->put('admin_is_super', '');
        $web_name = AppSetting::getValueByKey("web_name");
        return view('admin/login',['web_name' => $web_name ]);
    }

    public function index()
    {
        $admin_role = AdminRolePermission::where("role_id", session()->get('admin_role_id'))->get();
        $admin_role_data = array();
        foreach ($admin_role as $r) {
            array_push($admin_role_data, $r->action);
        }
        $web_name = AppSetting::getValueByKey("web_name");
        return view('admin.indexnew',[
        'web_name' => $web_name,
        ])->with("admin_role_data", $admin_role_data);;
    }

    public function indexnew()
    {
        $admin_role = AdminRolePermission::where("role_id", session()->get('admin_role_id'))->get();
        $admin_role_data = array();
        foreach ($admin_role as $r) {
            array_push($admin_role_data, $r->action);
        }
        return view('admin.index')->with("admin_role_data", $admin_role_data);;
    }


    public function getVerificationCode(Request $request)
    {
        $http_client = app('LbxChainServer');

        $uri = '/v3/wallet/verification';

        $response = $http_client->request('post', $uri, [
            'form_params' => [
                'projectname' => config('app.name'),
            ],
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if (isset($result['code']) && $result['code'] == 0) {
            return $this->success('发送成功');
        } else {
            return $this->error($result['msg']);
        }
    }

    public function check_google_key(Request $request)
    {
        $key = "google_key";
       if (Redis::get($key)) {
           return $this->success("验证成功");
       } else {
           return $this->error("验证失败");
       }
    }

    public function set_google_key(Request $request)
    {
        $key = "google_key";
        $ga = new Step2AuthUtil();
        $step2SecretKey = $ga->createSecret();
        $googleVerifyQrCodeUrl = $ga->getQRCodeGoogleUrl(urlencode("blockchain"), $step2SecretKey);
        if(Redis::get($key)){
            return $this->error("设置失败,请刷新页面");
        }
        $msg = "<br/><img src='{$googleVerifyQrCodeUrl}'/>";
        $msg .= "<br/><br/><font color='red'>" . $step2SecretKey . "</font>";
        Log::info($key.'------'.$step2SecretKey);
        if ( Redis::set($key, $step2SecretKey)) {
            return $this->success(["msg"=>$msg,"imgUrl"=>$googleVerifyQrCodeUrl]);
        } else {
            return $this->error("设置失败");
        }
    }
    public function dgx_upload(Request $request)
    {

        if (!empty($_FILES["file"]["error"])) {
            return $this->error($_FILES["file"]["error"]);
        } else {
            if ($_FILES["file"]["size"] > 35728640) {
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
            if ($_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/png"
                || $_FILES["file"]["type"] == "image/webp" || $_FILES["file"]["type"] == "image/jpeg") {
                $type = strtolower(substr($_FILES["file"]["name"], strrpos($_FILES["file"]["name"], '.') + 1)); // 得到文件类型，并且都转化成小写
                $path = 'upload/' . date('Ymd');
                if($type =="lob"){
                    $type = "png";
                }
                $arr = ["jpg","png","webp","jpeg"];
                if(!in_array($type,$arr)){
                    return $this->error("文件类型不对");
                }
                $disk = Storage::disk('s3');
                $disk->directories();
                $wenjian_name = time() . rand(0, 999999) . "." . $type;

                $filename =  $wenjian_name;
                // 转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
                $filename = iconv("UTF-8", "gb2312", $filename);
                $options = [
                    'ACL' => 'public-read',
                ];

                $path = $disk->putFileAs($path,$request->file('file'), $filename,$options);
                Log::info("----".$path);
                $path = "/".$path;
                $full_path = config('app.aws_url').$path;
                return $this->success(["path"=>$path,"full_path"=>$full_path]);
            } else {
                return $this->error("文件类型不对");
            }
        }
    }
}
