<?php
namespace Apie\Core\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SessionFactoryInterface
{
    public function create(): SessionInterface;
}
