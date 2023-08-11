<?php

namespace App\Traits;

trait response
{
    function success($message, $data, $code = 200)
    {
        return response()->json(
            [
                'status' => 'Successful',
                'message' => $message,
                'data' => [
                    'user' => $data['user'],
                    'token' => $data['token']
                ]
            ],
            $code,
        );
    }

    function error($message, $data, $code)
    {
        return response()->json(
            [
                'status' => 'Failed',
                'message' => $message,
                'data' => [
                    'user' => '',
                    'token' => ''
                ]
            ],
            $code,
        );
    }
}
