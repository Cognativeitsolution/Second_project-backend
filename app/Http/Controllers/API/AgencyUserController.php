<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Validator;use App\Models\AgencyUser;


use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AgencyUserController extends BaseController
{
    //
}
