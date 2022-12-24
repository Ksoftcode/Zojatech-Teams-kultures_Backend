<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\search;
use App\Models\users;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search=users::query();
        if ($request->query('keyword')) {
            $search= $search->where('title','like','%'.$request->query('keyword'))->paginate('10');
        }
        $search=$search->get();
        return response()->json([
            'message'=>'search successful',
                'data'=>$search
        ],200);
// } public function filter(Request $request)
// {
//     try {
//         //code...
//         $filter = DB::table('users,files')
//             ->when($request->firstname, function ($q, $firstname) {
//                 return $q->where('firstname', '=', $firstname);
//             })
//             ->when($request->lastname, function ($q, $lastname) {
//                 return $q->where('lastname', '=', $lastname);
//             })
//             ->when($request->username, function ($q, $username) {
//                 return $q->where('username', '=', $username);
//             })
//             ->when($request->files, function ($q, $files) {
//                 return $q->where('files', '=', $files);
//             })
            
            
//             ->orderByDesc('users.id, files.id')
//             ->get();
//     } catch (\Throwable $th) {
//         //throw $th;
//         return $this->badRequestResponse("Error", ['errror' => $th->getMessage()]);
//     }
//     return $this->successResponse('done', $filter);
}

}
