<?php

namespace Illuminate\Routing\Dumped;

use Illuminate\Contracts\Routing\UrlInformation as UrlInformationContract;

class UrlInformation implements UrlInformationContract
{
    protected $byName = [];
    protected $byAction = [];

    public function getByName($name)
    {
        if ( ! isset($this->byName[$name])) {
            return;
        }

        $information = $this->byName[$name];

        return new DumpedRoute(
            $information['methods'],
            $information['uri'],
            $information['parameters'],
            $information['defaults'],
            $information['wheres'],
            $information['domain']
        );
    }

    public function getByAction($action)
    {
        if (isset($this->byAction[$action])) {
            return $this->getByName($this->byAction[$action]);
        }
    }
}