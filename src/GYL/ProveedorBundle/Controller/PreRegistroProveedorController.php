<?php

namespace GYL\ProveedorBundle\Controller;

use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use GYL\ProveedorBundle\Entity\BackupUserData;
use GYL\ProveedorBundle\Entity\EstadoEvaluacion;
use GYL\ProveedorBundle\Entity\EstadoEvaluacionGerencia;
use GYL\ProveedorBundle\Entity\Gerencia;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use GYL\ProveedorBundle\Entity\TipoProveedor;
use GYL\ProveedorBundle\Entity\TipoTimeline;
use GYL\ProveedorBundle\Entity\UserXProveedorDatoPersonal;
use GYL\UsuarioBundle\Entity\Invitacion;
use GYL\UsuarioBundle\Entity\Usuario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


/**
 * PreRegistroProveedor controller.
 *
 * @Route("/preinscripcion")
 */
class PreRegistroProveedorController extends BaseController
{
    /**
     *
     * @Route("/registro", name="preinscripcion_registro")
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $this->render('ProveedorBundle::Preinscripcion/invitacion_pre_registro_proveedor.html.twig',
            array('csrf_token' => $csrfToken));

    }

    /**
     * Crear invitacion.
     *
     * @Route("/crear", name="preinscripcion_crear")
     * @Method("POST")
     */
    public function verificarAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        $recaptcha = $this->container->get('app.repository.usuario')->captchaverify($request->get('g-recaptcha-response'));

        if (!$recaptcha->success) {
            return new JsonResponse([
                'sts' => 1,
                'msg' => 'Error en código captcha.',
            ]);
        }

        $email                    = strtolower($request->request->get('email'));
        $extranjero               = $request->request->getBoolean('extranjero');
        $cuit                     = $request->request->get('cuit');
        $identificacionTributaria = $request->request->get('identificacion_tributaria');
        $website                  = strtolower($request->request->get('website'));
        //\Doctrine\Common\Util\Debug::dump($request->request);die;

        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $repo     = $em->getRepository(Usuario::class);
        $usuarios = $repo->findBy(['email' => $email]);
        $result   = count($usuarios);

        if ($result > 0) {
            return new JsonResponse([
                'sts' => 2,
                'msg' => 'Ya posee una cuenta asignada a este correo.',
            ]);
        }

        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $repo   = $em->getRepository(Invitacion::class);
        $invit  = $repo->findBy(['email' => $email, 'fechaBaja' => null]);
        $result = count($invit);

        if ($result > 0) {
            return new JsonResponse([
                'sts' => 3,
                'msg' => 'Ya tiene una invitación enviada, busque en la carpeta de SPAM',
            ]);
        }

        $repoProveedorDatoPersonal    = $em->getRepository(ProveedorDatoPersonal::class);
        $cuitExistenteSIGA = $repoProveedorDatoPersonal->findCuitInSIGA($cuit);
        $cuitExistentePortal = $repoProveedorDatoPersonal->findCuitInPortal($cuit);

        if (empty($extranjero) && (!empty($cuitExistentePortal) || !empty($cuitExistenteSIGA))) {
            return new JsonResponse([
                'sts' => 4,
                'msg' => 'CUIT existente en el sistema.',
            ]);
        }


