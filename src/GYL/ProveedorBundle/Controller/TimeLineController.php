<?php

namespace GYL\ProveedorBundle\Controller;


use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorTimeline;
use GYL\ProveedorBundle\Entity\TipoProveedor;
use GYL\ProveedorBundle\Entity\TipoTimeline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;


/**
 * Timeline controller.
 *
 * @Route("/")
 */
class TimeLineController extends BaseController
{
    /**
     * Timeline.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function timelineAction(Request $request, $idProvDatoPersonal)
    {

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $datosPersonalesRepo = $em->getRepository(ProveedorDatoPersonal::class);
        $repoTipoProveedor = $em->getRepository(TipoProveedor::class);
        $repoProveedorTimeline = $em->getRepository(ProveedorTimeline::class);
        $repoTipoTimeline = $em->getRepository(TipoTimeline::class);

        $proveedorTimeline = $repoProveedorTimeline->findBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $datosPersonales = $datosPersonalesRepo->findOneBy(['id' => $idProvDatoPersonal]);
        $tipoProveedor = $repoTipoProveedor->findOneBy(['id' => $datosPersonales->getTipoProveedor()]);
        $tiposTimeline = $repoTipoTimeline->findAll(); //Reflejo 21/09 esta variable parece no usarse.

        $mappedArray = array();
        foreach ($proveedorTimeline as $ptm) {
            if ($ptm->getStatus() == 'completo') {
                $mappedArray[$ptm->getDenominacion()->getDenominacion()] = true;
            }
        }


        return $this->render('ProveedorBundle::Preinscripcion/timeline.html.twig', array(
            'proveedorDatosPersonales' => $datosPersonales,
            'tipoProveedor' => $tipoProveedor,
            'mappedTimelineInfo' => $mappedArray
        ));
    }


}