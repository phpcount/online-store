<?php

namespace App\Security\Authenticator\Main;

use App\Event\UserLoggedSocialNetEvent;
use App\Utils\Factory\UserFactory;
use App\Utils\Generator\PasswordGenerator;
use App\Utils\Manager\UserManager;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    /**
     *
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     *
     * @var UserManager
     */
    private $userManager;

    /**
     *
     * @var RouterInterface
     */
    private $router;

    /**
     *
     * @var Session
     */
    private $session;

    /**
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(ClientRegistry $clientRegistry, UserManager $userManager, RouterInterface $router, EventDispatcherInterface $eventDispatcher)
    {
        $this->clientRegistry = $clientRegistry;
        $this->userManager = $userManager;
        $this->router = $router;
        $this->session = new Session();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        // if (!$this->supports($request)) {
        //     return null;
        // }

        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        $existingUser = $this->userManager->getRepository()->findOneBy([
            'googleId' => $googleUser->getId()
        ]);
        if ($existingUser) {
            return $existingUser;
        }

        $user = $this->userManager->getRepository()->findOneBy([
            'email' => $email
        ]);

        if(!$user) {
            $user = UserFactory::creteUserFromGoogleAccount($googleUser);

            $plainPassword = PasswordGenerator::generatePassword();
            $this->userManager->hashPassword($user, $plainPassword);

            $event = new UserLoggedSocialNetEvent($user, $plainPassword);
            $this->eventDispatcher->dispatch($event);

            $this->session->getFlashBag()->add('success', 'An email has been sent. Please, check your inbox to find password');
        }

        $user->setGoogleId($googleUser->getId());

        $this->userManager->save($user);

        return $user;
    }

    /**
     * @return GoogleClient
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry
            // "google_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('google_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('main_profile_index');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

}
