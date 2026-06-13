<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Socialite;

use App\Models\SocialAccount;
use App\Models\User;
use App\Notifications\LoginLinkNotification;
use App\Support\EmailAddress;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store as Session;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

final readonly class HandleProviderCallbackController
{
    public function __construct(
        private StatefulGuard $guard,
        private Session $session
    ) {}

    public function __invoke(SocialiteFactory $socialite, string $provider): RedirectResponse
    {
        /** @var AbstractProvider $driver */
        $driver = $socialite->driver($provider);
        $socialiteUser = $driver->user();
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

        if (EmailAddress::matches($user->email, $socialiteUser->getEmail())) {
            $user->addSocialAccount($socialiteUser, $provider);

            return $this->redirectAfterAccountLinking($user);
        }

        return $this->redirectToProfile($user)
            ->with('error', 'The email address of this account does not match the one from the provider.');
    }

    private function handleGuestUser(SocialiteUser $socialiteUser, string $provider): RedirectResponse
    {
        $socialAccount = SocialAccount::findByProviderIdentity(
            provider: $provider,
            providerId: (string) $socialiteUser->getId()
        );

        if ($socialAccount) {
            return $this->loginOrChallengeTwoFactor($socialAccount->user);
        }

        $userWithSameEmail = User::query()
            ->where('email', EmailAddress::normalize($socialiteUser->getEmail()))
            ->first();

        if ($userWithSameEmail) {
            return $this->sendLoginLinkandRedirect($userWithSameEmail);
        }

        $newUser = User::createFromSocialite($socialiteUser, $provider);

        return $this->loginAndRedirect($newUser);
    }

    private function loginOrChallengeTwoFactor(User $user): RedirectResponse
    {
        if ($user->two_factor_secret) {
            session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => true,
            ]);
            event(new TwoFactorAuthenticationChallenged($user));

            return to_route('two-factor.login');
        }

        return $this->loginAndRedirect($user);
    }

    private function loginAndRedirect(User $user): RedirectResponse
    {
        $this->guard->login($user);

        return redirect()->intended('/dashboard');
    }

    private function redirectAfterAccountLinking(User $user): RedirectResponse
    {
        $this->session->regenerate();

        return $this->redirectToProfile($user)->with('success', 'Your provider account has been linked.');
    }

    private function sendLoginLinkandRedirect(User $user): RedirectResponse
    {
        $user->notify(new LoginLinkNotification);

        return to_route('login')->with('info', 'An account with this email already exists. We have sent a login link to your email address.');
    }

    private function redirectToProfile(User $user): RedirectResponse
    {
        if (! $user->currentTeam) {
            return to_route('onboarding');
        }

        return to_route('profile.edit', ['current_team' => $user->currentTeam->slug]);
    }
}
