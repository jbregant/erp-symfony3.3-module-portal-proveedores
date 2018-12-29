<?php
namespace GYL\UsuarioBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GYL\UsuarioBundle\Entity\TiempoRespuesta;
use DateTime;

/**
 * TiempoRespuesta controller.
 *
 * @Route("/tiemporespuesta")
  */
class TiempoRespuestaController extends Controller{
    
    /**
    *
    * @Route("/guardartiempo", name="tiemporespuesta_guardartiempo")
    * 
    * @param Request $request
    */
    public function guardarTiempoAction(Request $request){
        $idTiempoRespuesta = $request->request->get('idTiempoRespuesta');
        
        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $tiempoRespuesta = $em->getRepository(TiempoRespuesta::class)->find($idTiempoRespuesta);
        
        $fechaHoraInicio = DateTime::createFromFormat('Y-m-d H:i:s.u', $tiempoRespuesta->getFechaTiempo() ); 
        $fechaHoraFin = DateTime::createFromFormat('U.u', microtime(TRUE));
        $tiempo = $this->mdiff($fechaHoraFin, $fechaHoraInicio);
        
        $tiempoRespuesta->setTiempo($tiempo);
        $em->merge($tiempoRespuesta);
        $em->flush();
        
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }
    
    public function mdiff($date1, $date2){
        //Absolute val of Date 1 in seconds from  (EPOCH Time) - Date 2 in seconds from (EPOCH Time)
        $diff = abs(strtotime($date1->format('d-m-Y H:i:s.u'))-strtotime($date2->format('d-m-Y H:i:s.u')));

        //Creates variables for the microseconds of date1 and date2
        $micro1 = $date1->format("u");
        $micro2 = $date2->format("u");

        //Difference between these micro seconds:
        $diffmicro = $micro1 - $micro2;

        list($sec,$micro) = explode('.',((($diff) * 1000000) + $diffmicro )/1000000);

        //Creates the variable that will hold the seconds (?):
        $difference = ($sec*1000) + ((int)str_pad($micro,6,'0')/1000);

        return $difference;
    }
}
