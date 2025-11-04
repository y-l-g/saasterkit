<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Socialite;

use App\Mail\LoginLinkMail;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store as Session;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\User as SocialiteUser;

final readonly class HandleProviderCallbackController
{
    public function __construct(
        private StatefulGuard $guard,
        private Session $session
    ) {}

    public function __invoke(SocialiteFactory $socialite, string $provider): RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = $socialite->driver($provider);
        $socialiteUser = $driver->stateless()->user();
        assert($socialiteUser instanceof SocialiteUser);

        if ($this->guard->check()) {
            return $this->handleAuthenticatedUser($socialiteUser, $provider);
        }

        return $this->handleGuestUser($socialiteUser, $provider);
    }

    private function handleAuthenticatedUser(SocialiteUser $socialiteUser, string $provider): RedirectResponse
    {
        /** @var User $user */
        $user = $this->guard->user();

        if ($user->email === $socialiteUser->getEmail()) {
            $user->addSocialAccount($socialiteUser, $provider);

            return $this->redirectAfterAccountLinking();
        }

        return to_route('profile.edit')->with('error', 'The email address of this account does not match the one from the provider.');
    }

    private function handleGuestUser(SocialiteUser $socialiteUser, string $provider): RedirectResponse
    {
        $socialAccount = SocialAccount::findByProviderIdentity(
            provider: $provider,
            providerId: (string) $socialiteUser->getId()
        );

        if ($socialAccount) {
            return $this->loginAndRedirect($socialAccount->user);
        }

        $userWithSameEmail = User::query()->where('email', $socialiteUser->getEmail())->first();

        if ($userWithSameEmail) {
            return $this->sendLoginLinkandRedirect($userWithSameEmail);
        }

        $newUser = User::createFromSocialite($socialiteUser, $provider);

        return $this->loginAndRedirect($newUser);
    }

    private function loginAndRedirect(User $user): RedirectResponse
    {
        $this->guard->login($user);

        return redirect()->intended('/dashboard');
    }

    private function redirectAfterAccountLinking(): RedirectResponse
    {
        $this->session->regenerate();

        return to_route('profile.edit')->with('success', 'Your provider account has been linked.');
    }

    private function sendLoginLinkandRedirect(User $user): RedirectResponse
    {
        Mail::to($user)->queue(new LoginLinkMail($user));

        return to_route('login')->with('info', 'An account with this email already exists. We have sent a login link to your email address.');
    }
}
