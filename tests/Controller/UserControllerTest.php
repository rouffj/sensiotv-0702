<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'SensioTV+');
    }

    public function testUserRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertSelectorTextContains('h1', 'Create your account');

        // On failure
        $client->submitForm('user_save', []);
        $this->assertEquals(6, $client->getCrawler()->filter('.form-error-message')->count(), 'You should have 6 errors on signup form');

        // On Success
        $client->submitForm('user_save', [
            'user[firstName]' => 'Joseph',
            'user[lastName]' => 'FOO',
            'user[email]' => 'joseph@gmail.com',
            'user[password][first]' => 'monSuperMotDe',
            'user[password][second]' => 'monSuperMotDe',
            'user[terms]' => true,
        ]);
        $this->assertEquals(0, $client->getCrawler()->filter('.form-error-message')->count());

        //file_put_contents(__DIR__.'/../../public/test.html', print_r($client->getResponse()->getContent(), true));die;
        $userRepo = $client->getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneByEmail('joseph@gmail.com');

        $this->assertNotNull($user);
    }
}
