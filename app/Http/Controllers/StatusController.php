<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function status(Request $request, $id)
    {
        try {

           $statusid = Status::findorfail($id);
           
           $statusid ->name = $request->name;
            $statusid ->save();
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('Error', ['error' => $th->getMessage()]);
        }
        return $this->sucess("updated sucessfully",$statusid );
    }
}