        //Reflejo 10/08/2018 consulta en el padron mediante webservice, con certificados pertenecientes a GyL.
        //Revisar parameters.yml y WSAAService.php asociados.
//        $authService = 'ws_sr_padron_a4';
//        $entorno = in_array($this->container->getParameter('kernel.environment'), ['dev'], true)?'dev':'prod';
//
//        $url = $this->container->getParameter("ws_sr_padron_a4_{$entorno}.URL");
//        $cuitRepresentada = $this->container->getParameter("ws_sr_padron_a4_{$entorno}.cuitRepresentada");
//
//        // Obtencion del WSAA.
//        $WSAA = new \GYL\ProveedorBundle\Service\WSAAService($this->container, $authService);
//        $SSO = $WSAA->getAuth();
//
//        //Inicializacion del cliente.
//        $client = new \Soapclient($url);
//
//        //Si el idPersona(cuit) no esta presente en las bases del organismo no se devuelve como error, sino como excepcion.
//        $result = null;
//        try
//        {
//            $result = $client->getPersona(array(
//                'token' => $SSO['Token'],
//                'sign' => $SSO['Sign'],
//                'idPersona' => $cuit,
//                'cuitRepresentada' => $cuitRepresentada //GyL
//            ));
//        }
//        catch(\Exception $e)
//        {
//            $error = $e->getMessage();
//        }
        //Si la variable error no esta seteada, quiere decir que la comprobacion de la existencia de la persona fue existosa.
        //Interpretar los resultados recibidos por el webservice https://www.afip.gob.ar/ws/ws_sr_padron_a4/manual_ws_sr_padron_a4_v1.1.pdf

        $repoInvitacion = $em->getRepository(Invitacion::class);

        $invitacion = new Invitacion();
        $invitacion->setEmail($email);

        $proveedorDatoPersonal = new ProveedorDatoPersonal();

        if ($extranjero) {
            $proveedorDatoPersonal->setNumeroIdTributaria($identificacionTributaria);
        } else {
            $proveedorDatoPersonal->setCuit($cuit);
        }

        $proveedorDatoPersonal->setProveedor(false);
        $proveedorDatoPersonal->setEmail($email);
        $proveedorDatoPersonal->setExtranjero($extranjero);
        $proveedorDatoPersonal->setDireccionWeb($website);

        $from   = $this->container->getParameter('mailer_user');
        $sender = $this->container->getParameter('mailer_sender_name');

        $message = (new \Swift_Message('ADIF: Portal de Proveedores - Email de invitación'))
            ->setFrom(array($from => $sender))
            ->setTo($email)
            ->setBody(
                $this->renderView('ProveedorBundle::EmailFormPreInscripcion.html.twig',
                    [
                        'code'   => $invitacion->getCodigo(),
                        'nombre' => strtolower($email),
                    ]
                ),
                "text/html"
            );

        $this->get('mailer')->send($message);

        $em->getFilters()->disable('softdeleteable');
        $invit  = $repoInvitacion->findBy(['email' => $email, 'caducada' => true]);
        $result = count($invit);
        $em->getFilters()->enable('softdeleteable');

        $invitacion->setEnviado(true);
        $em->persist($invitacion);
        $em->persist($proveedorDatoPersonal);
        $em->flush();

