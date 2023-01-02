<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);

        $roles = Role::where('admin_id_for_role', Auth::user()->id)->select('id','name','uuid')->first();
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id", $roles->id)
            ->get();

        return [
            'id'  => $this->id,
            'uuid' => $this->uuid,
            'name'  => $this->name,
            'email'  => $this->email,
            'status' => $this->status,
            'is_super_admin'  => $this->is_super_admin,
            'is_agency'  => $this->is_agency,
            'is_company'  => $this->is_company,
            'is_worker'  => $this->is_worker,
            'token'  => $this->createToken('employment')->plainTextToken,
            'roles'   => $roles,
            'permissions'   => $rolePermissions->pluck('name') ?? [],
        ];
    }
}
