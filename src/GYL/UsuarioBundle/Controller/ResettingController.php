<?php

namespace GYL\UsuarioBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use GYL\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Administrar el reseteo de contraseña.
 *
 */
class ResettingController extends BaseController
{
    /**
     * Request reset user password: show form.
     */
    public function requestAction()
    {
        return $this->render('@FOSUser/Resetting/request.html.twig');
    }

    /**
     * Request reset user password: submit form and send email.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sendEmailAction(Request $request)
    {

        $recaptcha = $this->container->get('app.repository.usuario')->captchaverify($request->get('g-recaptcha-response'));
        $username = $request->request->get('username');
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        //validacion captcha
        if (!$recaptcha->success) {
            $msg = 'Código captcha incompleto o incorrecto';
            $request->getSession()->getFlashBag()->add('error', $msg);
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        //validacion usuario existente
        if (!$user instanceof Usuario) {
            $msg = 'El correo electrónico ingresado no pertenece a un usuario';
            $request->getSession()->getFlashBag()->add('error', $msg);
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        /** @var $user UserInterface */

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

        if (null !== $user && !$user->isPasswordRequestNonExpired($ttl)) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            /* Dispatch confirm event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            //Envio de email
            $em             = $this->getDoctrine()->getManager('adif_proveedores');
            $datosProveedor = $em->getRepository('ProveedorBundle:ProveedorDatoPersonal')->findBy(['idUsuario' => $user->getId()]);
            $url            = $this->generateUrl('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
            $message        = (new \Swift_Message('ADIF: Portal de Proveedores - Recuperar contraseña'))
                ->setFrom('no_responder@adifse.com.ar')
                ->setTo($username)
                ->setBody(
                    $this->renderView('GYLUsuarioBundle:Resetting:email.html.twig',
                        [
                            'username'        => $username,
                            'nombre'          => (isset($datosProveedor[0]))?$datosProveedor[0]->getNombre():"",
                            'confirmationUrl' => $url,
                        ]
                    ),
                    "text/html"
                );
            $this->get('mailer')->send($message);
            // Fin envio de email

            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('fos_user.user_manager')->updateUser($user);

            /* Dispatch completed event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return new RedirectResponse($this->generateUrl('fos_user_resetting_check_email', array('username' => $username)));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        $em          = $this->getDoctrine()->getManager('adif_proveedores');
        $repoUsuario = $em->getRepository(Usuario::class);
        $usuario     = $repoUsuario->findOneBy(['username' => $username]);

        if (count($usuario) == 0) {
            // the user does not come from the sendEmail action
            $request->getSession()->getFlashBag()->add('info', 'Se ha producido un error en el sistema, por favor reporte el problema detallando la situación en el siguiente <a href="mailto:soporte.portal@adifse.com.ar">link</a>');
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        return $this->render('@FOSUser/Resetting/check_email.html.twig', array(
            'tokenLifetime' => ceil($this->container->getParameter('fos_user.resetting.retry_ttl') / 3600),
        ));
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            $request->getSession()->getFlashBag()->add('info', 'La contraseña se ha cambiado con éxito.');

            return new RedirectResponse($this->generateUrl('fos_user_security_login'));

        } else {
            $errors = $form->getErrors(true);
        }

        return $this->render('@FOSUser/Resetting/reset.html.twig', array(
            'token'  => $token,
            'form'   => $form->createView(),
            'errors' => $errors,
        ));
    }
}
