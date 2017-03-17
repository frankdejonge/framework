<?php
namespace Illuminate\Contracts\Routing;

interface UrlInformation
{
    public function getByName($name);

    public function getByAction($action);
}