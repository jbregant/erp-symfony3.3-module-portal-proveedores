<?php 
 
namespace GYL\UsuarioBundle\EventListener; 
 
use FOS\UserBundle\FOSUserEvents; 
use FOS\UserBundle\Event\FormEvent; 
use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
 
/** 
 * Listener responsible to change the redirection at the end of the password change 
 */ 
class ChangePasswordListener implements EventSubscriberInterface { 
    private $router; 
 
    public function __construct(UrlGeneratorInterface $router) { 
        $this->router = $router; 
    } 
 
    public static function getSubscribedEvents() { 
        return [ 
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess', 
        ]; 
    } 
 
    public function onChangePasswordSuccess(FormEvent $event) { 
        $url = $this->router->generate('fos_user_security_logout'); 
        $event->setResponse(new RedirectResponse($url)); 
    } 
}