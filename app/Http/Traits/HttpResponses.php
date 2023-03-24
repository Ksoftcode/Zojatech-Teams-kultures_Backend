<?php
namespace App\Traits;
<<<<<<< HEAD
=======

>>>>>>> 12669577543cd7ea57a6093f205d79590254ed1e
trait HttpResponses{
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data, $message = null, $code)
    {
        return response()->json([
            'status' => 'Error has occured',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    



}