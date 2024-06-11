<?php

namespace App\Service;
use App\Models\Users;
use App\Models\UsersWallet;
use Illuminate\Support\Facades\DB;

class CurrencyService
{

    public static function set_Wallet($id)
    {
        try {

            $user_query = Users::whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from('users_wallet')
                    ->where('currency', $id)
                    ->whereRaw('users_wallet.user_id = users.id');
            });
            foreach ($user_query->cursor() as $user) {
                if (UsersWallet::where('user_id',$user->id)->where('currency', $id)->exists()) {
                    continue;
                }
                UsersWallet::unguarded(function () use ( $user,$id) {
                    UsersWallet::create([
                        'user_id' => $user->id,
                        'currency' => $id,
                        'create_time' => time(),
                    ]);
                });
            }
        } catch (\Exception $exception) {
            return false;
        }

    }


}