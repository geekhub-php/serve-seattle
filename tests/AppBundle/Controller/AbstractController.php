<?php

namespace Tests\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractController extends WebTestCase
{
    protected static $options = [
        'environment' => 'test',
        'debug' => true,
    ];
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var Client
     */
    protected $client;
    /**
     * {@inheritDoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        self::bootKernel(self::$options);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        parent::__construct($name, $data, $dataName);
    }


    /**
     * @param string $path
     * @param string $method
     * @param int $expectedStatusCode
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function request($path, $method = 'GET', $expectedStatusCode = 200)
    {
        $client = $this->getClient();
        $crawler = $client->request($method, $path);
        self::assertEquals(
            $expectedStatusCode,
            $client->getResponse()->getStatusCode(),
            sprintf(
                'We expected that uri "%s" will return %s status code, but had received %d',
                $path,
                $expectedStatusCode,
                $client->getResponse()->getStatusCode()
            )
        );
        return $crawler;
    }

    /**
     * @param array $options
     * @param array $server
     * @return Client
     */
    protected function getClient(array $options = array(), array $server = array())
    {
        if (!$this->client) {
            $this->client = static::createClient($options, $server);
        }
        return $this->client;
    }
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = static::$kernel->getContainer();
        }
        return $this->container;
    }

    protected function logIn($username = 'admin@serve-seattle.com', $password = 'admin')
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $client->submit($form, ['_username' => $username, '_password' => $password]);
        return $client;
    }
}