        if ($result > 0) {
            return new JsonResponse([
                'sts' => 5,
                'msg' => 'Gracias por contactarnos, su invitación previa caducó, en breve recibirá un email de invitación para definir su contraseña.',
                'url' => $this->generateUrl('fos_user_security_login'),
            ]);
        } else {
            return new JsonResponse([
                'sts' => 5,
                'msg' => 'Gracias por contactarnos, en breve recibirá un email de invitación para definir su contraseña.',
                'url' => $this->generateUrl('fos_user_security_login'),
            ]);
        }

    }

    /**
     * Agregar Proveedor.
     *
     * @Route("/agregarproveedor", name="agregar_proveedor")
     * @Method("POST")
     */
    public function agregarProveedorAction(Request $request)
    {
        try {
            $email                    = strtolower($request->request->get('email'));
            $extranjero               = $request->request->getBoolean('extranjero');
            $cuit                     = $request->request->get('cuit');
            $identificacionTributaria = $request->request->get('identificacion_tributaria');
            $website                  = strtolower($request->request->get('website'));

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repo    = $em->getRepository(ProveedorDatoPersonal::class);
            $cuitExistenteSIGA = $repo->findCuitInSIGA($cuit);


            if (!empty($cuit))
                $cuitExistente = $repo->findOneBy(['cuit' => $cuit]);
            if (!empty($identificacionTributaria))
                $identificacionTributariaExistente = $repo->findOneBy(['numeroIdTributaria' => $identificacionTributaria]);

            if ((isset($cuitExistente) && $cuitExistente instanceof ProveedorDatoPersonal) || !empty($cuitExistenteSIGA) ){
                return new JsonResponse([
                    'sts' => 202,
                    'msg' => 'Cuit existente',
                ]);
            }

            if (isset($identificacionTributariaExistente) && $identificacionTributariaExistente instanceof ProveedorDatoPersonal){
                return new JsonResponse([
                    'sts' => 202,
                    'msg' => 'Nro de Identificacion Tributaria existente',
                ]);
            }

            //Reflejo 10/08/2018 consulta en el padron mediante webservice, con certificados pertenecientes a GyL.
            //Revisar parameters.yml y WSAAService.php asociados.
//            $authService = 'ws_sr_padron_a4';
//            $entorno = in_array($this->container->getParameter('kernel.environment'), ['dev'], true)?'dev':'prod';
//
//            $url = $this->container->getParameter("ws_sr_padron_a4_{$entorno}.URL");
//            $cuitRepresentada = $this->container->getParameter("ws_sr_padron_a4_{$entorno}.cuitRepresentada");
//
//            // Obtencion del WSAA.
//            $WSAA = new \GYL\ProveedorBundle\Service\WSAAService($this->container, $authService);
//            $SSO = $WSAA->getAuth();
//
//            //Inicializacion del cliente.
//            $client = new \Soapclient($url);
//
//            //Si el idPersona(cuit) no esta presente en las bases del organismo no se devuelve como error, sino como excepcion.
//            $result = null;
//            try
//            {
//                $result = $client->getPersona(array(
//                    'token' => $SSO['Token'],
//                    'sign' => $SSO['Sign'],
//                    'idPersona' => $cuit,
//                    'cuitRepresentada' => $cuitRepresentada //GyL
//                ));
//            }
//            catch(\Exception $e)
//            {
//                $error = $e->getMessage();
//            }
            //Si la variable error no esta seteada, quiere decir que la comprobacion de la existencia de la persona fue existosa.
            //Interpretar los resultados recibidos por el webservice https://www.afip.gob.ar/ws/ws_sr_padron_a4/manual_ws_sr_padron_a4_v1.1.pdf

            $proveedorDatoPersonal = new ProveedorDatoPersonal();

            if ($extranjero) {
                $proveedorDatoPersonal->setNumeroIdTributaria($identificacionTributaria);
            } else {
                $proveedorDatoPersonal->setCuit($cuit);
            }

            $proveedorDatoPersonal->setProveedor(false);
            $proveedorDatoPersonal->setEmail($this->getUser()->getEmail());
            $proveedorDatoPersonal->setExtranjero($extranjero);
            $proveedorDatoPersonal->setDireccionWeb($website);
            $proveedorDatoPersonal->addIdUsuario($this->getUser());

            $em->persist($proveedorDatoPersonal);
            $em->flush();

            $response = 200;
            $msg = 'Agregado Correctamente';
            $idDatoPersonal = $proveedorDatoPersonal->getId();

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $idDatoPersonal = '';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $idDatoPersonal = '';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
            'idDatoPersonal' => $idDatoPersonal
        ]);
    }

    /**
     *
     * @Route("/formulario/{idDatoPersonal}/{unLock}", name="preinscripcion_formulario_id_dato_personal_unlock")
     * @Route("/formulario/{idDatoPersonal}", name="preinscripcion_formulario_id_dato_personal")
     * @Route("/formulario/", name="preinscripcion_formulario")
     * @Method("GET|POST")
     *
     * @return Response
     */
    public function formularioAction(Request $request, $idDatoPersonal = null, $unLock = null)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }

        $em                    = $this->getDoctrine()->getManager('adif_proveedores');
        $repoUsuario           = $em->getRepository(Usuario::class)->findOneBy(['id' => $user->getId()]);
        $repoProvDatoPersonal  = $em->getRepository(ProveedorDatoPersonal::class);
        $repoTipoProveedor     = $em->getRepository(TipoProveedor::class);

        //buscamos por el idDatoPersonal recibido o tomamos el primer registro existente
        if ($idDatoPersonal){
            $notAllowedFlag = false;
            $datosPersonalesUser = $repoUsuario->getProveedorDatoPersonal();

            foreach ($datosPersonalesUser as $item) {
                if($item->getId() == $idDatoPersonal)
                    $notAllowedFlag = true;
            }

            if (!$notAllowedFlag){
                throw new AccessDeniedException('No tienes acceso a esta sección');
            }

            $proveedorDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $idDatoPersonal]);
        } else {
            $datosPersonalesUser = $repoUsuario->getProveedorDatoPersonal();
            $proveedorDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $datosPersonalesUser[0]->getId()]);
        }

        $proveedorEvaluacion   = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => ($idDatoPersonal) ? $idDatoPersonal : $datosPersonalesUser[0]->getId()]);

        $comboProveedores = $repoUsuario->getProveedorDatoPersonal();


        if ($proveedorDatoPersonal->getTipoProveedor())
            $userTipoProveedor    = $proveedorDatoPersonal->getTipoProveedor()->getId();

        if ($proveedorEvaluacion) {
            $estadoEvaluacionGral = $proveedorEvaluacion->getEstadoEvaluacion()->getId();
            $estadoEvaluacionGalo = $proveedorEvaluacion->getEstadoEvaluacionGalo()->getId();
            $estadoEvaluacionGafFinanzas = $proveedorEvaluacion->getEstadoEvaluacionGafFinanzas()->getId();
            $estadoEvaluacionGafImpuestos = $proveedorEvaluacion->getEstadoEvaluacionGafImpuestos()->getId();
            $estadoEvaluacionGcshm = $proveedorEvaluacion->getEstadoEvaluacionGcshm()->getId();
        } else {
            $estadoEvaluacionGral = null;
            $estadoEvaluacionGalo = null;
            $estadoEvaluacionGafFinanzas = null;
            $estadoEvaluacionGafImpuestos = null;
            $estadoEvaluacionGcshm = null;
        }

        if ($estadoEvaluacionGalo == 3 || $estadoEvaluacionGafFinanzas == 3 || $estadoEvaluacionGafImpuestos == 3 || $estadoEvaluacionGcshm == 3)
            $estadoEvaluacionGral = 3;

        $radioButtonState = false;

        //Reflejo 06/09: Se pidio explicitamente que el estado 4 (OBSERVADO) este con disabled
        ($estadoEvaluacionGral != null) ? $radioButtonState = "'disabled'" : false;
        //($estadoEvaluacionGral && $estadoEvaluacionGral != 4) ? $radioButtonState = "'disabled'" : false;

        $idTipoProveedor = $proveedorDatoPersonal->getTipoProveedor();

        if (isset($idTipoProveedor)) {
            $tipoProveedorEntity = $repoTipoProveedor->findOneBy(['id' => $idTipoProveedor->getId()]);

            $cbxFlagPersonaFisica              = $tipoProveedorEntity->getDenominacion() == 'Persona física';
            $cbxFlagPersonaJuridica            = $tipoProveedorEntity->getDenominacion() == 'Persona jurídica';
            $cbxFlagPersonaContratos           = $tipoProveedorEntity->getDenominacion() == 'Contratos de Colaboración Empresaria';
            $cbxFlagPersonaJuridicaNoResidente = $tipoProveedorEntity->getDenominacion() == 'Persona física extranjera no residente en el país';
            $cbxFlagPersonaJuridicaExtranjera  = $tipoProveedorEntity->getDenominacion() == 'Persona jurídica extranjera';

        } else {
            $cbxFlagPersonaFisica              = null;
            $cbxFlagPersonaJuridica            = null;
            $cbxFlagPersonaContratos           = null;
            $cbxFlagPersonaJuridicaNoResidente = null;
            $cbxFlagPersonaJuridicaExtranjera  = null;
        }


        // para que estos dos paneles sean opcionales
        //UPDATE: a pedido de jorge estos paneles vuelven a ser requeridos y deben guardarse por el usuario
