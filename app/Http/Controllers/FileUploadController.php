<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function fileupload(Request $request){
   $file=new users();
        $file->file=$request->file('file')->store('apifile');
        $result=$file->save();
        if ($file){
            return["result"=>"file uploaded"];
        }else{
            return["result"=>"file upload failed"];
  }

    }        
}
