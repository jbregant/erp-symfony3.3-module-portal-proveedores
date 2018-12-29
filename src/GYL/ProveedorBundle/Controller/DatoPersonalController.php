<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\TipoPersona;
use GYL\ProveedorBundle\Entity\TipoProveedor;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

/**
 * PreRegistroProveedor controller.
 *
 * @Route("/")
 */
class DatoPersonalController extends BaseController
{
    /**
     * Agregar persona fisica.
     *
     * @Route("/formulariopreinscripcion/agregardatopersonal", name="agregar_dato_personal")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarDatoPersonalAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $user = $this->getUser();

            //TODO AGREGAR CUIT/EMAIL para agregar datos personales
//            $dp = New ProveedorDatoPersonal();
//            $dp->setNombre($data['form']['nombre']);
//            $dp->setApellido($data['form']['apellido']);
//            $dp->setNumeroDocumento($data['form']['numero_documento']);
//            $dp->setId_Usuario($user);
//            $em->persist($dp);
//            $em->flush();


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
            'msg' => $msg
        ]);


    }

    /**
     * Modificar dato personal.
     *
     * @Route("/formulariopreinscripcion/modificardatopersonal", name="modificar_dato_personal")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function modificarDatoPersonalAction(Request $request)
    {

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
            $pf = $repo->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($pf instanceof ProveedorDatoPersonal) {
                $pf->setNombre($data['form']['nombre']);
                $pf->setApellido($data['form']['apellido']);
                $pf->setNumeroDocumento($data['form']['numero_documento']);
                $pf->setTipoDocumento($data['form']['tipo_documento']);
                $em->merge($pf);
                $em->flush();

                $request->getSession()->set('impuestos', true);
                $this->guardarTimeline('timeline_persona_fisica', 'completo', 1, $data['form']['id_dato_personal']);

                $response = 200;
                $msg = 'ok';
            } else {
                $response = 304;
                $msg = 'ok';
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
     * Modificar tipo proveedor.
     *
     * @Route("/formulariopreinscripcion/modificartipoproveedor", name="modificar_tipo_proveedor")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function modificarTipoProveedorAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        if ($this->getUser() == null) {
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        try {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repo = $em->getRepository(ProveedorDatoPersonal::class);
            $repoTipoProveedor = $em->getRepository(TipoProveedor::class);
            $tp = $repoTipoProveedor->findOneBy(['denominacion' => $data['denom']]);
            $repoTipoPersona = $em->getRepository(TipoPersona::class);

            // Modifico el tipo de persona para extranjeros.
            $idTp = ($tp->getId() > 3)? ($tp->getId()-3) : $tp->getId();
            $tipoPersona = $repoTipoPersona->findOneBy(['id' => $idTp]);

            $pf = $repo->findOneBy(['id' => $data['idDatoPersonal']]);

            if ($pf instanceof ProveedorDatoPersonal && $tp instanceof TipoProveedor) {
                if($tipoPersona instanceof TipoPersona){
                    $pf->setTipoPersona($tipoPersona);
                }
                $pf->setTipoProveedor($tp);
                $em->merge($pf);
                $em->flush();
                $response = 200;
                $msg = 'ok';
            } else {
                $response = 304;
                $msg = 'ok';
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
     * Dato Personal.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosPersonalesAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);

        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_persona_fisica']);

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

        $repo = $em->getRepository(ProveedorDatoPersonal::class);
        $persona = $repo->findOneBy(['id' => $idProvDatoPersonal]);

        $sql = "SELECT * FROM tipo_documento";
        $emRrhh = $this->getDoctrine()->getManager('adif_rrhh');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        $tipos_documentos = $stmt->fetchAll();

        $choices = [];

        foreach ($tipos_documentos as $table2Obj) {
            $choices[$table2Obj['nombre']] = $table2Obj['id'];
        }


        $personaFisica = $this->createFormBuilder()
            ->add('nombre', TextType::class, array('label' => '*Nombre', 'data' => $persona ? $persona->getNombre() : '',
                 'attr' => array('maxlength' => 36)
            ))
            ->add('apellido', TextType::class, array('label' => '*Apellido', 'data' => $persona ? $persona->getApellido() : '',
                'attr' => array('maxlength' => 36)))
            ->add('tipo_documento', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices,
                'placeholder' => 'Seleccione una opcion',
                'data' => $persona ? $persona->getTipoDocumento() : null,
                'choice_attr' => function ($val, $key, $index) { //se disablea la opcion 'EXTRANJERO'
                    return ($key == ProveedorDatoPersonal::EXTRANJERO)? array('disabled' => true): array('disabled' => false);
                },
                'required' => true
            ))
            ->add('numero_documento', TextType::class, array('label' => 'Numero de Documento', 'required' => false, 'data' => $persona ? $persona->getNumeroDocumento() : '',
                'attr' => array('maxlength' => 36)))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_persona_fisica.html.twig', array(
            'personaFisica' => $personaFisica->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }


}
