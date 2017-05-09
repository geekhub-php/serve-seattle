<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine');

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => 'admin@serve-seattle.com', '_password' => 'admin']);

        $crawler = $client->request('GET', '/admin/');

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'admin[userName]' => 'test1',
            'admin[email]' => 'test1@gmail.com',
        ]);
        $admin = $em->getRepository(Admin::class)->findOneBy(['userName' => 'test1']);

        $this->assertInstanceOf(Admin::class, $admin);
        exec('./bin/console d:d:d --force --env=test');
    }
}
