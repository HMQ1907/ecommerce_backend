<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Exceptions\EmailTakenException;
use Modules\Auth\Models\OAuthProvider;

class OAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config([
            'services.github.redirect' => route('oauth.callback', 'github'),
        ]);
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirect($provider)
    {
        return responder()->success([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ])->respond();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string  $driver
     * @return \Illuminate\Http\Response
     */
    public function handleCallback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        $user = $this->findOrCreateUser($provider, $user);

        $this->guard()->setToken(
            $token = $this->guard()->login($user)
        );

        return response()->view('oauth.callback', [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->guard()->getPayload()->get('exp') - time(),
        ]);
    }

    /**
     * @param  string  $provider
     * @param  \Laravel\Socialite\Contracts\User  $sUser
     * @return \App\Models\User
     */
    protected function findOrCreateUser($provider, $user)
    {
        $oauthProvider = OAuthProvider::where('provider', $provider)
            ->where('provider_user_id', $user->getId())
            ->first();
        if ($oauthProvider) {
            $oauthProvider->update([
                'access_token' => $user->token,
                'refresh_token' => $user->refreshToken,
            ]);

            return $oauthProvider->user;
        }

        if (User::where('email', $user->getEmail())->exists()) {
            throw new EmailTakenException();
        }

        return $this->createUser($provider, $user);
    }

    /**
     * @param  string  $provider
     * @param  \Laravel\Socialite\Contracts\User  $sUser
     * @return \App\Models\User
     */
    protected function createUser($provider, $sUser)
    {
        $user = User::create([
            'name' => $sUser->getName(),
            'email' => $sUser->getEmail(),
            'email_verified_at' => now(),
        ]);

        $user->oauthProviders()->create([
            'provider' => $provider,
            'provider_user_id' => $sUser->getId(),
            'access_token' => $sUser->token,
            'refresh_token' => $sUser->refreshToken,
        ]);

        return $user;
    }
}