<?php

namespace Moukail\PasswordResetMailBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Moukail\PasswordResetMailBundle\DependencyInjection\MoukailPasswordResetMailExtention;

class MoukailPasswordResetMailBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new MoukailPasswordResetMailExtention();
        }

        return $this->extension;
    }
}
