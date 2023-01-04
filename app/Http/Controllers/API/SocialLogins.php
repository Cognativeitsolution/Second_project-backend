<?php

namespace App\Http\Controllers\API;

use App\Models\Logs;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;
use GuzzleHttp\Exception\ClientException;

class SocialLogins extends Controller
{
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);

        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function handleProviderCallback($provider) {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $user->email
            ],
            [
                'uuid' => rand(10000,9999999),
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => now(),
                'is_agency' => 1,
                'is_company' => 0,
                'is_worker' => 0,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->id,
            ],
            [
                'provider' => $provider,
                'provider_id' => $user->id,
                'user_id' => $userCreated->id
            ]
        );

        $agency = Role::updateOrCreate(
            [
                'name' => 'Agency Admin',
                'admin_id_for_role' => $userCreated->id
            ],
            [
                'name' => 'Agency Admin',
                'admin_id_for_role' => $userCreated->id,
                'uuid' => rand(10000,9999999)
            ]
        );

        // Agency this is all permission for Agency Admin
        $permissions = Permission::whereNotIn('id',[5,6,7,8]) // Remove Agency Create permission during create agency account
            ->pluck('id','id')->all();

        $agency->syncPermissions($permissions);

        $userCreated->assignRole([$agency->id]);

        Logs::add_log(User::getTableName(), $userCreated->id, $userCreated, 'add', '');

        Auth::login($userCreated, true);

        return new UserResource(Auth::user());
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider) {
        if (!in_array($provider, ['google', 'facebook', 'linkedin'])) {
            return response()->json(['error' => 'Please login using google, facebok or linkedin'], 422);
        }
    }
}
