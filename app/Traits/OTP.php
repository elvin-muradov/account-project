<?php

namespace App\Traits;

use App\Models\User\OtpCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait OTP
{
    private function generateOTP(): int
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function checkOTP($type, $phone, $code): Model|null
    {
        return OtpCode::query()
            ->where('type', '=', $type)
            ->where('phone', '=', $phone)
            ->where('code', '=', $code)
            ->where('expired_at', '>', now())
            ->first();
    }
}
