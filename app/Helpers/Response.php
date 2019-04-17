<?php

namespace App\Helpers;
class Response
{
    public static function apiSuccessResponse($code, $message, $data = [])
    {
        $responseArray['message'] = $message;
        $responseArray['status'] = true;
        $responseArray['data'] = $data;

        return response()->json($responseArray);
    }

    public static function apiErrorResponse($code, $message, $data = [])
    {
        $responseArray['message'] = $message;
        $responseArray['status'] = false;
        $responseArray['data'] = $data;
        
        return response()->json($responseArray);
    }
}