<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\SignupConfirmation;
use App\Traits\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Access\AuthorizationException;
use Laravel\Fortify\Contracts\VerifyEmailResponse;

class VerifyUserEmail extends Controller
{
    use Response;

    public function verifyMail(Request $request, $userId)
    {
        try {
            // GETTING THE URI FROM THE REQUEST
            $uri = $request->getUri();

            // SEARCHING FOR A MATCH ELSE ERROR
            $find = DB::table('verify_email_blacklist')->where('userId', '=', $userId)->first();
            if (!$find) {
                return $this->error('Not found', ' ', 404);
            }
            if ($uri != $find->link) {
                return $this->error('Not found', ' ', 404);
            }

            // DELETE IT FROM THE TEMPORARY STORAGE AND ADD TIME TO THE USER TABLE
            $user = DB::table('verify_email_blacklist')->where('userId', '=', $userId)->delete();

            // UPDATING THE USERS TABLE (EMAIL VERIFIED)
            $user = DB::table('users')->where('userId', '=', $userId)->update(['email_verified_at' => now()]);
            return !$user
                ?  $this->error('mess', ' ', 500)
                :   view('verified');

        } catch (\Illuminate\Database\QueryException $e) {
            
            $this->error($e, ' ', 422);
        }
    }
}
