<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertEquals('302', $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);


        $crawler = $client->request('GET', '/admin/');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'admin[userName]' => 'test1',
            'admin[email]' => 'test1@gmail.com',
        ]);

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $admin = $em->getRepository(Admin::class)->findOneBy(['email' => 'test1@gmail.com']);

        $this->assertInstanceOf(Admin::class, $admin);
    }
}
