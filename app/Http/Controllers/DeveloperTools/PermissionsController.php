<?php

namespace App\Http\Controllers\DeveloperTools;

use Illuminate\Database\Eloquent\ModelNotFoundException as ME;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Validator;
use Auth;
use DB;

class PermissionsController extends Controller
{
    
    public function init_list(){
        return response()->json([
            'data'  =>  Permission::orderBy('id', 'desc')->paginate(10)
        ]);
    }
    
    public function search_list(Request $req){
        return response()->json([
            'data'  =>  Permission::where('name', 'LIKE', '%'.$req->keyword.'%')->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function create(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4|unique:permissions'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            $u = Permission::create([
                'name'  =>  $req->name,
                'guard_name' =>  'api'
            ]);
            DB::commit();
            return response()->json([
                'text'  =>  'Permission has been created.'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in creating new Permission.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function update(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4|unique:permissions,name,'.$req->id,
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            try{
                $perms = Permission::findOrFail($req->id);
                $perms->name = $req->name;
                $perms->save();
                DB::commit();
                return response()->json([
                    'text'  =>  'Permission has been updated.'
                ]);
            }catch(ME $ee){
                DB::rollback();
                return response()->json([
                    'errors'    =>  [ 'Permission doesnt exists.' ],
                ],400);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in updating a Permission.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $req){
        DB::beginTransaction();
        try{
            $perm = Permission::findOrFail($req->id);
            $perm->delete();
            DB::commit();
            return response()->json([
                'text'  =>  'Permission has been deleted.'
            ]);
        }catch(ME $ee){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'Permission doesnt exists.' ],
            ],400);
        }
    }

}
