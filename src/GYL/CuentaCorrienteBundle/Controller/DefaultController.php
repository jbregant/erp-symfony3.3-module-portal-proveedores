<?php

namespace GYL\CuentaCorrienteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->redirectToRoute('fos_user_security_login');
    }
}
