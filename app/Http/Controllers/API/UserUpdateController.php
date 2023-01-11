<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;

class UserUpdateController extends BaseController
{
    public function user_edit($uuid) {
        $details = User::select('id', 'name', 'contact', 'uuid')
                    ->where('uuid', $uuid)
                    ->first();

        if (!is_null($details)) {
            return $this->sendResponse($details, 'Data retrieved successfully for update.');
        }

        else {
            return $this->sendError('Record not found or somthing went wrong.');
        }
    }

    public function user_update(Request $request, User $user) {
        // Input validation
        $request->validate([
            'name' => 'required|string|max:80',
            'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,contact,' . $user->id
        ]);

        $data['name'] = $request->name;
        $data['contact'] = $request->contact;

        // Update record
        $user->update($data);

        return $this->sendResponse($user, 'Record updated successfully');
    }
}
