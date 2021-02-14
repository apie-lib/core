<?php


namespace Apie\Core\PluginInterfaces;

use Apie\Core\Apie;

interface ApieAwareInterface
{
    /**
     * @param Apie $apie
     * @return mixed
     */
    public function setApie(Apie $apie);
}
