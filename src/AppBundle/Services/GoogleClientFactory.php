<?php

namespace AppBundle\Services;

class GoogleClientFactory
{
    private $path;

    private $scope = 'default';

    private $role = 'reader';

    public function createCalendar()
    {
        $calendar = new \Google_Service_Calendar($this->createClient());

        $calendar->acl->insert('primary', $this->scope());

        return $calendar;
    }

    private function createClient()
    {
        $client = new \Google_Client();
        $client->setScopes([\Google_Service_Calendar::CALENDAR]);
        $path = $this->getCredentials();
        $client->setAuthConfig($path);

        return $client;
    }

    private function scope()
    {
        $scope = new \Google_Service_Calendar_AclRuleScope();
        $scope->setType($this->scope);

        $rule = new \Google_Service_Calendar_AclRule();
        $rule->setRole($this->role);
        $rule->setScope($scope);

        return $rule;
    }

    private function getCredentials()
    {
        if (!file_exists(__DIR__.'/../../../app/config/credentials/credentials.json')) {
            throw new \Google_Exception('Credentials is not valid');
        }
        $this->path = __DIR__.'/../../../app/config/credentials/credentials.json';

        return $this->path;
    }

    public function setScope($type, $role)
    {
        $this->scope = $type;

        $this->role = $role;
    }
}
