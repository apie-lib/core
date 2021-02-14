<?php


namespace Apie\Core\PluginInterfaces;

use Apie\Core\Apie;
use Apie\Core\Exceptions\BadConfigurationException;

trait ApieAwareTrait
{
    private $apie;

    public function setApie(Apie $apie)
    {
        if ($this->apie) {
            throw new BadConfigurationException('setApie should only be called once');
        }
        $this->apie = $apie;
        return $this;
    }

    protected function getApie(): Apie
    {
        if (!$this->apie) {
            throw new BadConfigurationException('setApie is not being called');
        }
        return $this->apie;
    }
}
