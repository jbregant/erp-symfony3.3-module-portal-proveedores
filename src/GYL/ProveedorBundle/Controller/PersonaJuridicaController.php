<?php

namespace GYL\ProveedorBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\DecimalType;
use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorUteMiembros;
use GYL\ProveedorBundle\Entity\ProveedorPersonaJuridicaMiembros;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorUte;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\TipoPersonaJuridica;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Bridge\Doctrine\Tests\Fixtures\Person;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as UniqueConstrait;

/**
 * DatosUte controller.
 *
 * @Route("/")
 */
class PersonaJuridicaController extends BaseController {

    /**
     * Agregar Persona Juridica .
     *
     * @Route("/formulariopreinscripcion/agregarpersonajuridica", name="agregar_persona")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarPersonaJuridicaAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $user = $this->getUser();
            $repo = $em->getRepository(ProveedorDatoPersonal::class);
            $dp = $repo->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($dp instanceof ProveedorDatoPersonal) {

                $repoTipoPersonaJuridica = $em->getRepository(TipoPersonaJuridica::class);
                $tipoPersonaJuridica = $repoTipoPersonaJuridica->findOneBy(['id' => $data['form']['tipo_persona_juridica']]);

                $dp->setTipoPersonaJuridica($tipoPersonaJuridica);
                if (isset($data['form']['direccion_web_persona_juridica'])) {
                    $dp->setDireccionWeb($data['form']['direccion_web_persona_juridica']);
                }
                if (isset($data['form']['nacionalidad'])) {
                    $dp->setIdPaisRadicacion($data['form']['nacionalidad']);
                }
                $dp->setRazonSocial($data['form']['razon_social_persona_juridica']);
                $dp->setFechaInicioActividades(new \DateTime($data['form']['fecha_inicio_actividad_persona_juridica']));
                $em->merge($dp);
                $em->flush();
                $response = 200;
                $msg = 'ok';
            }
            $this->guardarTimeline('timeline_persona_juridica', 'completo', 1, $data['form']['id_dato_personal']);
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
     * Eliminar Miembro Persona Juridica .
     *
     * @Route("/formulariopreinscripcion/eliminarmiembropersonajuridica", name="eliminar_miembro_persona_juridica")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function eliminarMiembroExtranjeroAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoMPj = $em->getRepository(ProveedorPersonaJuridicaMiembros::class);
            $mPj = $RepoMPj->findOneBy(['id' => $data['id']]);
            $em->remove($mPj);
            $em->flush();
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
     * Agregar Miembro Persona Juridica .
     *
     * @Route("/formulariopreinscripcion/agregarmiembroextranjero", name="agregar_miembrroç_extranjero")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarMiembroExtranjeroeAction(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $user = $this->getUser();
            $repoDp = $em->getRepository(ProveedorDatoPersonal::class);
            $repoPJMDP = $em->getRepository(ProveedorPersonaJuridicaMiembros::class);

            $porcentajeParticipacion = $data['form']['participacion_persona_juridica'];

            if ($porcentajeParticipacion > 100 || $porcentajeParticipacion < 0) {
                $response = 206;
                $msg = 'ok';
                $idpersisted = 'Empty';
            } else {

                $datoPersonal = $repoDp->findOneBy(['id' => $data['form']['id_dato_personal']]);

                if ($datoPersonal instanceof ProveedorDatoPersonal) {

                    $repoMiembroPersonaJuridica = $em->getRepository(ProveedorPersonaJuridicaMiembros::class);
                    $miembrosContador = $repoMiembroPersonaJuridica->findBy(['proveedorDatoPersonal' => $datoPersonal->getId()]);

                    $pjflag = $repoPJMDP->findOneBy(['proveedorDatoPersonal' => $datoPersonal->getId(), 'cuit' => $data['form']['cuit_persona_juridica']]);

                    if ($pjflag instanceof ProveedorPersonaJuridicaMiembros) {
                        $response = 204;
                        $msg = 'ok';
                        $idpersisted = 'Empty';
                    } else {
                        if (count($miembrosContador) > 0) {
                            foreach ($miembrosContador as $value) {
                                $porcentajeParticipacion += $value->getParticipacion();
                            }
                            if ($porcentajeParticipacion > 100 || $porcentajeParticipacion < 0) {
                                $response = 206;
                                $msg = 'ok';
                                $idpersisted = 'Empty';
                            } else {
                                $pjmdp = new ProveedorPersonaJuridicaMiembros();

                                $pjmdp->setCuit($data['form']['cuit_persona_juridica']);
                                $pjmdp->setNombre($data['form']['nombre_persona_juridica']);
                                $pjmdp->setApellido($data['form']['apellido_persona_juridica']);
                                $pjmdp->setParticipacion($data['form']['participacion_persona_juridica']);
                                $pjmdp->setProveedorDatoPersonal($datoPersonal);
                                $em->persist($pjmdp);
                                $em->flush();
                                $response = 200;
                                $msg = 'ok';
                                $idpersisted = $pjmdp->getId();
                                $data['form']['id'] = $idpersisted;
                            }
                        } else {
                            $pjmdp = new ProveedorPersonaJuridicaMiembros();

                            $pjmdp->setCuit($data['form']['cuit_persona_juridica']);
                            $pjmdp->setNombre($data['form']['nombre_persona_juridica']);
                            $pjmdp->setApellido($data['form']['apellido_persona_juridica']);
                            $pjmdp->setParticipacion($data['form']['participacion_persona_juridica']);
                            $pjmdp->setProveedorDatoPersonal($datoPersonal);
                            $em->persist($pjmdp);
                            $em->flush();
                            $response = 200;
                            $msg = 'ok';
                            $idpersisted = $pjmdp->getId();
                            $data['form']['id'] = $idpersisted;
                        }
                    }
                } else {
                    $response = 500;
                    $msg = 'ok';
                    $idpersisted = 'empty';
                }
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
     * Editar miembro adicional.
     *
     * @Route("/formulariopreinscripcion/editarmiembroextranjero/{id}", name="editar_miembro")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    function editarMiembroExtranjeroeAction($id, Request $request) {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }
        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoPJMDP = $em->getRepository(ProveedorPersonaJuridicaMiembros::class);
            $pjmdp = $repoPJMDP->findOneBy(['id' => $id]);

            $pjmdp->setCuit($data['form']['cuit_persona_juridica']);
            $pjmdp->setNombre($data['form']['nombre_persona_juridica']);
            $pjmdp->setApellido($data['form']['apellido_persona_juridica']);
            $pjmdp->setParticipacion($data['form']['participacion_persona_juridica']);
            $em->merge($pjmdp);
            $em->flush();
            $response = 200;
            $msg = 'ok';

            $data['form']['id'] = $id;

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
            'data' => $data
        ]);
    }

    /**
     * Datos Persona Juridica .
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosPersonaJuridicaAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral : 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_persona_juridica']);

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

        $repomiembrosPersonaJuridica = $em->getRepository(ProveedorPersonaJuridicaMiembros::class);
        $repoTipoPersonasJuridicas = $em->getRepository(TipoPersonaJuridica::class);

        $repo = $em->getRepository(ProveedorDatoPersonal::class);
        $persona = $repo->findOneBy(array('id' => $idProvDatoPersonal));
        $miembrosPersonaJuridica = $repomiembrosPersonaJuridica->findBy(array('proveedorDatoPersonal' => $idProvDatoPersonal));


        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_persona_juridica'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));

        $tipoPersonasJuridicas = $repoTipoPersonasJuridicas->findAll();

        $tipoPersonasJuridicasChoices = [];

        foreach ($tipoPersonasJuridicas as $table2Obj) {
            $tipoPersonasJuridicasChoices[$table2Obj->getDenominacion()] = $table2Obj->getId();
        }


        $form = $this->createFormBuilder()
                ->add('tipo_persona_juridica', ChoiceType::class, array(
                    'mapped' => false,
                    'choices' => $tipoPersonasJuridicasChoices,
                    'data' => $persona != null ? $persona->getTipoPersonaJuridica() != null ? $persona->getTipoPersonaJuridica()->getId() : null : null
                ))
                ->add('razon_social_persona_juridica', TextType::class, array('label' => '*Apellido', 'data' => $persona ? $persona->getRazonSocial() : '',
                    'attr' => array('maxlength' => 36)))
                ->add('direccion_web_persona_juridica', TextType::class, array('label' => '*Direcci&oacute;n Web', 'data' => $persona ? $persona->getDireccionWeb() : '',
                    'attr' => array('maxlength' => 36)))
                ->add('fecha_inicio_actividad_persona_juridica', DateType::class, array('label' => ' ', 'data' => $persona ? $persona->getFechaInicioActividades() : new \DateTime,
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']))
                ->getForm();


        $formMiembrosPersonaJuridica = $this->createFormBuilder()
            ->add('nombre_persona_juridica', TextType::class, array('label' => 'Nombre',
                'attr' => array('maxlength' => 36),))
            ->add('cuit_persona_juridica', TextType::class, array('label' => 'Cuit',
                'attr' => array('maxlength' => 36),))
            ->add('apellido_persona_juridica', TextType::class, array('label' => 'Apellido',
                'attr' => array('maxlength' => 36),))
            ->add('participacion_persona_juridica', IntegerType::class, array('label' => '%Participacion',
                'attr' => array('min' => 0, 'max' => 100),))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_persona_juridica.html.twig', array(
                    'datosPersonaJuridica' => $form->createView(),
                    'formMiembrosPersonaJuridica' => $formMiembrosPersonaJuridica->createView(),
                    'documentacionPersonaJuridica' => $documentacion ? $documentacion : array(),
                    'miembrosPersonaJuridica' => $miembrosPersonaJuridica ? $miembrosPersonaJuridica : array(),
                    'estadoEvaluacionGral' => $estadoEvaluacionGral,
                    'observacionEvaluacion' => $observacionEvaluacion,
                    'unlockForm' => $unlockForm
        ));
    }

}
