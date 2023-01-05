<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;

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

    public function getRecentActivities() {
        $records = User::select('users.id AS user_id',
                            'users.name',
                            'users.email',
                            DB::raw("COALESCE(
                                IF(users.is_agency = 0, NULL, 'Agency'),
                                IF(users.is_company = 0, NULL, 'Company'),
                                IF(users.is_worker = 0, NULL, 'Worker'),
                                'Super Admin'
                            ) AS account_type"), 
                            'logs.table_name',
                            'logs.action AS activity',
                            'logs.updated_at AS activity_date')
                ->join('logs', 'logs.created_by', 'users.id')
                ->take(10)
                ->orderBy('logs.id', 'DESC')
                ->get();

        return $this->sendResponse($records, 'Records retrieved successfully.');
    }
}
