<?php

namespace GYL\UsuarioBundle\Handler;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionIdleHandler {

    protected $session;
    protected $authorizationChecker;
    protected $securityToken;
    protected $router;
    protected $maxIdleTime;

    public function __construct(SessionInterface $session, TokenStorageInterface $securityToken, AuthorizationChecker $authorizationChecker, RouterInterface $router, $maxIdleTime = 0) {
        $this->session = $session;
        $this->securityToken = $securityToken;
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(GetResponseEvent $event) {

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if ($this->maxIdleTime > 0) {

            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();
            $isFullyAuthenticated = $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY');

            if ($lapse > $this->maxIdleTime && $isFullyAuthenticated) {

                $this->session->start();
                $this->securityToken->setToken(null);
                $this->session->getFlashBag()->add('timeout', 'Sesión finalizada por inactividad.');

                $request = $event->getRequest();

                if ($request->isXmlHttpRequest()){
                    $event->setResponse(new Response('', 403));
                } else {
                    $event->setResponse(new RedirectResponse($this->router->generate('fos_user_security_logout')));
                }
            }
        }
    }

}