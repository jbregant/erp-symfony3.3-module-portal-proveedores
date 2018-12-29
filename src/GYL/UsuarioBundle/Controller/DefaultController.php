<?php

namespace GYL\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Invitacion controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * Crear invitacion.
     *
     * @Route("/", name="home")
     *
     */
    public function indexAction()
    {
        return $this->redirectToRoute('fos_user_security_login');
        //return $this->render('ADIFSE/Trenes_Argentinos_Infraestructura_Argentina.gob.ar.html.twig');
    }
}
