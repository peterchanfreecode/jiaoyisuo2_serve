<?php

namespace App\Service;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailService
{

    // const KEY = 'LBANK@LBANKsml.com';
    // const PASSWORD = 'nezlxzvdtuvqkmqg';
    // const SENDER_EMAIL = 'LBANK@LBANKsml.com';
    // const SENDER_NAME = 'LBANK';
    // const SMTP_HOST = 'smtp.gmail.com';
    // const SMTP_PORT = '465';

    // const KEY = 'info@lbank18.com';
    // const PASSWORD = '2YIFxldqvmsb';
    // const SENDER_EMAIL = 'info@lbank18.com';
    // const SENDER_NAME = 'Lbank';
    // const SMTP_HOST = 'smtp.lbank18.com';
    // const SMTP_PORT = '587';

    const KEY = 'system@lbank18.com';
    const PASSWORD = 'gzohdfesehhsjeds';
    const SENDER_EMAIL = 'system@lbank18.com';
    const SENDER_NAME = 'LBANK';
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = '465';

    public static function sendMail($toMail)
    {
        $mail = new PHPMailer();
        try {
            $str = rand(100000, 999999);
            Cache::set('email_code:' . $toMail, $str, 1800); // 10分钟有效
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = self::SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = self::KEY;
            $mail->Password = self::PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = self::SMTP_PORT;

            $mail->setFrom(self::SENDER_EMAIL, self::SENDER_NAME);
            $mail->addAddress($toMail);
            $mail->isHTML(true);
            $mail->CharSet = "UTF-8"; //字符集
            $mail->Subject = 'Account Activation';
            $mail->Body = '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
      .content {
        background: #1f253a;
        width: 100%;
        color: #93a1c2;
        padding: 20px;
      }
      .gt {
        width: 60%;
        margin-left: 10%;
      }
      .code {
        margin-top: 20px;
        height: 100px;
        background-color: #0f121d;
        width: 100%;
        color: #1373e8;
        font-size: 30px;
        line-height: 100px;
        text-align: center;
        font-weight: 600;
      }
    </style>
  </head>
  <body>
    <div class="content">
      <div class="aHl"></div>
      <div id=":2ol" tabindex="-1"></div>
      <div
        id=":2oa"
        class="ii gt"
        jslog="20277; u014N:xr6bB; 4:W251bGwsbnVsbCxbXV0."
        style=""
      >
        <div id=":2o9" class="a3s aiL msg-5807654868968254169">
          <u></u>

          <div class="m_-5807654868968254169bg">
            <div
              class="m_-5807654868968254169container m_-5807654868968254169p-4"
            >
              <h2 style="color: #fff">Account Activation</h2>
              <div class="m_-5807654868968254169mt-2" style="color:#93a1c2">
                Dear users, welcome to LBANK,Your verification code is:
              </div>
              <div class="code">' . $str . '</div>
              <div class="m_-5807654868968254169mt-4" style="color:#93a1c2">
                <h4>Security Tips:</h4>
                <ol>
                  <li>
                    Don\'t tell anyone your password and secondary verification
                    code.
                  </li>
                  <li>
                    Don\'t call any customer service person who claims to be
                    LBANK.
                  </li>
                </ol>
                <h4>LBANK Team</h4>
                <p>System mail,please do not reply.</p>
                <div class="yj6qo"></div>
                <div class="adL"></div>
              </div>
              <div class="adL"></div>
            </div>
            <div class="adL"></div>
          </div>
          <div class="adL"></div>
        </div>
      </div>
      <div id=":2op" class="ii gt" style="display: none">
        <div id=":2oq" class="a3s aiL" dir="ltr">
          <u></u>
          <div class="m_4414228920273776432bg">
            <div
              class="m_4414228920273776432container m_4414228920273776432p-4"
            >
              <div
                class="m_4414228920273776432mt-4 m_4414228920273776432font-bold m_4414228920273776432text-xl m_4414228920273776432text-white"
              >
                帐号激活
              </div>
              <div class="m_4414228920273776432mt-2" style="color:#93a1c2">
                尊敬的用户,欢迎来到LBANK.您的验证码是:
              </div>
              <div
                class="m_4414228920273776432dark-box m_4414228920273776432mt-2 m_4414228920273776432font-bold m_4414228920273776432text-blue m_4414228920273776432text-center"
              >
                ' . $str . '
              </div>
              <div class="m_4414228920273776432mt-4" style="color:#93a1c2">
                <h4>安全提示:</h4>
                <ol>
                  <li>不要告诉任何人你的密码和二次验证码.</li>
                  <li>不要打电话给任何自称是LBANK的客服.</li>
                </ol>
                <h4>LBANK 团队</h4>
                <p>系统邮件,请勿回复.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hi"></div>
    </div>
  </body>
</html>
';
            $mail->send();
            return true;
        } catch (\Exception $e) {
            Log::info('邮件发送失败' . $mail->ErrorInfo);
            return false;
        }
    }

}
