<?php

namespace GYL\ProveedorBundle\Controller;

use Doctrine\DBAL\Connection;
use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorActividad;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\TipoActividad;
use GYL\ProveedorBundle\Entity\TipoActividadSeccion;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Proxies\__CG__\GYL\ProveedorBundle\Entity\TipoActividadGrupo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Actividad controller.
 *
 * @Route("/")
 */
class ProveedorActividadController extends BaseController {

    /**
     * Agregar Actividad.
     *
     * @Route("/formulariopreinscripcion/agregaractividad", name="agregar_actividad")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarActividadAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoActividad = $em->getRepository(ProveedorActividad::class);

            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $proveedorDatoPersonal = $repoProveedorDatoPersonal->findOneBy(['id' => $data['idDatoPersonal']]);

            $existente = $repoActividad->findOneBy(['tipoActividad' => $data['id'], 'idDatoPersonal' =>  $data['idDatoPersonal']]);
            $repoTipoActividad = $em->getRepository(TipoActividad::class);
            $tipoActividad = $repoTipoActividad->findOneBy(['id' => $data['id']]);

            if ($existente != null) {
                $response = 304;
                $actId = 'Sin cambios';
            } else {
                $act = New ProveedorActividad();
                $act->setIdUsuario($this->getUser());
                $act->setTipoActividad($tipoActividad);
                $act->setIdDatoPersonal($proveedorDatoPersonal);
                $em->persist($act);
                $em->flush();
                $response = 200;
                $actId = $act->getId();
                $this->guardarTimeline('timeline_actividades', 'completo', 1, $data['idDatoPersonal']);
            }
            $msg = 'ok';
        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $actId = null;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $actId = null;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
            'data' => $actId
        ]);
    }

    /**
     * Agregar Actividad Extranjero.
     *
     * @Route("/formulariopreinscripcion/agregaractividadextranjero", name="agregar_actividad_extranjero")
     * @Method("GET|POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarActividadExtranjeroAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $proveedorDatoPersonal = $repoProveedorDatoPersonal->findOneBy(['id' => $data["form"]["id_dato_personal"]]);

            if ($data['form']['exportacion_bienes_extranjero'] === 'true'){
                $repoProveedorActividad = $em->getRepository(ProveedorActividad::class);
                $repoTipoActividad = $em->getRepository(TipoActividad::class);

                $existenteExportacion = $repoProveedorActividad->findOneBy(['tipoActividad' => 897, 'idDatoPersonal' =>  $data['form']['id_dato_personal']]);
                $tipoActividadExportacion = $repoTipoActividad->findOneBy(['id' => 897]);

                if ($existenteExportacion instanceof ProveedorActividad){
                    $this->guardarActividad($existenteExportacion, $data, $proveedorDatoPersonal, $tipoActividadExportacion, $em);
                } else {
//                    $result1Flag = true;
                    $this->guardarActividadNew($data, $em, $tipoActividadExportacion, $proveedorDatoPersonal);
                }
            } else {
                //me fijo si existe para borrarlo
                $repoProveedorActividad = $em->getRepository(ProveedorActividad::class);

                $existenteExportacion = $repoProveedorActividad->findOneBy(['tipoActividad' => 897, 'idDatoPersonal' =>  $data['form']['id_dato_personal']]);

                if ($existenteExportacion instanceof ProveedorActividad){
                    $em->remove($existenteExportacion);
                    $em->flush();
                }
            }

            if ($data['form']['prestacion_servicios_extranjero'] === 'true'){
                $repoActividadPres = $em->getRepository(ProveedorActividad::class);
                $repoTipoActividad = $em->getRepository(TipoActividad::class);
                $existentePrestaciones = $repoActividadPres->findOneBy(['tipoActividad' => 896, 'idDatoPersonal' =>  $data['form']['id_dato_personal']]);

                $tipoActividadPrestaciones = $repoTipoActividad->findOneBy(['id' => 896]);

                if ($existentePrestaciones instanceof ProveedorActividad){
                    $this->guardarActividad($existentePrestaciones, $data, $proveedorDatoPersonal, $tipoActividadPrestaciones, $em);
                } else {
//                    $result1Flag1 = true;
                    $this->guardarActividadNew($data, $em, $tipoActividadPrestaciones, $proveedorDatoPersonal);
                }
            } else {
                //me fijo si existe para borrarlo
                $repoProveedorActividad = $em->getRepository(ProveedorActividad::class);

                $existentePrestaciones = $repoProveedorActividad->findOneBy(['tipoActividad' => 896, 'idDatoPersonal' =>  $data['form']['id_dato_personal']]);

                if ($existentePrestaciones instanceof ProveedorActividad){
                    $em->remove($existentePrestaciones);
                    $em->flush();
                }
            }
            $this->guardarTimeline('timeline_actividades', 'completo', 1, $data['form']['id_dato_personal']);
            $response = 200;

            $msg = 'ok';
        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
        ]);
    }

    /**
     * Get Actividades.
     *
     * @Route("/formulariopreinscripcion/getactividades/{codigo}", name="get_actividades")
     * @Method("GET")
     * @return JsonResponse|Response
     */
    public function getActividadesAction($codigo, Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoTipooActividad = $em->getRepository(TipoActividad::class);

            $results = $repoTipooActividad->findByDenomPart($codigo);


            $data = $results;
            $msg = 'ok';
            $response = 200;
        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $data = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
            'data' => $data
        ]);
    }

    /**
     * Quitar Actividad.
     *
     * @Route("/formulariopreinscripcion/quitaractividad", name="quitar_actividad")
     * @Method("POST")
     * @return JsonResponse|Response
     */
    public function quitarActividadAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoActividad = $em->getRepository(ProveedorActividad::class);
            $proveedorActividad = $repoActividad->findOneBy(['id' => $data['id'], 'idDatoPersonal' =>  $data['idDatoPersonal']]);
            $em->remove($proveedorActividad);
            $em->flush();

            if ($data['actividades'] == 'true') {
                $this->guardarTimeline('timeline_actividades', 'completo', 1,$data['idDatoPersonal']);
            } else {
                $this->guardarTimeline('timeline_actividades', 'incompleto', 2, $data['idDatoPersonal']);
            }
            $msg = 'ok';
            $response = 200;
        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
        ]);
    }

    /**
     * Datos Actividad.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function proveedorActividadAction(Request $request,$estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm){
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
//        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;//wtf

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorActividad::class);

        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'actividad']);

        $observacionEvaluacion = null;

        if ($proveedorEvaluacion) {
            $observacionEvaluacion = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacion->getId(),
                'activo' => 1
            ]);

            if (empty($observacionEvaluacion))
                $observacionEvaluacion = null;
        }

        $results = $repo->findByIdDenoms($idProvDatoPersonal);

        return $this->render('ProveedorBundle::Preinscripcion/panel_actividad.html.twig', array(
            'actividades' => $results,
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));
    }

    /**
     * Datos Actividad Extranjeros.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function proveedorActividadExtranjeroAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm){
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral : 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorActividad::class);
        $proveedorActividad = $repo->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);

        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'actividad']);

        if ($proveedorEvaluacion) {
            $data = $em->getRepository(ObservacionEvaluacion::class)->findOneBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacion->getId(),
                'activo' => 1
            ]);

            // Seteo observacion para actividades.
            $observacionEvaluacion = ($data)? $data->getObservaciones() : null;

        } else {
            $observacionEvaluacion = null;
        }

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDocConvenioUnilateral = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'convenio_unilateral'));
        $tipoDocConvenioTributacionInternacional = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'tributacion_internacional'));
        $tipoDocEstablecimientoArgentina = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'establecimiento_argentina'));

        $documentacionConvenioUnilateral = $repoDocumentacion->findBy(array('idUsuario' => $user->getId(), 'tipoDocumentacion' => $tipoDocConvenioUnilateral->getId()));
        $documentacionConvenioTributacionInternacional = $repoDocumentacion->findBy(array('idUsuario' => $user->getId(), 'tipoDocumentacion' => $tipoDocConvenioTributacionInternacional->getId()));
        $documentacionEstablecimientoArgentina = $repoDocumentacion->findBy(array('idUsuario' => $user->getId(), 'tipoDocumentacion' => $tipoDocEstablecimientoArgentina->getId()));


        $form = $this->createFormBuilder()
            ->add('exportacion_bienes_extranjero', HiddenType::class, array('label' => ''))
            ->add('prestacion_servicios_extranjero', HiddenType::class, array('label' => ''))
            ->getForm();

        $formPrestacionServicios = $this->createFormBuilder()
            ->add('prestacion_servicio_numero', IntegerType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getPrestacionServicioNumero() : null, 'attr' => array('min' => 0)))
            ->add('prestacion_servicio_regimen', IntegerType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getPrestacionServicioRegimen() : null, 'attr' => array('min' => 0)))
            ->add('prestacion_servicio_porcentaje_excension', IntegerType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getPrestacionServicioPorcentajeExcension() : null, 'attr' => array('min' => 0, 'max' => 100)))
            ->add('prestacion_servicio_fecha_desde', DateType::class, array('label' => ' ', 'data' => $proveedorActividad ? $proveedorActividad->getPrestacionServicioFechaDesde() : new \DateTime,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']))
            ->add('prestacion_servicio_fecha_hasta', DateType::class, array('label' => ' ', 'data' => $proveedorActividad ? $proveedorActividad->getPrestacionServicioFechaHasta() : new \DateTime,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']))
            ->add('convenio_unilateral', HiddenType::class, array('label' => ''))
            ->getForm();


        $convenioUnilateral = $this->createFormBuilder()
            ->add('convenio_unilateral_aplicacion_caba', TextType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getConvenioUnilateralAplicacionCaba() : null, 'attr' => array('min' => 0, 'max' => 100)))
            ->add('convenio_unilateral_aplicacion_grupo', TextType::class, array('label' => '', 'required' => false, 'data' => $proveedorActividad ? $proveedorActividad->getConvenioUnilateralGrupo() : null))
            ->getForm();

        $formTipoDePrestacion = $this->createFormBuilder()
            ->add('tipo_prestacion_asistencia_tecnica', CheckboxType::class, array('label' => ' ', 'data' => $proveedorActividad ? $proveedorActividad->isTipoPrestacionAsistenciaTecnica() == 1 ? true : false : false))
            ->add('tipo_prestacion_otros', TextType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getTipoPrestacionOtros() : null))
            ->add('convenio_tributacion_internacional', HiddenType::class, array('label' => ''))
            ->add('establecimiento_argentina', HiddenType::class, array('label' => ''))
            ->getForm();

        $formConvenioInternacional = $this->createFormBuilder()
            ->add('articulo_de_convenio_aplicable', TextType::class, array('label' => '', 'data' => $proveedorActividad ? $proveedorActividad->getArticuloDeConvenioAplicable() : null,
                'required' => false))
            ->getForm();

        return $this->render('ProveedorBundle::Preinscripcion/panel_actividad_extranjero.html.twig', array(
            'actividad_extranjero' => $proveedorActividad,
            'formActividadExtranjero' => $form->createView(),
            'formPrestacionServicios' => $formPrestacionServicios->createView(),
            'formTipoDePrestacion' => $formTipoDePrestacion->createView(),
            'formConvenioUnilateral' => $convenioUnilateral->createView(),
            'formConvenioInternacional' => $formConvenioInternacional->createView(),
            'documentacionConvenioUnilateral' => $documentacionConvenioUnilateral,
            'documentacionConvenioTributacionInternacional' => $documentacionConvenioTributacionInternacional,
            'documentacionEstablecimientoArgentina' => $documentacionEstablecimientoArgentina,
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));
    }

    /**
     * @param $existenteExportacion
     * @param $data
     * @param $proveedorDatoPersonal
     * @param $tipoActividadExportacion
     * @param $em
     * @return mixed
     */
    public function guardarActividad($emObj, $data, $proveedorDatoPersonal, $tipoActividadExportacion, $em)
    {
        $emObj->setExportacionBienes($data['form']['exportacion_bienes_extranjero'] == 'true' ? 1 : 0);
        $emObj->setPrestacionServicios($data['form']['prestacion_servicios_extranjero'] == 'true' ? 1 : 0);
        $emObj->setPrestacionServicioRegimen($data['form']['prestacion_servicio_regimen'] ? $data['form']['prestacion_servicio_regimen'] : null);
        $emObj->setPrestacionServicioNumero($data['form']['prestacion_servicio_numero'] ? $data['form']['prestacion_servicio_numero'] : null);
        $emObj->setConvenioUnilateralAplicacionCaba($data['form']['convenio_unilateral_aplicacion_caba'] ? $data['form']['convenio_unilateral_aplicacion_caba'] : null);
        $emObj->setConvenioUnilateralGrupo($data['form']['convenio_unilateral_aplicacion_grupo'] ? $data['form']['convenio_unilateral_aplicacion_grupo'] : null);
        $emObj->setPrestacionServicioPorcentajeExcension($data['form']['prestacion_servicio_porcentaje_excension'] ? $data['form']['prestacion_servicio_porcentaje_excension'] : null);
        $emObj->setPrestacionServicioFechaDesde($data['form']['prestacion_servicio_fecha_desde'] ? New \Datetime($data['form']['prestacion_servicio_fecha_desde']) : null);
        $emObj->setPrestacionServicioFechaHasta($data['form']['prestacion_servicio_fecha_hasta'] ? New \Datetime($data['form']['prestacion_servicio_fecha_hasta']) : null);
        if (isset($data['form']['tipo_prestacion_asistencia_tecnica'])) {
            $emObj->setTipoPrestacionAsistenciaTecnica(1);
        } else {
            $emObj->setTipoPrestacionAsistenciaTecnica(0);
        }
        $emObj->setTipoPrestacionOtros($data['form']['tipo_prestacion_otros'] ? $data['form']['tipo_prestacion_otros'] : null);
        $emObj->setConvenioTributacionInternacional($data['form']['convenio_tributacion_internacional'] ? $data['form']['convenio_tributacion_internacional'] == 'true' ? 1 : 0 : null);
        $emObj->setEstablecimientoArgentina($data['form']['establecimiento_argentina'] ? $data['form']['establecimiento_argentina'] == 'true' ? 1 : 0 : null);
        $emObj->setConvenioUnilateral($data['form']['convenio_unilateral'] ? $data['form']['convenio_unilateral'] == 'true' ? 1 : 0 : null);
        $emObj->setArticuloDeConvenioAplicable(@$data['form']['articulo_de_convenio_aplicable'] ? @$data['form']['articulo_de_convenio_aplicable'] : null);
        $emObj->setIdDatoPersonal($proveedorDatoPersonal);
        $emObj->setTipoActividad($tipoActividadExportacion);

        $em->merge($emObj);
        $em->flush();
    }

    /**
     * @param $data
     * @param $em
     * @param $tipoActividadExportacion
     * @param $proveedorDatoPersonal
     * @return array
     */
    public function guardarActividadNew($data, $em, $tipoActividadExportacion, $proveedorDatoPersonal)
    {
        $act = New ProveedorActividad();
        $act->setIdUsuario($this->getUser());
        $act->setExportacionBienes($data['form']['exportacion_bienes_extranjero'] == 'true' ? 1 : 0);
        $act->setPrestacionServicios($data['form']['prestacion_servicios_extranjero'] == 'true' ? 1 : 0);
        $act->setPrestacionServicioRegimen($data['form']['prestacion_servicio_regimen'] ? $data['form']['prestacion_servicio_regimen'] : null);
        $act->setPrestacionServicioNumero($data['form']['prestacion_servicio_numero'] ? $data['form']['prestacion_servicio_numero'] : null);
        $act->setPrestacionServicioPorcentajeExcension($data['form']['prestacion_servicio_porcentaje_excension'] ? $data['form']['prestacion_servicio_porcentaje_excension'] : null);
        $act->setPrestacionServicioFechaDesde($data['form']['prestacion_servicio_fecha_desde'] ? New \Datetime($data['form']['prestacion_servicio_fecha_desde']) : null);
        $act->setPrestacionServicioFechaHasta($data['form']['prestacion_servicio_fecha_hasta'] ? New \Datetime($data['form']['prestacion_servicio_fecha_hasta']) : null);
        if (isset($data['form']['tipo_prestacion_asistencia_tecnica'])) {
            $act->setTipoPrestacionAsistenciaTecnica(1);
        } else {
            $act->setTipoPrestacionAsistenciaTecnica(0);
        }
        $act->setTipoPrestacionOtros($data['form']['tipo_prestacion_otros'] ? $data['form']['tipo_prestacion_otros'] : null);
        $act->setConvenioUnilateralGrupo($data['form']['convenio_unilateral_aplicacion_grupo'] ? $data['form']['convenio_unilateral_aplicacion_grupo'] : null);
        $act->setConvenioUnilateralAplicacionCaba($data['form']['convenio_unilateral_aplicacion_caba'] ? $data['form']['convenio_unilateral_aplicacion_caba'] : null);
        $act->setConvenioTributacionInternacional($data['form']['convenio_tributacion_internacional'] ? $data['form']['convenio_tributacion_internacional'] == 'true' ? 1 : 0 : null);
        $act->setEstablecimientoArgentina($data['form']['establecimiento_argentina'] ? $data['form']['establecimiento_argentina'] == 'true' ? 1 : 0 : null);
        $act->setConvenioUnilateral($data['form']['convenio_unilateral'] ? $data['form']['convenio_unilateral'] == 'true' ? 1 : 0 : null);
        $act->setArticuloDeConvenioAplicable(@$data['form']['articulo_de_convenio_aplicable'] ? @$data['form']['articulo_de_convenio_aplicable'] : null);
        $act->setTipoActividad($tipoActividadExportacion);
        $act->setIdDatoPersonal($proveedorDatoPersonal);
        // $act->setExtranjero(1);
        $em->persist($act);
        $em->flush();
    }

}
