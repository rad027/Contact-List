<?php

namespace App\Http\Controllers\DeveloperTools;

use Illuminate\Database\Eloquent\ModelNotFoundException as ME;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Validator;
use Auth;
use DB;

class RolesController extends Controller
{
    
    public function init_list(){
        return response()->json([
            'data'  =>  Role::orderBy('id', 'desc')->paginate(10)
        ]);
    }
    
    public function search_list(Request $req){
        return response()->json([
            'data'  =>  Role::where('name', 'LIKE', '%'.$req->keyword.'%')->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function create(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4|unique:roles'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            $u = Role::create([
                'name'  =>  $req->name,
                'guard_name' =>  'api'
            ]);
            DB::commit();
            return response()->json([
                'text'  =>  'Role has been created.'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in creating new Role.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function update(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4|unique:roles,name,'.$req->id,
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            try{
                $role = Role::findOrFail($req->id);
                $role->name = $req->name;
                $role->save();
                DB::commit();
                return response()->json([
                    'text'  =>  'Role has been updated.'
                ]);
            }catch(ME $ee){
                DB::rollback();
                return response()->json([
                    'errors'    =>  [ 'Role doesnt exists.' ],
                ],400);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in updating a Role.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $req){
        DB::beginTransaction();
        try{
            $perm = Role::findOrFail($req->id);
            $perm->delete();
            DB::commit();
            return response()->json([
                'text'  =>  'Role has been deleted.'
            ]);
        }catch(ME $ee){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'Role doesnt exists.' ],
            ],400);
        }
    }

}
