<?php

namespace App\Http\Controllers\API;


use App\Models\SubAgency;
use App\Http\Requests\StoreSubAgencyRequest;
use App\Http\Requests\UpdateSubAgencyRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\UserResource;

use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Str;
use Carbon\Carbon;


class SubAgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubAgencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubAgencyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubAgency  $subAgency
     * @return \Illuminate\Http\Response
     */
    public function show(SubAgency $subAgency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubAgency  $subAgency
     * @return \Illuminate\Http\Response
     */
    public function edit(SubAgency $subAgency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubAgencyRequest  $request
     * @param  \App\Models\SubAgency  $subAgency
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubAgencyRequest $request, SubAgency $subAgency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubAgency  $subAgency
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubAgency $subAgency)
    {
        //
    }
}
