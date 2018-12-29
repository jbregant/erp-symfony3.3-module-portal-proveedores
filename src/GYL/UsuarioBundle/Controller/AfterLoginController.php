<?php

namespace GYL\UsuarioBundle\Controller;

use GYL\ProveedorBundle\Entity\ProveedorDatoContacto;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use GYL\UsuarioBundle\Entity\Usuario;
use GYL\ProveedorBundle\Entity\Visita;

/**
 * Login controller
 *
 * @Route("/afterlogin")
 */
class AfterLoginController extends Controller
{

    /**
     * Redireccionar usuario luego de loguearse
     *
     * @Route("/redirect", name="after_login_redirect")
     * @param Request $request
     *
     * @return Response
     */
    public function redirectAction(Request $request)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof Usuario) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $userManager = $this->get('fos_user.user_manager');

        $visita = New Visita();
        $visita->setFecha(new \DateTime);
        $visita->setUsuario($user);
        $em->persist($visita);
        $em->flush();

        /**
         * Creo una PreInscripción vacía
         * para todos los proveedores existentes en siga que no la posean
         */
        $proveedores = $this->container->get('app.repository.usuario')->getListaProveedores($user->getEmail());
        if (!empty($proveedores)) {
            foreach($proveedores as $proveedor){
                //Valido que efectivamente no la tenga:
                $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
                $existePdp = $repoProveedorDatoPersonal->findOneBy(['idProveedorAsoc' => $proveedor['idProveedor']]);
                if(empty($existePdp)){
                    $cuit = $proveedor['cuit'];
                    $cdi = $proveedor['cdi'];
                    $idProveedor = $proveedor['idProveedor'];
                    $extranjero = $proveedor['es_extranjero'];

                    //creo proveedor dato personal y seteo datos para el usuario nuevo
                    $proveedorDatoPersonal = new ProveedorDatoPersonal();

                    //checkeo si es extranjero para setear cuit o identificacion tributaria
                    if($extranjero){
                        $proveedorDatoPersonal->setNumeroIdTributaria($cdi);
                    } else {
                        $proveedorDatoPersonal->setCuit($cuit);
                    }

                    $proveedorDatoPersonal->setProveedor(true);
                    $proveedorDatoPersonal->setEmail($user->getEmail());
                    $proveedorDatoPersonal->setExtranjero($proveedor['es_extranjero']?true:false);
                    $proveedorDatoPersonal->setIdProveedorAsoc($idProveedor);

                    $user->addProveedorDatoPersonal($proveedorDatoPersonal); //Le seteo el Prov Dato Personal al user

                    $em->persist($proveedorDatoPersonal);
                    $em->flush();
                } elseif(!empty($existePdp)){
                    $existeAsoc = $this->container->get('app.repository.usuario')->buscarAsociacion($existePdp->getId(), $user->getId());
                    if (empty($existeAsoc)){
                        $this->container->get('app.repository.usuario')->crearAsociacion($existePdp->getId(), $user->getId());
                    }
                }
            }
            $userManager->updateUser($user);
        }

        /**
         * Si el email está como contacto de alguna preinscripcion,
         * esa preinscripción se asocia con el usuario
         */
        $contactosPortal = $em->getRepository(ProveedorDatoContacto::class)->findBy(['email' => $user->getEmail()]);
        if (!empty($contactosPortal)) {
            foreach($contactosPortal as $contactoPortal){
                //generar un usuario nuevo y asociarle el idDatoPersonal al que pertenece el prov_dato_contacto encontrado
                //seteo asociacion entre usuario nuevo y id dato personal
                $asocExistente = $datosProveedor = $this->container->get('app.repository.usuario')->buscarAsociacion($contactoPortal->getIdDatoPersonal()->getId(), $user->getId());

                if(!$asocExistente){
                    $user->addProveedorDatoPersonal($contactoPortal->getIdDatoPersonal());
                }

            }
            $userManager->updateUser($user);
        }
        $proveedorDatoPersonales = $this->getUser()->getProveedorDatoPersonal();

        /**
         * hardcodeo el indice hasta ver q hacer
         */

        $preinscripcionFlag = false;

        foreach ($proveedorDatoPersonales as $proveedorDatoPersonal) {
            if ($proveedorDatoPersonal && $proveedorDatoPersonal->esProveedor()) {
                $preinscripcionFlag = true;
            }
        }

        if ($preinscripcionFlag ){
            $redirectResponse = new RedirectResponse($this->generateUrl('cuenta_corriente_index'));
        } elseif (!$preinscripcionFlag) {
            $redirectResponse = new RedirectResponse($this->generateUrl('preinscripcion_formulario'));
        } else {
            $redirectResponse = new Response();
            $redirectResponse->setStatusCode(500);
        }

        return $redirectResponse;
    }

}
