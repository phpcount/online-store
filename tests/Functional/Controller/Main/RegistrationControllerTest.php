<?php

namespace App\Tests\Functional\Controller\Main;

use App\Repository\UserRepository;
use App\Tests\TestUtils\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

/**
 * @group functional
 */
class RegistrationControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();

        $newUserEmail = 'new_test_user_1@test.com';
        $newUserPassword = 'test123';

        $client->request('GET', '/en/registration');

        $client->submitForm('SIGN UP', [
            'registration_form[email]' => $newUserEmail,
            'registration_form[plainPassword]' => $newUserPassword,
            'registration_form[agreeTerms]' => true,
        ]);

        $expectedLocation = '/en/';
        $this->assertResponseRedirects(
            $expectedLocation,
            Response::HTTP_FOUND,
            sprintf('The %s URL redirects to the home page', $expectedLocation)
        );

        $client->followRedirect();

        $this->assertSelectorTextContains('div', 'An email has been sent. Please check your inbox to complete registration.');


        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["email" => $newUserEmail]);

        $this->assertNotNull($user);

        $this->assertSame($newUserEmail, $user->getEmail());

        $this->assertFalse($user->isVerified());

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->get());

    }

    public function testRegistrationEmailDublicate(): void
    {
        $client = static::createClient();

        $newUserEmail = UserFixtures::USER_1_EMAIL;
        $newUserPassword = 'pass';

        $client->request('GET', '/en/registration');

        $client->submitForm('SIGN UP', [
            'registration_form[email]' => $newUserEmail,
            'registration_form[plainPassword]' => $newUserPassword,
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('div', 'There is already an account with this email');

        $this->assertSelectorTextContains('div', 'Your password should be at least 6 characters');

    }

    public function testRegistrationPasswordTooShort(): void
    {
        $client = static::createClient();

        $newUserEmail = 'test_user_2@test.com';
        $newUserPassword = 'pass';

        $client->request('GET', '/en/registration');

        $client->submitForm('SIGN UP', [
            'registration_form[email]' => $newUserEmail,
            'registration_form[plainPassword]' => $newUserPassword,
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('div', 'Your password should be at least 6 characters');

    }


}
