<?php

namespace App\Traits;

use App\Libraries\Step2auth\Step2AuthUtil;
use Illuminate\Support\Facades\Log;
trait CheckGoogleSecurityCodeTraint
{
    public function checkSecurityCode($securityCode,$step2_secret_key){
        if(empty($securityCode)){
            return false;
        }
        if(empty($step2_secret_key)){
            return false;
        }

        $ga = new Step2AuthUtil();
        Log::info($ga->verifyCode($step2_secret_key,$securityCode).'++++');
        if($ga->verifyCode($step2_secret_key,$securityCode)==false){
           return false;
        }else{
            return true;
        }
    }
}
