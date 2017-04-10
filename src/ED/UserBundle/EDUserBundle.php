<?php

namespace ED\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EDUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
