<?php

namespace App\Http\Controllers\DeveloperTools;

use Illuminate\Database\Eloquent\ModelNotFoundException as ME;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Validator;
use Auth;
use DB;

class UsersController extends Controller
{
    
    public function init_list(){
        return response()->json([
            'data'  =>  User::orderBy('id', 'desc')->paginate(10)
        ]);
    }
    
    public function search_list(Request $req){
        return response()->json([
            'data'  =>  User::where('name', 'LIKE', '%'.$req->keyword.'%')->orWhere('email', 'LIKE', '%'.$req->keyword.'%')->orderBy('id', 'desc')->paginate(10)
        ]);
    }

    public function create(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4',
            'email' =>  'required|email|unique:users',
            'password' => 'required|string|min:8',
            'roles' =>  'required|array',
            'permissions'   =>  'nullable|array'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            $u = User::create([
                'name'  =>  $req->name,
                'email' =>  $req->email,
                'password'  =>  bcrypt($req->password)
            ]);
            //assign role
            $roles = array_column($req->roles, 'name');
            $u->assignRole($roles);
            //assign permissions
            if(count($req->permissions)){
                $perms = array_column($req->permissions, 'name');
                $u->syncPermissions($perms);
            }
            DB::commit();
            return response()->json([
                'text'  =>  'User has been created.'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in creating new user.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function update(Request $req){
        $valid = Validator::make($req->all(),[
            'name'  =>  'required|string|min:4',
            'email' =>  'required|email|unique:users,email,'.$req->id,
            'password' => 'nullable|string|min:8',
            'roles' =>  'required|array',
            'permissions'   =>  'nullable|array'
        ]);
        if($valid->fails()){
            return response()->json([
                'errors'    =>  $valid->errors()
            ],400);
        }
        DB::beginTransaction();
        try{
            try{
                $user = User::findOrFail($req->id);
                $user->name = $req->name;
                $user->email = $req->email;
                //check if they want to change pass.
                if($req->password){
                    $user->password = bcrypt($req->password);
                }
                //assign new role
                $roles =array_column($req->roles,'name');
                $user->syncRoles($roles);
                //check if permission to be updated
                if(count($req->permissions)){
                    $perms = array_column($req->permissions, 'name');
                    $user->syncPermissions($perms);
                }
                $user->save();
                DB::commit();
                return response()->json([
                    'text'  =>  'User has been updated.'
                ]);
            }catch(ME $ee){
                DB::rollback();
                return response()->json([
                    'errors'    =>  [ 'User doesnt exists.' ],
                ],400);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in updating a user.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

    public function ban_process(Request $req){
        DB::beginTransaction();
        try{
            try{
                $user = User::findOrFail($req->id);
                if($req->status === 'ban'){
                    $user->revokePermissionTo('can login');
                }else{
                    $user->givePermissionTo('can login');
                }
                DB::commit();
                return response()->json([
                    'text'  =>  'User has been updated.'
                ]);
            }catch(ME $ee){
                DB::rollback();
                return response()->json([
                    'errors'    =>  [ 'User doesnt exists.' ],
                ],400);
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'errors'    =>  [ 'There is a problem in updating a user.' ],
                'msg'       =>  $e->getMessage()
            ],500);
        }
    }

}
