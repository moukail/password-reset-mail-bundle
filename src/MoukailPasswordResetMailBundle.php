<?php

namespace Moukail\PasswordResetMailBundle;

use Moukail\PasswordResetMailBundle\DependencyInjection\MoukailPasswordResetMailExtention;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MoukailPasswordResetMailBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MoukailPasswordResetMailExtention();
        }

        return $this->extension;
    }
}
