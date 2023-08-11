<?php

namespace App\Http\Controllers;

use App\Events\SignupMail;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Requests\SignupRequest;
use App\Models\Visiting_device;
use App\Traits\response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    use response;

    public function signup(SignupRequest $request)
    {
        try {
            $user = $request->Validated();
            $userId = $this->create(time());
            $user['password'] = Hash::make($user['password']);
            $user['userId'] = $userId;
            $user = User::create($user);
            $data = [
                'user' => [
                    'name' => $user['name'],
                    'userId' => $user['userId'],
                    'email' => $user['email'],
                    'email_verified_at' => $user['email_verified_at'],
                ],
                'token' => $user->createToken('bearer ')->plainTextToken,
            ];
            $agent = new Agent;
            $userDeviceInfo = [
                'userId' => $user['userId'],
                'device' => $agent->device(),
                'deviceType' => $agent->deviceType(),
                'platform' => $agent->platform(),
                'platform_version' => $agent->version($agent->platform()),
                'browser' => $agent->browser(),
                'browser_version' => $agent->version($agent->browser()),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            Visiting_device::create($userDeviceInfo);
        } catch (\Throwable $th) {
            return $this->error('registration failed', $th->getMessage() ,  500);
        }
        try {
            event(new SignupMail($user['email'], $userId));
        } catch (\Throwable $th) {
            $error = $th;
        };
        return $this->success('Account created', $data, 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $request->validated();
            Auth::attempt($user);
            $user = Auth::user();
            $data = [
                'user' => Auth::user(),
                'token' => $user->createToken('Bearer ')->plainTextToken
            ];
            $agent = new Agent;
            $userDeviceInfo = [
                'userId' => $user['userId'],
                'device' => $agent->device(),
                'deviceType' => $agent->deviceType(),
                'platform' => $agent->platform(),
                'platform_version' => $agent->version($agent->platform()),
            ];

            Visiting_device::updateOrCreate(
                $userDeviceInfo,
                [
                    'updated_at' => now(),
                    'browser' => $agent->browser(),
                    'browser_version' => $agent->version($agent->browser()),
                ]
            );

            return $this->success('login success', $data, 200);
        } catch (\Throwable $th) {
            return $this->error('Failed login', ' ',  404);
            // throw $th;
        }
    }

    // Create unique Id
    private function create($time)
    {
        $random = rand(1000, 9999);
        $random =  ceil(round(sqrt($random * 9999), 4));
        $random = "REA" . $random;

        return ($random);
    }
}
