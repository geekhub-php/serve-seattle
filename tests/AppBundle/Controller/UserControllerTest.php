<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListAction() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users');

        $this->assertEquals('302', $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);

        $crawler = $client->request('GET', '/users');

        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }

//    public function testAddAction()
//    {
//        $user = new User();
//        $user -> setEmail('test@gmail.com');
//        $user -> setLastName('Test');
//        $user -> setFirstName('Test');
//        $user -> setPlainPassword('Test');
//
//        $this->assertInstanceOf(User::class, $user);
//    }
//
//    public function testActivationAction()
//    {
//        $user = new User();
//        $user -> setEmail('test@gmail.com');
//        $user -> setLastName('Test');
//        $user -> setFirstName('Test');
//        $user -> setPlainPassword('Test');
//        $user -> setEnabled(true);
//
//        $this->assertEquals(true, $user->isEnabled());
//    }
//
//    public function testEditAction()
//    {
//        $user = new User();
//        $user -> setEmail('test@gmail.com');
//        $user -> setLastName('Test');
//        $user -> setFirstName('Test');
//        $user -> setPlainPassword('Test');
//
//        $user -> setEmail('editedtest@gmail.com');
//        $user -> setLastName('editedTest');
//        $user -> setFirstName('editedTest');
//        $user -> setPlainPassword('editedTest');
//
//        $this->assertInstanceOf(User::class, $user);
//    }
}