<?php

namespace App\Http\Controllers\API;

trait apiResponseTrait
{
public function apiResponse($data =null , $msg = null , $status = null){
 $array = [
     'data' => $data ,
     'message' => $msg ,
     'status' => $status ,
 ];
 return response($array , $status);
}
}
