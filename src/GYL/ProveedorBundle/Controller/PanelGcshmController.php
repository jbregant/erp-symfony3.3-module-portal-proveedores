<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoGcshm;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\UsuarioBundle\Entity\Invitacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GYL\UsuarioBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Dato Gcshm controller.
 *
 * @Route("/")
 */
class PanelGcshmController extends BaseController
{
    /**
     * Agregar Gcshm.
     *
     * @Route("/formulariopreinscripcion/agregargcshm", name="agregar_agregargcshm")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function aregarGcshmAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoGcsm = $em->getRepository(ProveedorDatoGcshm::class);
            $datoGcshm = $repoGcsm->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($datoGcshm instanceof ProveedorDatoGcshm) {

                $datoGcshm->setCertificacionIso9001($data['form']['certificacion_iso9001'] == 'true' ? 1 : 0);
                $datoGcshm->setCertificacionIso14001($data['form']['certificacion_iso14001'] == 'true' ? 1 : 0);
                $datoGcshm->setCertificacionOsha18001($data['form']['certificacion_osha18001'] == 'true' ? 1 : 0);
                if (isset ($data['form']['permisos_ambientales'])) {
                    $datoGcshm->setPermisosAmbientales($data['form']['permisos_ambientales']);
                } else {
                    $datoGcshm->setPermisosAmbientales(0);
                }
                if (isset ($data['form']['documentacion_evaluacion'])) {
                    $datoGcshm->setDocumentacionEvaluacion($data['form']['documentacion_evaluacion']);
                } else {
                    $datoGcshm->setDocumentacionEvaluacion(0);
                }
                if (isset ($data['form']['organigrama_institucional_obra'])) {
                    $datoGcshm->setOrganigramaInstitucionalObra($data['form']['organigrama_institucional_obra']);
                } else {
                    $datoGcshm->setOrganigramaInstitucionalObra(0);
                }
                if (isset ($data['form']['pese'])) {
                    $datoGcshm->setPese($data['form']['pese']);
                } else {
                    $datoGcshm->setPese(0);
                }
                $datoGcshm->setIdUsuario($this->getUser());

                $em->merge($datoGcshm);
                $em->flush();

                $this->guardarTimeline('timeline_gcshm', 'completo', 1, $data['form']['id_dato_personal']);

            } else {

                $pdG = New ProveedorDatoGcshm();
                $pdG->setCertificacionIso9001($data['form']['certificacion_iso9001'] == 'true' ? 1 : 0);
                $pdG->setCertificacionIso14001($data['form']['certificacion_iso14001'] == 'true' ? 1 : 0);
                $pdG->setCertificacionOsha18001($data['form']['certificacion_osha18001'] == 'true' ? 1 : 0);
                if (isset ($data['form']['permisos_ambientales'])) {
                    $pdG->setPermisosAmbientales($data['form']['permisos_ambientales']);
                } else {
                    $pdG->setPermisosAmbientales(0);
                }
                if (isset ($data['form']['documentacion_evaluacion'])) {
                    $pdG->setDocumentacionEvaluacion($data['form']['documentacion_evaluacion']);
                } else {
                    $pdG->setDocumentacionEvaluacion(0);
                }
                if (isset ($data['form']['organigrama_institucional_obra'])) {
                    $pdG->setOrganigramaInstitucionalObra($data['form']['organigrama_institucional_obra']);
                } else {
                    $pdG->setOrganigramaInstitucionalObra(0);
                }
                if (isset ($data['form']['pese'])) {
                    $pdG->setPese($data['form']['pese']);
                } else {
                    $pdG->setPese(0);
                }
                $pdG->setIdUsuario($this->getUser());
                $pdG->setIdDatoPersonal($provDatoPersonal);

                $em->persist($pdG);
                $em->flush();

                $this->guardarTimeline('timeline_gcshm', 'completo', 1, $data['form']['id_dato_personal']);

            }

            $response = 200;
            $msg = 'ok';

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }catch (\Throwable $e) {
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
     * Datos Gcshm.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosGcshmAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_gcshm']);

        $observacionEvaluacion = null;

        if ($proveedorEvaluacion)
        {
            $observacionEvaluacion = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacion->getId(),
                'activo' => 1
            ]);

            if(empty($observacionEvaluacion))
                $observacionEvaluacion = null;
        }
        
        $repo = $em->getRepository(ProveedorDatoGcshm::class);
        $gcshm = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal));
        if ($gcshm == null) {
            $gcshm = new ProveedorDatoGcshm();
        }

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_gcshm'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));
        $tipoDocISO9001 = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_gcshm_iso9001'));
        $documentacionISO9001 = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocISO9001->getId()));
        $tipoDocISO14001 = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_gcshm_iso14001'));
        $documentacionISO14001 = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocISO14001->getId()));
        $tipoDocOSHA18001 = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_gcshm_osha18001'));
        $documentacionOSHA18001 = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocOSHA18001->getId()));

        $formDatosGcshm = $this->createFormBuilder()
            ->add('certificacion_iso9001', HiddenType::class, array('label' => ''))
            ->add('certificacion_iso14001', HiddenType::class, array('label' => ''))
            ->add('certificacion_osha18001', HiddenType::class, array('label' => ''))
            ->add('permisos_ambientales', CheckboxType::class, array('label' => 'Permisos Ambientales Provinciales Nacionales', 'data' => $gcshm->getPermisosAmbientales() == 1 ? true : false))
            ->add('documentacion_evaluacion', CheckboxType::class, array('label' => 'Documentación Oficial de Evaluación de Proveedor', 'data' => $gcshm->getDocumentacionEvaluacion() == 1 ? true : false))
            ->add('organigrama_institucional_obra', CheckboxType::class, array('label' => 'Organigrama de Calidad, Ambiente y Seguridad e Higiene (Institucional y Obra)', 'data' => $gcshm->getOrganigramaInstitucionalObra() == 1 ? true : false))
            ->add('pese', CheckboxType::class, array('label' => '¿Se encuentra dentro del Programa de Empleadores con Siniestralidad Elevada (P.E.S.E.)?', 'data' => $gcshm->getPese() == 1 ? true : false))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_gcshm.html.twig', array(
            'formDatosGcshm' => $formDatosGcshm->createView(),
            'gcshm' => $gcshm,
            'documentacionGcshm' => $documentacion ? $documentacion : array(),
            'documentacionGcshmISO9001' => $documentacionISO9001 ? $documentacionISO9001 : array(),
            'documentacionGcshmISO14001' => $documentacionISO14001 ? $documentacionISO14001 : array(),
            'documentacionGcshmOSHA18001' => $documentacionOSHA18001 ? $documentacionOSHA18001 : array(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

}
