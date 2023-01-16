<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Validator;

//use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
	
    public function index()
    {
        $roles = Role::orderBy('id','DESC')
                    ->select('id','uuid','name','created_at','updated_at')
                    ->where('admin_id_for_role', Auth::user()->id)
                    ->get();

        $success['roles'] =  $roles;
        return $this->sendResponse($success, 'Roles retrieved successfully.');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::select('id','name')->get();
        $success['permission'] =  $permission;

        return $this->sendResponse($success, 'Create Roles and Permissions');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'max:30'],
            'permission' => 'required',
        ]);

        $uuid = $this->generateUUID(12);

        $data = [
            'uuid'  => $uuid,
            'name'  => $request->input('name'),
            'admin_id_for_role'  => Auth::user()->id,
            'guard_name'    => 'web'
        ];

        $role = Role::create( $data );

        $permission_ids = array_map('intval', explode(',', $request->input('permission')));

        for($i=0; $i < count($permission_ids); $i++){
            $dataSave = [
                'role_id'  => $role->id,
                'permission_id'  => $permission_ids[$i],
            ];
            DB::table('role_has_permissions')->insert($dataSave);
        }

        $success['name'] = $request->input('name') ;
        Logs::add_log("roles", $role->id, $data, 'add', '');
        return $this->sendResponse($success, 'Role has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $uuid_record = Role::select('id','uuid')
            ->where('roles.uuid', $uuid)
            ->first();

        if( $uuid_record == false){
            return $this->sendError('Role not found.');
        }

        $role = Role::find($uuid_record->id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->select('id','name')
            ->where("role_has_permissions.role_id",$uuid_record->id)
            ->get();

        $data['role'] = $role;
        $data['permissions'] = $rolePermissions;
        return $this->sendResponse($data, 'Role and permissions show successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {

        $uuid_record = Role::select('id','uuid')
            ->where('roles.uuid', $uuid)
            ->first();

        if( $uuid_record == false){
            return $this->sendError('Role edit not found.');
        }

        $role = Role::find($uuid_record->id);
        $permission = Permission::select('id','name')->get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$uuid_record->id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        $data['role'] = $role;
        $data['given_role_permissions'] = $rolePermissions;
        $data['all_permission'] = $permission;

        return $this->sendResponse($data, 'Role and permissions edit successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $this->validate($request, [
            'name' => ['required', 'max:100'],
            'permission' => 'required',
        ]);

        $uuid_record = Role::select('id','uuid')
            ->where('roles.uuid', $uuid)
            ->first();

        if( $uuid_record == false){
            return $this->sendError('Role update not found or somthing went wrong.');
        }

        $role = Role::find($uuid_record->id);

        $role->name = $request->input('name');
        $role->save();

        $data['role'] = Role::find($uuid_record->id);

        $permission_ids = array_map('intval', explode(',', $request->input('permission')));

        DB::table('role_has_permissions')->where('role_id', $uuid_record->id)->delete();

        for($i=0; $i < count($permission_ids); $i++){
            $dataSave = [
                'role_id'  => $role->id,
                'permission_id'  => $permission_ids[$i],
            ];
            DB::table('role_has_permissions')->insert($dataSave);
        }

        // Get records after update role and permissions
        $role = Role::find($uuid_record->id);
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$uuid_record->id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        $data['role'] = $role;
        $data['given_role_permissions'] = $rolePermissions;

        Logs::add_log("roles", $role->id, $request->all(), 'edit', 1);
        return $this->sendResponse($data, 'Role and permissions update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $uuid_record = Role::where('roles.uuid', $uuid)->first();

        if( $uuid_record == false){
            return $this->sendError('Role delete not found or somthing went wrong.');
        }

        DB::table("roles")->where('id',$uuid_record->id)->delete();

        $data['success'] = true;

        Logs::add_log(Role::getTableName(), Auth::user()->id, $uuid_record, 'delete', '');
        return $this->sendResponse($data, 'Role deleted successfully.');

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function generateUUID($length) {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }
}
