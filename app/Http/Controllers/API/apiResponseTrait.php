<?php

namespace App\Http\Controllers\API;

trait apiResponseTrait
{
    public function apiResponse($data = null, $msg = null, $status = null)
    {
        $array = [
            'message' => $msg,
            'data' => $data,
            'status' => $status,
        ];
        return response($array, 200);
    }
}
