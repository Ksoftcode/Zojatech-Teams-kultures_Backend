<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function fileupload(Request $request)
    {
        $file = new File();
        $file->files = $request->file('file')->store('apiFile');
        $result = $file->save();
        if ($result) {
            return ['result' => 'file uploaded'];
        } else {
            return ['result' => 'file upload failed'];
        }
    }
}
