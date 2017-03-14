<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Form\User\EditType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    public function testListAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users');
        $this->assertEquals('302', $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'ghjynj']);
        $crawler = $client->request('GET', '/users');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }

    public function testAddAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'ghjynj']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $usersCount1 = count($em->getRepository('AppBundle:User')->findAll());

        $crawler = $client->request('GET', '/user/add');
        $form = $crawler->selectButton('Register')->form();
        //dump($form);
        $client->submit($form, [
            'registration[lastName]' => 'test',
            'registration[firstName]' => 'test',
            'registration[email]' => 'test@gmail.com',
            'registration[plainPassword][first]' => 'test',
            'registration[plainPassword][second]' => 'test',
        ]);

        $usersCount2 = count($em->getRepository('AppBundle:User')->findAll());
        $this->assertEquals($usersCount1 + 1, $usersCount2);
    }

    public function testActivationAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'ghjynj']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $crawler = $client->request('GET', '/users');

        $form = $crawler->filter('table td form')->form();
        $url_params = explode('/', $form->getUri());
        $userid = $url_params[count($url_params)-1];

        $client->submit($form, ['activation[enabled]' => false]);
        $user = $em->getRepository('AppBundle:User')->findOneBy(array("id" => $userid));

        $this->assertEquals(false, $user->isEnabled());
    }

    public function testEditAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'ghjynj']);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $crawler = $client->request('GET', '/users');

        $form = $crawler->filter('table td form')->form();
        $url_params = explode('/', $form->getUri());
        $userid = $url_params[count($url_params)-1];

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
}
