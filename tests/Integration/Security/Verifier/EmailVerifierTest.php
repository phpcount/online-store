<?php

namespace App\Tests\Integration\Security\Verifier;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Security\Verifier\EmailVerifier;
use App\Tests\TestUtils\Fixtures\UserFixtures;
use App\Utils\Manager\UserManager;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group integration
 */
class EmailVerifierTest extends KernelTestCase
{

    /**
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     *
     * @var EmailVerifier
     */
    private $emailVerifier;


    public function setUp(): void
    {
        parent::setUp();

        // launch kernel
        self::bootKernel();

        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->emailVerifier = self::getContainer()->get(EmailVerifier::class);

        // test
        // $users = $this->userRepository->findAll();
        // dd($users);

        // /** @var CategoryRepository $categoryRepository */
        // $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        // $categories = $categoryRepository->findAll();

        // /** @var ProductRepository $productRepository */
        // $productRepository = self::getContainer()->get(ProductRepository::class);
        // $products = $productRepository->findAll();
        // dd($categories, $products);

    }

    public function testGenerateEmailSignatureAndHandleEmailConfirmation()
    {
        $user = $this->userRepository->findOneBy(['email' => UserFixtures::USER_1_EMAIL]);
        self::assertNotNull($user, 'Not found user');

        $user->setIsVerified(false);

        $emailSignature = $this->checkGenerateEmailSignature($user);

        $this->checkHandleEmailConfirmation($user, $emailSignature);

    }

    private function checkGenerateEmailSignature(User $user)
    {
        $currentDateTime = new DateTimeImmutable();
        $emailSignature = $this->emailVerifier->generateEmailSignature('main_verify_email', $user);

        self::assertGreaterThan($currentDateTime, $emailSignature->getExpiresAt(), 'DateTime is not correctly');

        return $emailSignature;
    }

    private function checkHandleEmailConfirmation(User $user, $emailSignature)
    {
        self::assertFalse($user->isVerified());

        $this->emailVerifier->handleEmailConfirmation($emailSignature->getSignedUrl(), $user);

        self::assertTrue($user->isVerified(), 'User is not verified');
    }


}
