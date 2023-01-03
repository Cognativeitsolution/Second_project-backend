<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\UserResource;

class AdminDashboardController extends BaseController
{
    public function getRecentAgencies(){
        $data = User::where('is_agency', 1)
            ->select('id','name','created_at')
            ->take(3)
            ->latest()
            ->get();

        return $this->sendResponse($data, 'Records retrieved successfully.');
    }
}
