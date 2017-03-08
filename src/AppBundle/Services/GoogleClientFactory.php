<?php

namespace AppBundle\Services;

class GoogleClientFactory
{
    private $path;

    public function createCalendar($scopeType, $role)
    {
        $calendar = new \Google_Service_Calendar($this->createClient());

        $calendar->acl->insert('primary', $this->aclRule($scopeType, $role));

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

    private function aclRule($scopeType, $role)
    {
        $scope = new \Google_Service_Calendar_AclRuleScope();
        $scope->setType($scopeType);

        $rule = new \Google_Service_Calendar_AclRule();
        $rule->setRole($role);
        $rule->setScope($scope);

        return $rule;
    }

    private function getCredentials()
    {
        if (!file_exists(__DIR__.'/../../../app/config/credentials/credentials.json')) {
            throw new \Google_Exception('Credentials is not found');
        }
        $this->path = __DIR__.'/../../../app/config/credentials/credentials.json';

        return $this->path;
    }
}
