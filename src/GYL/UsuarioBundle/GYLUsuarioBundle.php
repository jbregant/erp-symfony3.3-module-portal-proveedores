<?php

namespace GYL\UsuarioBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GYLUsuarioBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
