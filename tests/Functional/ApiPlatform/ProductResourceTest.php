<?php

namespace App\Tests\Functional\ApiPlatform;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Tests\TestUtils\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class ProductResourceTest extends ResourceTestUtils
{
    /**
     *
     * @var string
     */
    protected $uriKey= '/api/v1/products';


    public function testGetProducts(): void
    {
        $client = static::createClient();

        $client->request('GET', $this->uriKey, [], [], self::REQUEST_HEADER);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testGetProduct(): void
    {
        $client = static::createClient();

        /** @var ProductRepository $productRepository */
        $productRepository = self::getContainer()->get(ProductRepository::class);

        $prodcuts = $productRepository->findAll();

        $this->assertIsArray($prodcuts);

        $productCount = count($prodcuts);
        $this->assertTrue($productCount > 0);

        $currentProduct = $prodcuts[rand(0, $productCount - 1)];

        $expectedUuid = $currentProduct->getUuid();
        // dump("Expected Uuid: {$expectedUuid}", "");

        $uri = "{$this->uriKey}/{$expectedUuid}";

        $client->request('GET', $uri, [], [], self::REQUEST_HEADER);
        // $this->printResponsedContent($client, "Response:");

        $this->assertResponseIsSuccessful();

    }

    public function testCreateProduct(): void
    {
        $client = static::createClient();

        $this->checkDefaultUserhasNotAccess($client, $this->uriKey, 'POST');

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(["email" => UserFixtures::USER_ADMIN_1_EMAIL]);
        // dump($user->getEmail());

        $client->loginUser($user, 'main');

        $titleForFind = 'New Product 1313';
        $context = [
            'title' => $titleForFind,
            'price' => '100,50',
            'quantity' => 5
        ];
        $client->request('POST', $this->uriKey, [], [], self::REQUEST_HEADER, json_encode($context));
        // $this->printResponsedContent($client, "Response:");

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        /** @var ProductRepository $productRepository */
        $productRepository = self::getContainer()->get(ProductRepository::class);

        $prodcut = $productRepository->findOneBy(["title" => $titleForFind]);

        $this->assertEquals($titleForFind, $prodcut->getTitle());
    }

    public function testPatchProduct(): void
    {
        $client = static::createClient();

        $this->checkDefaultUserhasNotAccess($client, $this->uriKey, 'POST');

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(["email" => UserFixtures::USER_ADMIN_1_EMAIL]);
        // dump($user->getEmail());

        $client->loginUser($user, 'main');


        /** @var ProductRepository $productRepository */
        $productRepository = self::getContainer()->get(ProductRepository::class);

        $product = $productRepository->findOneBy([]);

        $uri = "{$this->uriKey}/{$product->getUuid()}";
        $context = [
            'title' => 'New name: ' . date('d/m/Y H:i:s')
        ];
        $client->request('PATCH', $uri, [], [], self::REQUEST_HEADER_PATCH, json_encode($context));
        $this->printResponsedContent($client, "Response:");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $product = $productRepository->find($product->getId());
        $this->assertEquals($product->getTitle(), $context["title"]);
    }

}
