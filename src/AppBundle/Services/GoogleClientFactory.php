<?php

namespace AppBundle\Services;

class GoogleClientFactory
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

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
        $client->setAuthConfig($this->path);

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
}
