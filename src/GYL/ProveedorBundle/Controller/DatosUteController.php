<?php

namespace GYL\ProveedorBundle\Controller;

use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorUteMiembros;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorUte;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * DatosUte controller.
 *
 * @Route("/")
 */
class DatosUteController extends BaseController {

    /**
     * Agregar ute.
     *
     * @Route("/formulariopreinscripcion/agregarute", name="agregar_ute")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarUteAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoUte = $em->getRepository(ProveedorUte::class);
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $user = $this->getUser();
            $denominacionExistente = $RepoUte->findByExistingDenom($data['form']['denominacion'], $data['form']['id_dato_personal']);

            if ($denominacionExistente) {
                $response = 204;
                $msg = 'ok';
            } else {
                $pf = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);
                $proveedorUte = $RepoUte->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);

                if ($proveedorUte instanceof ProveedorUte) {

                    $proveedorUte->setDenominacion($data['form']['denominacion']);
                    $proveedorUte->setFechaConstitucion(new \DateTime($data['form']['fecha_constitucion']));
                    $proveedorUte->setFechaFinalizacion(new \DateTime($data['form']['fecha_finalizacion']));
                    $proveedorUte->setNumeroInscripcion($data['form']['numero_inscripcion']);
                    $proveedorUte->setNombreFantasia($data['form']['nombre_fantasia']);
                    $proveedorUte->setRazonSocial($data['form']['razon_social']);
                    $proveedorUte->setUrl($data['form']['url']);
                    $proveedorUte->setId_Usuario($this->getUser());
                    $proveedorUte->setIdDatoPersonal($pf);

                    //se graba la razon social de la ute en la entidad ProveedorDatoPersonal
                    $pf->setRazonSocial($data['form']['razon_social']);
                    $em->merge($proveedorUte);
                    $em->merge($pf);
                    $em->flush();

                    $response = 200;
                    $msg = 'ok';
                } else {

                    $ute = New ProveedorUte();
                    $ute->setDenominacion($data['form']['denominacion']);
                    $ute->setFechaConstitucion(new \DateTime($data['form']['fecha_constitucion']));
                    $ute->setFechaFinalizacion(new \DateTime($data['form']['fecha_finalizacion']));
                    $ute->setNumeroInscripcion($data['form']['numero_inscripcion']);
                    $ute->setNombreFantasia($data['form']['nombre_fantasia']);
                    $ute->setRazonSocial($data['form']['razon_social']);
                    $ute->setUrl($data['form']['url']);
                    $ute->setId_Usuario($this->getUser());
                    $ute->setIdDatoPersonal($pf);


                    //se graba la razon social de la ute en la entidad ProveedorDatoPersonal
                    $pf->setRazonSocial($data['form']['razon_social']);
                    $em->merge($ute);
                    $em->merge($pf);
                    $em->flush();

                    $response = 200;
                    $msg = 'ok';
                }

                if ($data['form']['miembros'] == 'true') {
                    $this->guardarTimeline('timeline_contratos_ute', 'completo', 1, $data['form']['id_dato_personal']);
                } else {
                    $response = 202;
                    $msg = 'ok';
                    $this->guardarTimeline('timeline_contratos_ute', 'incompleto', 2, $data['form']['id_dato_personal']);
                }
            }
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
            'msg' => $msg
        ]);
    }


    /**
     * Eliminar ute.
     *
     * @Route("/formulariopreinscripcion/eliminarmiembroute", name="eliminar_miembro_ute")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function eliminarUteAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoMUte = $em->getRepository(ProveedorUteMiembros::class);
            $Mute = $RepoMUte->findOneBy(['id' => $data['id']]);
            $em->remove($Mute);
            $em->flush();
            $response = 200;
            $msg = 'ok';

            if ($data['miembros'] == 'true') {
                $this->guardarTimeline('timeline_contratos_ute', 'completo', 1, $data['idDatoPersonal']);
            } else {
                $this->guardarTimeline('timeline_contratos_ute', 'incompleto', 2, $data['idDatoPersonal']);
            }
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
     * Agregar MiembrosUte.
     *
     * @Route("/formulariopreinscripcion/agregarmiembrosute", name="agregar_miembros_ute")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarMiembroUteAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoUte = $em->getRepository(ProveedorUte::class);
            $user = $this->getUser();
            $ute = $RepoUte->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);
            $idpersisted = 'Empty';
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class)->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($ute instanceof ProveedorUte) {
                $RepoMiembroUte = $em->getRepository(ProveedorUteMiembros::class);
                $Mute = $RepoMiembroUte->findOneBy(['ute' => $ute->getId(), 'cuit' => $data['form']['cuit']]);

                if ($Mute instanceof ProveedorUteMiembros) {
                    $response = 304;
                    $msg = 'ok';
                } else {
                    $cuit = $data['form']['cuit'];
                    $sql2 = "SELECT id FROM cliente_proveedor WHERE cuit =  '$cuit'  LIMIT 1";
                    $emAdif = $this->getDoctrine()->getManager('adif_compras');
                    $stmtc = $emAdif->getConnection()->prepare($sql2);
                    $stmtc->execute();
                    $sigaProveedor = $stmtc->fetchAll();

                    if ($sigaProveedor) {
                        $porcentajeGanancias = $data['form']['participacion_ganancias'];
                        $porcentajeRemuneraciones = $data['form']['participacion_remunerativa'];
                        $utesContador = $RepoMiembroUte->findBy(['ute' => $ute->getId()]);

                        if (count($utesContador) > 0) {

                            foreach ($utesContador as $u) {
                                $porcentajeGanancias += $u->getParticipacionGanancias();
                                $porcentajeRemuneraciones += $u->getParticipacionRemunerativa();
                            }
                            if ($porcentajeGanancias > 100 || $porcentajeRemuneraciones > 100) {
                                $response = 206;
                                $msg = 'ok';
                            } else {
                                $uteM = New ProveedorUteMiembros();
                                $uteM->setCuit($data['form']['cuit']);
                                $uteM->setNumeroInscripcion($data['form']['numero_inscripcion_miembro']);
                                $uteM->setRazonSocial($data['form']['razon_social_miembro']);
                                $uteM->setParticipacionGanancias($data['form']['participacion_ganancias']);
                                $uteM->setParticipacionRemunerativa($data['form']['participacion_remunerativa']);
                                $uteM->setUte($ute);
                                $uteM->setEmpleador($data['form']['empleador']);
                                $uteM->setIdDatoPersonal($repoProvDatoPersonal);
                                $em->persist($uteM);
                                $em->flush();
                                $response = 200;
                                $msg = 'ok';
                                $idpersisted = $uteM->getId();
                                $data['form']['id'] = $idpersisted;
                                $this->guardarTimeline('timeline_contratos_ute', 'completo', 1, $data['form']['id_dato_personal']);
                            }
                        } else {
                            if ($porcentajeGanancias > 100 || $porcentajeRemuneraciones > 100) {
                                $response = 206;
                                $msg = 'ok';
                            } else {
                                $uteM = New ProveedorUteMiembros();
                                $uteM->setCuit($data['form']['cuit']);
                                $uteM->setNumeroInscripcion($data['form']['numero_inscripcion_miembro']);
                                $uteM->setRazonSocial($data['form']['razon_social_miembro']);
                                $uteM->setParticipacionGanancias($data['form']['participacion_ganancias']);
                                $uteM->setParticipacionRemunerativa($data['form']['participacion_remunerativa']);
                                $uteM->setUte($ute);
                                $uteM->setIdDatoPersonal($repoProvDatoPersonal);
                                $uteM->setEmpleador($data['form']['empleador']);

                                $em->persist($uteM);
                                $em->flush();
                                $response = 200;
                                $msg = 'ok';
                                $idpersisted = $uteM->getId();
                                $data['form']['id'] = $idpersisted;
                                $this->guardarTimeline('timeline_contratos_ute', 'completo', 1, $data['form']['id_dato_personal']);
                            }
                        }
                    } else {
                        $response = 305;
                        $msg = 'ok';
                    }
                }
            } else {
                $response = 201;
                $msg = 'ok';
            }
        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $idpersisted = 'Empty';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $idpersisted = 'Empty';
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
     * Editar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/editarmiembroute/{id}", name="editar_miembro_ute")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    function editarMiembroUte($id, Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoMiembro = $em->getRepository(ProveedorUteMiembros::class);
            $miembro = $RepoMiembro->findOneBy(['id' => $id]);
            $miembro->setCuit($data['form']['cuit']);
            $miembro->setNumeroInscripcion($data['form']['numero_inscripcion_miembro']);
            $miembro->setRazonSocial($data['form']['razon_social_miembro']);
            $miembro->setParticipacionGanancias($data['form']['participacion_ganancias']);
            $miembro->setParticipacionRemunerativa($data['form']['participacion_remunerativa']);
            $miembro->setEmpleador($data['form']['empleador']);


            $data['form']['id'] = $id;

            $em->merge($miembro);
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
            'data' => $data,
        ]);
    }

    /**
     * Datos Ute.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosUteAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral : 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_ute']);

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

        $repo = $em->getRepository(ProveedorUte::class);

        $ute = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal));

        $repoMUtes = $em->getRepository(ProveedorUteMiembros::class);

        if ($ute instanceof ProveedorUte) {
            $utesM = $repoMUtes->findBy(array('ute' => $ute->getId()));
        } else {
            $utesM = array();
        }

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'miembros_ute'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));

        //Form Ute
        $form = $this->createFormBuilder()
                ->add('denominacion', TextType::class, array('label' => ' ', 'data' => $ute ? $ute->getDenominacion() : '',
                    'attr' => array('maxlength' => 36),))
                ->add('fecha_constitucion', DateType::class, array('label' => ' ', 'data' => $ute ? $ute->getFechaConstitucion() : null,
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']))
                ->add('fecha_finalizacion', DateType::class, array('label' => ' ', 'data' => $ute ? $ute->getFechaFinalizacion() : null,
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']))
                ->add('numero_inscripcion', TextType::class, array('label' => ' ', 'data' => $ute ? $ute->getNumeroInscripcion() : '',
                    'attr' => array('maxlength' => 36),))
                ->add('razon_social', TextType::class, array('label' => ' ', 'data' => $ute ? $ute->getRazonSocial() : '',
                    'attr' => array('maxlength' => 36),))
                ->add('url', TextType::class, array('label' => ' ', 'required' => false, 'data' => $ute ? $ute->getUrl() : '',
                    'attr' => array('maxlength' => 36),))
                ->add('nombre_fantasia', TextType::class, array('label' => ' ', 'data' => $ute ? $ute->getNombreFantasia() : '',
                    'attr' => array('maxlength' => 36),))
                ->getForm();

        $form->handleRequest($request);

        //Form miembros Ute

        $formMiembro = $this->createFormBuilder()
                ->add('cuit', TextType::class, array('label' => '*Cuit',
                    'attr' => array('maxlength' => 36),
                ))
                ->add('razon_social_miembro', TextType::class, array(
                    'label' => '*Razón Social',
                    'attr' => array('maxlength' => 36,)))
                ->add('numero_inscripcion_miembro', TextType::class, array('label' => '*N° de Inscripción ICGJ (Registro Público de comercio, etc)',
                    'attr' => array('maxlength' => 36)))
                ->add('participacion_ganancias', IntegerType ::class, array('label' => '*Participacion Ganancias', 'required' => false,
                    'attr' => array('min' => 0, 'max' => 100),
                    'scale' => 2))
                ->add('empleador', HiddenType::class, array('label' => ''))
                ->add('participacion_remunerativa', IntegerType ::class, array('label' => 'Participación Remunerativa',
                    'data' => 0,
                    'attr' => array('min' => 0, 'max' => 100),
                    'scale' => 2))
                ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_datos_ute.html.twig', array(
                    'datosUte' => $form->createView(),
                    'utesM' => $utesM,
                    'formMiembro' => $formMiembro->createView(),
                    'documentacionUte' => $documentacion ? $documentacion : array(),
                    'estadoEvaluacionGral' => $estadoEvaluacionGral,
                    'observacionEvaluacion' => $observacionEvaluacion,
                    'unlockForm' => $unlockForm
        ));
    }

}
