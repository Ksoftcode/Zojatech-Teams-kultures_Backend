<?php

namespace App\Http\Controllers;
use App\Http\Requests\CrudRequest;
use App\Models\Crud;

use Illuminate\Http\Request;

class CrudController extends Controller
{
      // Register 
      public function create(Request $request){
        $request->validate([
        'country'=>'required|string',
        'state'=>'required|string',
        'facebook'=>'required|string',
        'instagram'=> 'required|string',
        'linkdin'=>'required|string|min:6',
        
        ]);
        $user=new Crud([
            'country'=>$request->country,
            'state'=>$request->state,
            'facebook'    =>$request->facebook,
            'instagram' =>$request->instagram,
            'linkdin'   =>$request->linkdin,
        
        ]);
        $user-> save();

return response()->json(['message'=>'user has been registerd',$user],200);



    // public function create(CrudRequest $request)
    // {
    //     try {
    //         //code...
    //         $requestCrud = $request->all();
    //         $createCrud = Crud::create( $requestCrud);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    // return response()->json(['message'=>'added sucessfully', $requestCrud],200);
          
    //         // return response('success','added sucessfully', $requestCrud );

    //     }
    //     // return $this->createdResponse("added sucessfully",  $requestCrud);
    }
    public function update(CrudRequest $request, $id)
    {
        try {

            $findCrud = Crud::findorfail($id);
            $findCrud->country = $request->country ;
            $findCrud->state = $request->state;
            $findCrud->facebook = $request->facebook;
            $findCrud->instagram = $request->insagram;
            $findCrud->linkdin = $request->linkdin;
            $findCrud->save();
        } 
        
        catch (\Throwable $th) {
            //throw $th;
         return response()->json(['message'=>'updated sucessfully'],200);
           
            // return response(['success','added sucessfully', $findCrud ]);

        }
        // return $this->successResponse("updated sucessfully",  $findCrud);
    }
}
