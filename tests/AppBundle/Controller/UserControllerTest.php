<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    public function testListAction()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertEquals('302', $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);
        $client->request('GET', '/users');

        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }

    public function testAddAction()
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertEquals('302', $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $usersCount1 = count($em->getRepository('AppBundle:User')->findAll());

        $crawler = $client->request('GET', '/user/add');
        $form = $crawler->selectButton('Register')->form();
        $client->submit($form, [
            'edit[lastName]' => 'test',
            'edit[firstName]' => 'test',
            'edit[email]' => 'test@gmail.com',
            'edit[plainPassword][first]' => 'test',
            'edit[plainPassword][second]' => 'test',
        ]);
        $usersCount2 = count($em->getRepository('AppBundle:User')->findAll());

        $this->assertEquals($usersCount1 + 1, $usersCount2);
    }

    public function testActivationAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $crawler = $client->request('GET', '/users');

        $form = $crawler->filter('table td form')->form();
        $url_params = explode('/', $form->getUri());
        $userid = $url_params[count($url_params) - 1];

        $client->submit($form, ['activation[enabled]' => false]);
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userid));

        $this->assertEquals(false, $user->isEnabled());
    }

    public function testEditAction()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertEquals('302', $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $crawler = $client->request('GET', '/users');

        $form = $crawler->filter('table td form')->form();
        $url_params = explode('/', $form->getUri());
        $userid = $url_params[count($url_params) - 1];
        $crawler = $client->request('GET', '/user/edit/'.$userid);
        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'edit[lastName]' => 'test1',
            'edit[firstName]' => 'test1',
            'edit[email]' => 'test123@gmail.com',
            'edit[plainPassword][first]' => 'test1',
            'edit[plainPassword][second]' => 'test1',
        ]);

        $this->assertInstanceOf(
            User::class,
            $em->getRepository(User::class)->findOneBy(['email' => 'test123@gmail.com'])
        );
    }

    public function testUpdatePassword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/password_update/2');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Submit')->form();
        $form['reset_password[plainPassword][first]'] = 'new';
        $form['reset_password[plainPassword][second]'] = 'new';
        $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        exec('./bin/console d:d:d --force --env=test');
    }
}
