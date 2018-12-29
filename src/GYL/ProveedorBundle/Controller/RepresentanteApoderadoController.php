<?php

namespace GYL\ProveedorBundle\Controller;

use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorRepresentanteApoderado;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * RepresentanteApoderado  controller.
 *
 * @Route("/")
 */
class RepresentanteApoderadoController extends BaseController {

    /**
     * Agregar Representante.
     *
     * @Route("/formulariopreinscripcion/modificarrepresentantes", name="modificar_representantes")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarRepresentanteAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $array_file = $request->request->get('data_array');
            $idDatoPersonal = $request->request->get('idDatoPersonal');

            if ($array_file) {
                foreach ($array_file as $representante) {

                    $em = $this->getDoctrine()->getManager('adif_proveedores');
                    $repoRepre = $em->getRepository(ProveedorRepresentanteApoderado::class);
                    $repre = $repoRepre->findOneBy(['id' => str_replace(".", "", $representante[0])]);

                    if ($repre instanceof ProveedorRepresentanteApoderado) {

                        $repre->setRepresentante($representante[7] == 'false' ? 0 : 1);
                        $repre->setApoderado($representante[8] == 'false' ? 0 : 1);
                        $repre->setPoderJudicial($representante[9] == 'false' ? 0 : 1);
                        $repre->setBancario($representante[10] == 'false' ? 0 : 1);
                        $repre->setAdmEspecial($representante[11] == 'false' ? 0 : 1);
                        $repre->setAdmGeneral($representante[12] == 'false' ? 0 : 1);

                        $em->merge($repre);
                        $em->flush();

                        $this->guardarTimeline('timeline_representantes_apoderados', 'completo', 1, $idDatoPersonal);
                    }
                }
            } else {
                $this->guardarTimeline('timeline_representantes_apoderados', 'completo', 1, $idDatoPersonal);
            }

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
     * Editar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/editarrepresentante/{id}", name="editar_representante")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function editarRepresentanteAction($id, Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $request = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoRepre = $em->getRepository(ProveedorRepresentanteApoderado::class);

            $ra = $repoRepre->findOneBy(['id' => $id]);
            $ra->setIdUsuario($user = $this->getUser());
            $ra->setNombre($request['form']['nombre_apod']);
            $ra->setApellido($request['form']['apellido_apod']);
            $ra->setCuitCuil($request['form']['cuit_cuil_apod']);
            $ra->setTipoDocumento($request['form']['tipo_documento_apod']);
            $ra->setNumeroDocumento(str_replace(".", "", $request['form']['numero_documento_apod']));
            $ra->setFechaDesignacion(new \DateTime($request['form']['fecha_designacion_apod']));

            $request['form']['id'] = $id;

            $em->merge($ra);
            $em->flush();

            $response = 200;
        } catch (Exception $e) {
            $response = 500;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => 'ok',
            'data' => $request,
        ]);
    }

    /**
     * Buscar Representante.
     *
     * @Route("/formulariopreinscripcion/agregarnuevorepresentante", name="agregar_nuevo_representante")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function buscarRepresentanteAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $request = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $proveedorDatoPersonal = $repoProveedorDatoPersonal->findOneBy(['id' => $request['form']['id_dato_personal']]);
            $repoRepre = $em->getRepository(ProveedorRepresentanteApoderado::class);
            $repre = $repoRepre->findOneBy(['numeroDocumento' => str_replace(".", "", $request['form']['numero_documento_apod']), 'idDatoPersonal' => $request['form']['id_dato_personal']]);

            if ($repre instanceof ProveedorRepresentanteApoderado) {
//                $data = 'existente';
                $msg = 'existente';
            } else {
                $ra = New ProveedorRepresentanteApoderado();
                $ra->setIdUsuario($user = $this->getUser());
                $ra->setNombre($request['form']['nombre_apod']);
                $ra->setApellido($request['form']['apellido_apod']);
                $ra->setCuitCuil($request['form']['cuit_cuil_apod']);
                $ra->setTipoDocumento($request['form']['tipo_documento_apod']);
                $ra->setNumeroDocumento(str_replace(".", "", $request['form']['numero_documento_apod']));
                $ra->setFechaDesignacion(new \DateTime($request['form']['fecha_designacion_apod']));
                $ra->setIdDatoPersonal($proveedorDatoPersonal);
                $em->persist($ra);
                $em->flush();
                $msg = 'ok';
                $request['form']['id'] = $ra->getId();
            }

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
            'data' => $request
        ]);
    }

    /**
     * Eliminar representante Apoderado.
     *
     * @Route("/formulariopreinscripcion/eliminarepresentanteapoderado/", name="eliminar_representante_apoderado")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function eliminarContactoAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoRepre = $em->getRepository(ProveedorRepresentanteApoderado::class);
            $repre = $RepoRepre->findOneBy(['id' => $data['id']]);
            $em->remove($repre);
            $em->flush();
            $response = 200;
            
            if ($data['representantesApoderados'] == 'true') {
                $this->guardarTimeline('timeline_representantes_apoderados', 'completo', 1, $data['idDatoPersonal']);
            } else {
                $this->guardarTimeline('timeline_representantes_apoderados', 'incompleto', 2, $data['idDatoPersonal']);
            }
        } catch (Exception $e) {
            $response = 500;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => 'ok',
        ]);
    }

    /**
     * Rrepresentantes.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function representantesApoderadosAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral : 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'representante_apoderado']);

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

        $repo = $em->getRepository(ProveedorRepresentanteApoderado::class);
        $entitiesRepresentantes = $repo->findBy(array('idDatoPersonal' => $idProvDatoPersonal));

        $sql = "SELECT * FROM tipo_documento";
        $emRrhh = $this->getDoctrine()->getManager('adif_rrhh');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        $tipos_documentos = $stmt->fetchAll();

        $choices = [];
        foreach ($tipos_documentos as $table2Obj) {
            $choices[$table2Obj['nombre']] = $table2Obj['id'];
        }

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_representante_apoderado'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));


        $form = $this->createFormBuilder()
            ->add('nombre_apod', TextType::class, array('label' => 'Nombre',
                'attr' => array('maxlength' => 36)))
            ->add('apellido_apod', TextType::class, array('label' => 'Apellido',
                'attr' => array('maxlength' => 36)))
            ->add('cuit_cuil_apod', TextType::class, array('label' => 'Cuit/Cuil',
                'attr' => array('maxlength' => 36)))
            ->add('tipo_documento_apod', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices,
                'choice_attr' => function ($val, $key, $index) { //se disablea la opcion 'EXTRANJERO'
                    return ($key == ProveedorDatoPersonal::EXTRANJERO)? array('disabled' => true): array('disabled' => false);
                }
            ))
            ->add('numero_documento_apod', TextType::class, array('label' => 'Numero Documento', 'attr' => array('maxlength' => 15)))
            ->add('fecha_designacion_apod', DateType::class, array('label' => '*Fecha de Finalización de Contrato', 'data' => new \DateTime,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_representantes_apoderados.html.twig', array(
                    'formRrepresentante' => $form->createView(),
                    'representantes' => $entitiesRepresentantes,
                    'documentacionRepresentantes' => $documentacion ? $documentacion : array(),
                    'estadoEvaluacionGral' => $estadoEvaluacionGral,
                    'observacionEvaluacion' => $observacionEvaluacion,
                    'unlockForm' => $unlockForm
        ));
    }

}
