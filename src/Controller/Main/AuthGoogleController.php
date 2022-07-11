<?php

namespace App\Controller\Main;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/connect/google", name="connect_google_")
 */
class AuthGoogleController extends AbstractController
{
    /**
     * @Route("/", name="start")
     */
    public function connectAction(ClientRegistry $clientRegistry): Response
    {

        return $clientRegistry
                    ->getClient('google_main')
                    ->redirect([], []);
    }

    /**
     * @Route("/check", name="check")
     */
    public function connectCheckAction(ClientRegistry $clientRegistry)
    {

        //
    }
}
