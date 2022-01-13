<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException as ME;
use Illuminate\Http\Request;
use App\Models\Contact;
use Validator;
use Exception;
use Auth;
use DB;

class ContactController extends Controller
{
    
    public function create(Request $req){
        $valid = Validator::make($req->all(),[
            'name'          =>  'required|string',
            'facebook'      =>  'nullable|string',
            'address'       =>  'required|string',
            'phone_number'  =>  'required|array'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            $user = Auth::user();
            $contact = $user->contact()->firstOrCreate([
                'name'      =>  $req->name,
                'facebook'  =>  $req->facebook,
                'address'   =>  $req->address
            ]);
            foreach($req->phone_number as $phone){
                $contact->phone_numbers()->firstOrCreate([
                    'value' =>  $phone['value']
                ]);
            }
            DB::commit();
            return response()->json([
                'text'  =>  'Contact has been added.'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'Something went wrong.' ],
                'message'   =>  $e->getMessage()
            ],500);
        }
    }

    public function update(Request $req){
        $valid = Validator::make($req->all(),[
            'id'            =>  'required|numeric',
            'name'          =>  'required|string',
            'facebook'      =>  'nullable|string',
            'address'       =>  'required|string',
            'phone_numbers'  =>  'required|array'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            try{
                $contact = Contact::findOrFail($req->id);
                $contact->update([
                    'name'      =>  $req->name,
                    'facebook'  =>  $req->facebook,
                    'address'   =>  $req->address
                ]);
                $contact->phone_numbers()->forceDelete();
                foreach($req->phone_numbers as $phone){
                    $contact->phone_numbers()->firstOrCreate([
                        'value' =>  $phone['value']
                    ]);
                }
                DB::commit();
                return response()->json([
                    'text'  =>  'Contact has been added.'
                ]);
            }catch(ME $me){
                DB::rollback();
                return response()->json([
                    'errors'    =>  [ 'Contact not found.' ]
                ],404);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'Something went wrong.' ],
                'message'   =>  $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $req){
        try{
            $contact = Contact::findOrFail($req->id);
            $contact->delete();
            DB::commit();
            return response()->json([
                'text'  =>  'Contact has been deleted.'
            ]);
        }catch(ME $e){
            DB::rollback();
            return reponse()->json([
                'errors'    =>  [ 'Contact not found.' ]
            ],404);
        }
    }
    
    public function search(Request $req){
        $keyword = $req->keyword;
        $user = Auth::user();
        return response()->json([
            'data'  =>  $user->contact()->where('name', 'LIKE', '%'.$req->keyword.'%')->orWhereHas('phone_numbers', function($q) use ($keyword){
                return $q->where('value', 'LIKE', '%'.$keyword.'%');
            })->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function init_list(){
        $user = Auth::user();
        return response()->json([
            'data'  =>  $user->contact()->orderBy('id', 'desc')->paginate(10)
        ]);
    }

}
