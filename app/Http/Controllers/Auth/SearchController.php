<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\search;
use App\Models\users;

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
}
}
