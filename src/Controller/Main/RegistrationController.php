<?php

namespace App\Controller\Main;

use App\Entity\User;
use App\Form\Main\RegistrationFormType;
use App\Messenger\Message\Event\UserRegisteredEvent;
use App\Repository\UserRepository;
use App\Security\Verifier\EmailVerifier;
use App\Utils\Manager\UserManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route({ "en": "/registration", "ru": "/регитсрация" }, name="main_registration")
     */
    public function registration(Request $request, UserManager $userManager, MessageBusInterface $messageBus): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main_profile_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $userManager->hashPassword($user, $form->get('plainPassword')->getData());
            $userManager->save($user);

            // generate a signed url and email it to the user
            $event = new UserRegisteredEvent($user->getId());
            $messageBus->dispatch($event);

            // do anything else you need here, like send an email
            $this->addFlash('success', 'An email has been sent. Please check your inbox to complete registration.');

            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('main/security/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="main_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('main_registration');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('main_registration');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request->getUri(), $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('warning', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('main_homepage');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('main_homepage');
    }
}
