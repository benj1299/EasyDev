<?php

namespace User\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
