<?php

namespace GYL\UsuarioBundle\Controller;

use GYL\ProveedorBundle\Entity\ProveedorDatoContacto;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\UserXProveedorDatoPersonal;
use GYL\UsuarioBundle\Entity\Invitacion;
use GYL\UsuarioBundle\Entity\Usuario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invitacion controller.
 *
 * @Route("/invitacion")
 */
class InvitacionController extends Controller
{
    /**
     * Crear invitacion.
     *
     * @Route("/crear", name="crear_invitacion")
     * @Method("POST")
     */
    public function crearAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        $recaptcha = $this->container->get('app.repository.usuario')->captchaverify($request->get('reCaptcha'));

        if (!$recaptcha->success) {
            return new JsonResponse([
                'sts' => 1,
                'msg' => 'Error en código captcha.',
            ]);
        }

        $email = strtolower($request->request->get('email'));
        $em    = $this->getDoctrine()->getManager('adif_proveedores');

        $repo     = $em->getRepository(Usuario::class);
        $usuarios = $repo->findBy(['email' => $email]);
        $result   = count($usuarios);
        if ($result > 0) {
            return new JsonResponse([
                'sts' => 2,
                'msg' => 'Ya posee una cuenta asignada a este correo.',
            ]);
        }

        $repo   = $em->getRepository(Invitacion::class);
        $invit  = $repo->findBy(['email' => $email, 'fechaBaja' => null]);
        $result = count($invit);
        if ($result > 0) {
            return new JsonResponse([
                'sts' => 3,
                'msg' => 'Ya tiene una invitación enviada, busque en la carpeta de SPAM',
            ]);
        }

        /**
         * Para crear una invitación valida que sea contacto de un proveedor o
         * de una preinscripción
         */
        $datosProveedor = $this->container->get('app.repository.usuario')->getListaProveedores($email);
        $contactosPortal = $em->getRepository(ProveedorDatoContacto::class)->findOneBy(['email' => $email]);
        $result         = count($datosProveedor);
        if ( $result > 0 || !empty($contactosPortal) ) {
            $invitacion = new Invitacion();
            $invitacion->setEmail($email);
            $nombre = (isset($datosProveedor[0]['nombre']) ? $datosProveedor[0]['nombre'] : $contactosPortal->getNombre());

            //Envio de email
            $message = (new \Swift_Message('ADIF: Portal de Proveedores - Email de invitación'))
                ->setFrom('no_responder@adifse.com.ar')
                ->setTo($email)
                ->setBody(
                    $this->renderView('GYLUsuarioBundle::EmailInvitation.html.twig',
                        [
                            'code'   => $invitacion->getCodigo(),
                            'nombre' => strtolower($nombre),
                        ]
                    ),
                    "text/html"
                );
            $this->get('mailer')->send($message);
            // Fin envio de email

            $em->getFilters()->disable('softdeleteable');
            $invit  = $repo->findBy(['email' => $email, 'caducada' => true]);
            $result = count($invit);
            $em->getFilters()->enable('softdeleteable');

            $invitacion->setEnviado(true);
            $em->persist($invitacion);
            $em->flush();

            if ($result > 0) {
                return new JsonResponse([
                    'sts' => 4,
                    'msg' => 'Gracias por contactarnos, su invitación previa caducó, en breve recibirá un email de invitación para definir su contraseña.',
                ]);
            } else {
                return new JsonResponse([
                    'sts' => 5,
                    'msg' => 'Gracias por contactarnos, en breve recibirá un email de invitación para definir su contraseña.',
                ]);
            }
        }

