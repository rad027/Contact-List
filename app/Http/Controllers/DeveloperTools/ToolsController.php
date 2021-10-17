<?php

namespace App\Http\Controllers\DeveloperTools;

use Illuminate\Database\Eloquent\ModelNotFoundException as ME;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    
    public function role_list(){
        return response()->json([
            'data'  =>  Role::all()
        ]);
    }
    
    public function permission_list(){
        return response()->json([
            'data'  =>  Permission::all()
        ]);
    }

}
