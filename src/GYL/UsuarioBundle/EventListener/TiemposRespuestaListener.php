<?php

namespace GYL\UsuarioBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use GYL\CuentaCorrienteBundle\Controller\CuentaCorrienteController;
use GYL\ProveedorBundle\Controller\PreRegistroProveedorController;
use GYL\UsuarioBundle\Entity\TiempoRespuesta;
use GYL\UsuarioBundle\Entity\Constantes\ConstanteTipoAccion;
use DateTime;

class TiemposRespuestaListener {

    protected $em;
    
    protected $twig;
    
    public function __construct(EntityManager $em, \Twig_Environment $twig){
        $this->em = $em;
        $this->twig = $twig;
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
        // You get the exception object from the received event
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof CuentaCorrienteController && $controller[1] == "indexAction") {
            
            $tiempoRespuesta = new TiempoRespuesta();
            $tiempoRespuesta->setFecha(new \DateTime);
            $date = DateTime::createFromFormat('U.u', microtime(TRUE));
            $tiempoRespuesta->setFechaTiempo($date->format('Y-m-d H:i:s.u'));
            
            $tipoAccion = $this->em->find('GYLUsuarioBundle:TipoAccion', ConstanteTipoAccion::CONSULTA_CUENTA_CORRIENTE);
            $tiempoRespuesta->setAccion($tipoAccion);
            $this->em->persist($tiempoRespuesta);
            $this->em->flush();
            
            $this->twig->addGlobal('idTiempoRespuesta', $tiempoRespuesta->getId());
            
        }
        
        if ($controller[0] instanceof PreRegistroProveedorController && $controller[1] == "formularioAction") {
            
            $tiempoRespuesta = new TiempoRespuesta();
             $tiempoRespuesta->setFecha(new \DateTime);
            $date = DateTime::createFromFormat('U.u', microtime(TRUE));
            $tiempoRespuesta->setFechaTiempo($date->format('Y-m-d H:i:s.u'));
            
            $tipoAccion = $this->em->find('GYLUsuarioBundle:TipoAccion', ConstanteTipoAccion::FORMULARIO_INSCRIPCION);
            $tiempoRespuesta->setAccion($tipoAccion);
            $this->em->persist($tiempoRespuesta);
            $this->em->flush();
            
            $this->twig->addGlobal('idTiempoRespuesta', $tiempoRespuesta->getId());
        }

    }
    
}