        return new JsonResponse([
            'sts' => 0,
            'msg' => 'No se pudo procesar su solicitud, por favor envíenos un correo haciendo click <a href="mailto:soporte.portal@adifse.com.ar">aquí</a>',
        ]);
    }

    /**
     * Verifica la invitacion enviada por correo.
     *
     * @Route("/verificar/{codigo}", name="verificar_invitacion")
     *
     */
    public function verificarAction($codigo)
    {
        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $repoInvitacion = $em->getRepository(Invitacion::class);
        $invitacion     = $repoInvitacion->findOneBy(['codigo' => $codigo]);
        $repoUsuario    = $em->getRepository(Usuario::class);

        if (count($invitacion) == 0) {
            $response = new Response();
            $response->setStatusCode(500);
            return $response;
        }

        return $response = $this->forward('GYLUsuarioBundle:Invitacion:Registrar', array(
            'invitacion' => $invitacion,
        ));

    }

    /**
     * Registra al usuario a partir de una invitacion
     *
     * @Route("/registro/", name="registro_invitacion")
     * @Method("POST")
     *
     */
    public function registrarAction($invitacion, Request $request)
    {

        $usuario = new Usuario();

        $form = $this->createFormBuilder($usuario, array(
            'validation_groups' => array('myChangePassword'),
            'invalid_message'   => 'fos_user.password.validate',
        ))->add('plainPassword', RepeatedType::class, [
            'error_bubbling'  => true,
            'type'            => PasswordType::class,
            'required'        => true,
            'options'         => array(
                'translation_domain' => 'FOSUserBundle'),
            'first_options'   => array(
                'label' => 'form.new_password'),
            'second_options'  => array(
                'label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoUsuario = $em->getRepository(Usuario::class);
            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);

            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $email = $invitacion->getEmail();

            //creo usuario nuevo
            $user->setEmail($invitacion->getEmail($email));
            $user->setEmailCanonical($email);
            $user->setEnabled(1);
            $user->setPlainPassword($request->get('form')['plainPassword']['first']);

            /**
             * Si el email está como contacto de alguna preinscripcion,
             * esa preinscripción se asocia con el usuario
             * --> Se hace acá y en AfterLoginController para los usuarios que ya estaban creados
             * y posteriormente se agregan como contacto de una preinscripción
             */
            $contactosPortal = $em->getRepository(ProveedorDatoContacto::class)->findBy(['email' => $email]);
            if (!empty($contactosPortal)) {
                foreach($contactosPortal as $contactoPortal){
                    //generar un usuario nuevo y asociarle el idDatoPersonal al que pertenece el prov_dato_contacto encontrado
                    //seteo asociacion entre usuario nuevo y id dato personal
                    $user->addProveedorDatoPersonal($contactoPortal->getIdDatoPersonal());
                }
            }

            //Relaciona la preinscripción creada anteriormente con el usuario
            $proveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class)->findOneBy(['email' => $email]);
            if ($proveedorDatoPersonal){
                $user->addProveedorDatoPersonal($proveedorDatoPersonal);
            }

            $userManager->updateUser($user);
            $em->remove($invitacion);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'El usuario fue creado con éxito.');

            return new RedirectResponse($this->generateUrl('fos_user_security_login'));

        } else {
            $request->attributes->set('form-error', true);
        }
        return $this->render('GYLUsuarioBundle:Invitacion:invitacionPass.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @Route("/passwordchange/{codigo}", name="password_change")
     * @param Request $request
     *
     * @return Response
     */
    public function registrarProveedorAction($codigo, Request $request)
    {
        $em             = $this->getDoctrine()->getManager('adif_proveedores');
        $repoInvitacion = $em->getRepository(Invitacion::class);
        $invitacion     = $repoInvitacion->findOneBy(['codigo' => $codigo]);

        if (count($invitacion) == 0) {
            return $this->render('GYLUsuarioBundle:Invitacion:errorInvitacion.html.twig');
        }

        $usuario = new Usuario();

        $form = $this->createFormBuilder($usuario, array(
            'validation_groups' => array('myChangePassword'),
            'invalid_message'   => 'fos_user.password.validate',
        ))->add('plainPassword', RepeatedType::class, [
            'error_bubbling'  => true,
            'type'            => PasswordType::class,
            'required'        => true,
            'options'         => array(
                'translation_domain' => 'FOSUserBundle'),
            'first_options'   => array(
                'label' => 'form.new_password'),
            'second_options'  => array(
                'label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userManager = $this->get('fos_user.user_manager');
            $user        = $userManager->createUser();
            $email       = $invitacion->getEmail();
            $user->setEmail($invitacion->getEmail($email));
            $user->setEmailCanonical($email);
            $user->setEnabled(1);
            $user->setPlainPassword($request->get('form')['plainPassword']['first']);
            $userManager->updateUser($user);

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $em->remove($invitacion);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'El usuario fue creado con éxito.');

            return new RedirectResponse($this->generateUrl('fos_user_security_login'));

        } else {
            $request->attributes->set('form-error', true);
        }

        return $this->render('GYLUsuarioBundle:Invitacion:invitacionPass.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

}
