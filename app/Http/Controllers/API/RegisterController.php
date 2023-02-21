<?php

namespace App\Http\Controllers\API;
   
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

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:80',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => 'required|min:6|max:15',
            'c_password' => 'required|same:password|min:6|max:15',
            'sin_number' => 'required|max:15',
        ]);
   
        $input = $request->all();
        $code = random_int(10000, 99999);

        $is_agency = $input['is_agency'] ? 1 : 0 ;
        $is_company = $input['is_company'] ? 1 : 0 ;
        $is_worker = $input['is_worker'] ? 1 : 0 ;

        $input['password'] = bcrypt($input['password']);
        $input['verify_code'] = $code;

        $input['is_agency'] = $is_agency;
        $input['is_company'] = $is_company;
        $input['is_worker'] = $is_worker;
        $input['uuid'] = rand(10000,9999999);

        $user = User::create($input);

        // Agency Main Account
        if($is_agency == 1){
            $agency = Role::create([
                'name' => 'Agency Admin',
                'admin_id_for_role' => $user->id,
                'uuid' => rand(10000,9999999)
            ]);

            // Agency this is all permission for Agency Admin
            $permissions = Permission::whereNotIn('id',[5,6,7,8]) // Remove Agency Create permission during create agency account
                ->pluck('id','id')->all();

            $agency->syncPermissions($permissions);

            $user->assignRole([$agency->id]);

        }elseif($is_worker == 1){
            // Company Worker
            $role = Role::create([
                'name' => 'Worker',
                'admin_id_for_role' => $user->id,
                'uuid' => rand(10000,9999999)
            ]);

            $permissions = Permission::whereIn('id',[13]) // 13 is Worker list
                            ->pluck('id','id')->all(); // this is worker register permission

            $role->syncPermissions($permissions);

            $user->assignRole([$role->id]);

        }else{
            // Company Admin
            $role_admin = Role::create([
                'name' => 'Company Admin',
                'admin_id_for_role' => $user->id,
                'uuid' => rand(10000,9999999)
            ]);

            $permissions = Permission::whereIn('id',[1,2,3,4,13,14,15,16])
                            ->pluck('id','id')->all(); // this is company admin register permission
                                                       // Role, and Worker

            $role_admin->syncPermissions($permissions);

            $user->assignRole([$role_admin->id]);
        }

        $success['token'] =  $user->createToken('employment')->plainTextToken;
        $success['name'] =  $user->name;

        $details['email'] = $user->email ;
        $details['verify_code'] = $code;

        // dispatch(new SendEmailJob($user->email, $details));
        // Change to Normal Email

        // Mail is working when goto live please remove commit

        Mail::send('emails.account_verify', $details, function($message) use ($user) {
            $message->to($user["email"] , $user["name"])
                ->subject('Employment Agency Tool - Please Verify Email Address');
        });

        Logs::add_log(User::getTableName(), $user->id, $input, 'add', '');
        return $this->sendResponse($success, 'Welcome, You are registered successfully, please check your email to verify your account.');

    }

    public function resendCode(Request $request){

        $details = $request->validate([
            'email' => 'required|string|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if( $user == false){
            return $this->sendError('Email address not found or something went wrong.');
        }

        $code = random_int(10000, 99999);
        $input['verify_code'] = $code;

        $details['email'] = $user->email ;
        $details['verify_code'] = $code;

        // Now send email and update user code
        $user->update(['verify_code' => $code ]);

        $success['email'] =  $user->email;

        Mail::send('emails.account_verify', $details, function($message) use ($user) {
            $message->to($user["email"] , $user["name"])
                ->subject('Employment Agency Tool - Please Verify Email Address');
        });

        return $this->sendResponse($success, 'Please check your email to verify your account.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $details = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        // New Code for Live Check Active ?? Block Status ...
        $user_exists_and_inactive = User::where('email', $details['email'])
            ->select('id','status')
            ->where('status', 0)
            ->first();

        if( $user_exists_and_inactive == true){
            return $this->sendError('Your account is block by Super Admin.');
        }

        // Check user email exists ?? or not
        $user = User::select(
            'id',
            'name',
            'email',
            'email_verified_at',
            'verify_code')
            ->where('email', $details['email'])->first();

        if ($user === null) {
            return [
                'message' => 'Email not found or something went wrong.'
            ];
        }

        // Check if user email is not verified
        if ($user->email_verified_at === null) {

            $verify_code = random_int(10000, 99999);

            $user->update(['verify_code' => $verify_code]);

            $details['name'] = $user->name;
            $details['verify_code'] = $verify_code;

            Mail::send('emails.account_verify', $details, function($message) use($user) {
                $message->to($user->email)
                    ->subject('Employment Agency Tool - Please Verify Email Address');
            });

            return [
                'message' => 'Your email is not verified. We have send a code in our email. Please check your email.'
            ];
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $request->getClientIp()
            ]);

            Logs::add_log(User::getTableName(), Auth::user()->id, $request->all(), 'login', '');
            return new UserResource(Auth::user());

        }else{

            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function destroy(Request $request)
    {
        if ($request->bearerToken()) {

            $accessToken = $request->bearerToken();
            $token = PersonalAccessToken::findToken($accessToken);

            if($token){
                $user = $token->tokenable;
                $user->tokens()->where('tokenable_id', $user->id)->delete();
                return response()->json(['message' => 'Logout successfully.']);
            }
            return response()->json(['message' => 'Logout failed.']);

        }

    }

    public function verify_email(Request $request){

        $input = $request->all();

        $request->validate([
            'verify_code' => 'required|min:4|max:10'
        ]);

        $user = User::select('id','email_verified_at', 'verify_code')
                        ->where('verify_code', $input['verify_code'])
                        ->first();

        if ($user) {
            $user->update([
                'email_verified_at' => now(),
                'verify_code' => null
            ]);

            $response = [
                'status' => 1,
                'message' => 'Email verified successfully'
            ];
        }else{
            $response = [
                'status' => 0,
                'message' => 'Code not found'
            ];
        }
        return response()->json($response);
    }

    public function user_remove($uuid)
    {
        $user = User::select('id')->where('uuid', $uuid)->first();

        if( $user == false){
            return $this->sendError('Record not found or somthing went wrong.');
        }

        $user = User::find($user->id);
        $user->delete();

        $data['success'] = true;
        return $this->sendResponse($data, 'Record has been deleted successfully.');
    }

    public function getAgencies() {
        $search = request('search');

        if (!empty($search)) {
            $data = User::select(
                'id',
                'uuid',
                'name',
                'email',
                'contact',
                //'sin_number',
                'is_agency'
            )
                ->where('users.is_agency', 1)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%'.$search.'%')
                        ->orWhere('users.email', 'like', '%'.$search.'%')
                        ->orWhere('users.contact', 'like', '%'.$search.'%');
                })
                ->latest()
                ->paginate(10);
        }else {
            $data = User::select(
                'id',
                'uuid',
                'name',
                'email',
                'contact',
                //'sin_number',
                'is_agency'
            )
                ->where('users.is_agency', 1)
                ->latest()
                ->paginate(10);
        }

        foreach($data as $key => $value){
            $data[$key]['personal_email'] = $this->obfuscate_email($value->email);
            $data[$key]['personal_contact'] = Str::mask($value->contact, '*', 4, 5);
            //$data[$key]['personal_sin_number'] = Str::mask($value->sin_number, '*', 4, 5);
            unset( $data[$key]->email );
            unset( $data[$key]->contact );
            //unset( $data[$key]->sin_number );
        }

        return $this->sendResponse($data, 'Record retrieved successfully.');
    }

    public function getCompanies() {
        $search = request('search');

        if (!empty($search)) {
            $data = User::select(
                'id',
                'uuid',
                'name',
                'email',
                'contact',
                //'sin_number',
                'is_company'
            )
                ->where('users.is_company', 1)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%'.$search.'%')
                        ->orWhere('users.email', 'like', '%'.$search.'%')
                        ->orWhere('users.contact', 'like', '%'.$search.'%');
                })
                ->latest()
                ->paginate(10);
        }else {
            $data = User::select(
                'id',
                'uuid',
                'name',
                'email',
                'contact',
                //'sin_number',
                'is_company'
            )
            ->where('users.is_company', 1)
                ->latest()
                ->paginate(10);
        }

        foreach($data as $key => $value){
            $data[$key]['personal_email'] = $this->obfuscate_email($value->email);
            $data[$key]['personal_contact'] = Str::mask($value->contact, '*', 4, 5);
            //$data[$key]['personal_sin_number'] = Str::mask($value->sin_number, '*', 4, 5);
            unset( $data[$key]->email );
            unset( $data[$key]->contact );
            //unset( $data[$key]->sin_number );
        }


        return $this->sendResponse($data, 'Record retrieved successfully.');
    }

    public function deleteAccounts(Request $request){

        $ids = array_map('intval', explode(',', $request->input('ids')));
        User::whereIn('id', $ids)->delete();
        return $this->sendResponse("", 'Record deleted successfully.');

    }

    public function changeStatus(Request $request){
        $uuid = $request->input('uuid');

        $uuid_record = User::where('uuid', $uuid)->select('id','uuid','status')->first();

        if( $uuid_record == false){
            return $this->sendError('Record not found or something went wrong.');
        }

        if($uuid_record->status == 1){
            $status = 0 ;
            $message = "Record Block successfully.";
        }else{
            $status = 1 ;
            $message = "Record Active successfully.";
        }

        $uuid_record->update(['status' => $status]);
        $data = User::where('uuid', $uuid)->first();
        $data['message'] = $message;

        Logs::add_log(User::getTableName(), Auth::user()->id, $data, 'edit', '');
        return $this->sendResponse($data, $message);
    }
}
