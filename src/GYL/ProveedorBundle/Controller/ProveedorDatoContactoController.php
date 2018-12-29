<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoContacto;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\UsuarioBundle\Entity\Invitacion;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GYL\UsuarioBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * ProveedorDatoContacto controller.
 *
 * @Route("/")
 */
class ProveedorDatoContactoController extends BaseController
{
    /**
     * Agregar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/agregarcontacto", name="agregar_contacto")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarContactoAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class)->findOneBy(['id' => $data['form']['id_dato_personal']]);

            $contacto = New ProveedorDatoContacto();
            $contacto->setNombre($data['form']['nombre']);
            $contacto->setApellido($data['form']['apellido']);
            $contacto->setArea($data['form']['area']);
            $contacto->setPosicion($data['form']['posicion']);
            $contacto->setEmail($data['form']['email']);
            $contacto->setTelefono($data['form']['telefono']);
            $contacto->setId_usuario($this->getUser());
            $contacto->setIdDatoPersonal($repoDatoPersonal);

            $em->persist($contacto);
            $em->flush();
            $response = 200;

            $data['form']['id'] = $contacto->getId();//wtf??

            $this->guardarTimeline('timeline_dato_contacto', 'completo', 1, $data['form']['id_dato_personal']);

        } catch (Exception $e) {
            $response = 500;
            $data = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $data = 'error';
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
     * Eliminar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/eliminarcontacto", name="eliminar_contacto")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function eliminarContactoAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoContacto = $em->getRepository(ProveedorDatoContacto::class);
            $contacto = $RepoContacto->findOneBy(['id' => $data['id']]);
            $em->remove($contacto);
            $em->flush();
            $response = 200;
            
            if ($data['contactos'] == 'true') {
                $this->guardarTimeline('timeline_dato_contacto', 'completo', 1, $data['idDatoPersonal']);
            } else {
                $this->guardarTimeline('timeline_dato_contacto', 'incompleto', 2, $data['idDatoPersonal']);
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
     * Editar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/editarcontacto/{id}", name="editar_contacto")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function editarContactoAction($id, Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $RepoContacto = $em->getRepository(ProveedorDatoContacto::class);
            $contacto = $RepoContacto->findOneBy(['id' => $id]);
            $contacto->setNombre($data['form']['nombre']);
            $contacto->setApellido($data['form']['apellido']);
            $contacto->setArea($data['form']['area']);
            $contacto->setPosicion($data['form']['posicion']);
            $contacto->setEmail($data['form']['email']);
            $contacto->setTelefono($data['form']['telefono']);

            $data['form']['id'] = $id;
            
            $em->merge($contacto);
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
     * Guardar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/guardarcontacto", name="guardarcontacto")
     * @Method("GET|POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function guardarContactoAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $idDatoPersonal = $request->request->get('idDatoPersonal');
            $this->guardarTimeline('timeline_dato_contacto', 'completo', 1, $idDatoPersonal);
            $response = 200;

        } catch (Exception $e) {
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
     * Datos Iniciales.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosContactoAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
//        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;//wtf

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorDatoContacto::class);
        $entities = $repo->findBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_contacto']);

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
        
        $form = $this->createFormBuilder()
                ->add('nombre', TextType::class, array('label' => 'Nombre',
                    'attr' => array('maxlength' => 36),
                ))
                ->add('apellido', TextType::class, array('label' => 'Apellido',
                    'attr' => array('maxlength' => 36),
                ))
                ->add('area', TextType::class, array('label' => 'Área',
                    'attr' => array('maxlength' => 36),
                ))
                ->add('posicion', TextType::class, array('label' => 'Posición',
                    'attr' => array('maxlength' => 36),
                    'required' => false,))
                ->add('email', TextType::class, array('label' => 'Email',
                    'attr' => array('maxlength' => 36),
                ))
                ->add('telefono', TextType::class, array('label' => 'Teléfono',
                    'attr' => array('maxlength' => 15),
                ))
                ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_datos_contacto.html.twig', array(
            'datosContacto' => $form->createView(),
            'entities' => $entities,
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

}