//        $this->guardarTimeline('timeline_representantes_apoderados', 'completo', 1);
//        $this->guardarTimeline('timeline_gcshm', 'completo', 1);

        $tipoProveedor = $this->createFormBuilder()
            ->add('persona_fisica', RadioType::class, array('label' => '', 'data' => $cbxFlagPersonaFisica, 'value' => 'Persona física', 'disabled' => $radioButtonState))
            ->add('persona_juridica', RadioType::class, array('label' => '', 'data' => $cbxFlagPersonaJuridica, 'value' => 'Persona jurídica', 'disabled' => $radioButtonState))
            ->add('persona_contratos', RadioType::class, array('label' => '', 'data' => $cbxFlagPersonaContratos, 'value' => 'Contratos de Colaboración Empresaria', 'disabled' => $radioButtonState))
            ->getForm();

        $tipoProveedorExtranjero = $this->createFormBuilder()
            ->add('persona_fisica_extranjera_no_residente_del_pais', RadioType::class, array('label' => '', 'data' => $cbxFlagPersonaJuridicaNoResidente, 'value' => 'Persona física extranjera no residente en el país', 'disabled' => $radioButtonState))
            ->add('persona_juridica_extranjera', RadioType::class, array('label' => '', 'data' => $cbxFlagPersonaJuridicaExtranjera, 'value' => 'Persona jurídica extranjera', 'disabled' => $radioButtonState))
            ->getForm();



        $proveedores = $this->container->get('app.repository.usuario')->getListaProveedores($this->getUser()->getUsername());

        if (!$proveedorDatoPersonal->getExtranjero()) {
            return $this->render('ProveedorBundle::Preinscripcion/registro-sabana.html.twig', array(
                'mostrarBtnModificar'   => ($estadoEvaluacionGral == 2) ? true : false,
                'mostrarBtnCte'         => (!empty($proveedores)),
                'unlockForm'            => ($unLock == null) ? false : true,
                'user'                  => $user,
                'idProvDatoPersonal'    => $proveedorDatoPersonal->getId(),
                'proveedorDatoPersonal' => $proveedorDatoPersonal,
                'comboProveedores'      => $comboProveedores,
                'tipoProveedor'         => $tipoProveedor->createView(),
                'estadoEvaluacionGral'  => $estadoEvaluacionGral,
                'ddjjFilename' => (@$userTipoProveedor == 1) ? 'FO-DDJJ202-17-PF.pdf' : 'FO-DDJJ202-17-PJ.pdf'
            ));
        } else {
            return $this->render('ProveedorBundle::Preinscripcion/registro-sabana-extranjero.html.twig', array(
                'mostrarBtnModificar'     => ($estadoEvaluacionGral == 2) ? true : false,
                'mostrarBtnCte'           => (!empty($proveedores)),
                'unlockForm'              => ($unLock == null) ? false : true,
                'user'                    => $user,
                'idProvDatoPersonal'      => $proveedorDatoPersonal->getId(),
                'proveedorDatoPersonal'   => $proveedorDatoPersonal,
                'comboProveedores'        => $comboProveedores,
                'tipoProveedorExtranjero' => $tipoProveedorExtranjero->createView(),
                'estadoEvaluacionGral'    => $estadoEvaluacionGral,
            ));
        }
    }

    /**
     *
     * Genera la pre inscripcion de interesado como proveedor
     *
     * @Route("/generar-preinscripcion", name="preinscripcion_alta")
     * @Method("GET|POST")
     */
    public function generarPreinscripcion(Request $request)
    {
        $em  = $this->getDoctrine()->getManager('adif_proveedores');
        $uid = $this->getUser()->getId();

        if (isset($uid) && $uid) {

            $data = $request->request->all();
            $em             = $this->getDoctrine()->getManager('adif_proveedores');
            $preInscripcion = new ProveedorEvaluacion;

            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['idDatoPersonal']]);

            $gerenciaGalo         = $em->getRepository(Gerencia::class)->findBy(['denominacion' => 'GALO']);
            $gerenciaGcshm        = $em->getRepository(Gerencia::class)->findBy(['denominacion' => 'GCSHM']);
            $gerenciaGafFinanzas  = $em->getRepository(Gerencia::class)->findBy(['denominacion' => 'GAF-Finanzas']);
            $gerenciaGafImpuestos = $em->getRepository(Gerencia::class)->findBy(['denominacion' => 'GAF-Impuestos']);

            $estadoEvaluacionGerencia = $em->getRepository(EstadoEvaluacionGerencia::class)->findBy(['denominacion' => 'Pendiente']);
            $estadoEvaluacion         = $em->getRepository(EstadoEvaluacion::class)->findBy(['denominacion' => 'Pendiente']);

            $preInscripcion->setIdUsuario($this->getUser());
            $preInscripcion->setGerenciaGalo($gerenciaGalo[0]);
            $preInscripcion->setGerenciaGcshm($gerenciaGcshm[0]);
            $preInscripcion->setGerenciaGafFinanzas($gerenciaGafFinanzas[0]);
            $preInscripcion->setGerenciaGafImpuestos($gerenciaGafImpuestos[0]);
            $preInscripcion->setEstadoEvaluacionGalo($estadoEvaluacionGerencia[0]);
            $preInscripcion->setEstadoEvaluacionGcshm($estadoEvaluacionGerencia[0]);
            $preInscripcion->setEstadoEvaluacionGafFinanzas($estadoEvaluacionGerencia[0]);
            $preInscripcion->setEstadoEvaluacionGafImpuestos($estadoEvaluacionGerencia[0]);
            $preInscripcion->setEstadoEvaluacion($estadoEvaluacion[0]);
            $preInscripcion->setIdDatoPersonal($provDatoPersonal);

            $em->persist($preInscripcion);
            $em->flush();

            $response = array('result' => 'OK');

        } else {
            $response = array('result' => 'NOK', 'message' => 'Error. No se pudo generar la pre-inscripcion');
        }

        return new JsonResponse($response);
    }

    /**
     *
     * Actualiza la pre inscripcion de interesado como proveedor
     *
     * @Route("/actualiza-preinscripcion", name="preinscripcion_actualiza")
     * @Method("GET|POST")
     */
    public function actualizaPreinscripcion(Request $request)
    {
        $session = $request->getSession();

        $idDatoPersonal = $request->request->get('idDatoPersonal');

        $panelesActualizados = [];

        if(!empty($request->request->get('panelesActualizados'))) {

            foreach (json_decode($request->request->get('panelesActualizados')) as $item) {
                $panelesActualizados[] = $item->panel;
            }
        }

        $em                  = $this->getDoctrine()->getManager('adif_proveedores');
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idDatoPersonal]);
        $estadoEvaluacion    = $em->getRepository(EstadoEvaluacion::class)->findOneBy(['denominacion' => 'Pendiente']);
        $estadoEvaluacionGerencia = $em->getRepository(EstadoEvaluacionGerencia::class)->findOneBy(['denominacion' => 'Pendiente']);


        foreach($panelesActualizados as $panel) {
            $tipoTimelineId = $em->getRepository(TipoTimeline::class)->findOneBy(['denominacion' => $panel])->getId();
            $idGerencia = $em->getRepository(TipoTimeline::class)->findOneBy(['denominacion' => $panel])->getIdGerencia();

            //setea la gerencia en pendiente
            if($proveedorEvaluacion){
                switch ($idGerencia){
                    case '1':
                        $proveedorEvaluacion->setEstadoEvaluacionGalo($estadoEvaluacionGerencia);
                        break;
                    case '2':
                        $proveedorEvaluacion->setEstadoEvaluacionGafFinanzas($estadoEvaluacionGerencia);
                        break;
                    case '3':
                        $proveedorEvaluacion->setEstadoEvaluacionGafImpuestos($estadoEvaluacionGerencia);
                        break;
                    case '4':
                        $proveedorEvaluacion->setEstadoEvaluacionGcshm($estadoEvaluacionGerencia);
                        break;
                    default:
                        break;
                }
            }

            //Seteo la observación en inactiva.
            $em->getRepository(ObservacionEvaluacion::class)->setInactivoByProveedorTimeline($proveedorEvaluacion->getId(), $tipoTimelineId);
        }

        if ($proveedorEvaluacion) {

            if ($proveedorEvaluacion->getEstadoEvaluacionGalo()->getId() == 4){
                $proveedorEvaluacion->setEstadoEvaluacionGalo($estadoEvaluacionGerencia);
            }

            if ($proveedorEvaluacion->getEstadoEvaluacionGcshm()->getId() == 4){
                $proveedorEvaluacion->setEstadoEvaluacionGcshm($estadoEvaluacionGerencia);
            }

            if ($proveedorEvaluacion->getEstadoEvaluacionGafFinanzas()->getId() == 4){
                $proveedorEvaluacion->setEstadoEvaluacionGafFinanzas($estadoEvaluacionGerencia);
            }

            if ($proveedorEvaluacion->getEstadoEvaluacionGafImpuestos()->getId() == 4){
                $proveedorEvaluacion->setEstadoEvaluacionGafImpuestos($estadoEvaluacionGerencia);
            }

            $proveedorEvaluacion->setEstadoEvaluacion($estadoEvaluacion);

            $em->persist($proveedorEvaluacion);
            $em->flush();
            $response = array('result' => 'OK');

        } else {
            $response = array('result' => 'NOK', 'message' => 'Error. No se pudo generar la pre-inscripcion');
        }

        return new JsonResponse($response);
    }

    /**
     *
     * Cancela y rollbackea los datos tras solicitar modificacion
     *
     * @Route("/cancelar-modificacion-preinscripcion", name="preinscripcion_cancelar")
     * @Method("POST")
     */
    public function cancelarModificacionPreinscripcion(Request $request)
    {
        try{
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $em->getRepository(ProveedorEvaluacion::class)->rollBackRowBackupxIdDatoPersonal($data['idDatoPersonal']);

        }catch (Exception $e) {
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
            return new JsonResponse(['result' => 'NOK']);
        } catch (\Throwable $e) {
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
            return new JsonResponse(['result' => 'NOK']);
        }
        return new JsonResponse(['result' => 'OK']);
    }

    /**
     * Backupea la info de la preinscripcion para poder rollbackear
     *
     * @Route("/backup-datos-preinscripcion", name="preinscripcion_backup")
     * @Method("POST")
     */
    public function backupearDatosPreinscripcion(Request $request){

        try{
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $backupMaker = $em->getRepository(ProveedorEvaluacion::class)->setRowBackupxIdDatoPersonal($data['idDatoPersonal']);

        }catch (Exception $e) {
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
            return new JsonResponse(['result' => 'NOK']);
        } catch (\Throwable $e) {
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
            return new JsonResponse(['result' => 'NOK']);
        }
        return new JsonResponse(['result' => 'OK']);
    }
}
