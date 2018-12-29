<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoBancario;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Dato Bancario controller.
 *
 * @Route("/")
 */
class DatoBancarioController extends BaseController
{
    /**
     * Agregar contacto adicional.
     *
     * @Route("/formulariopreinscripcion/agregardatobancario", name="agregar_dato_bancario")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarDatoBancarioAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoDatos = $em->getRepository(ProveedorDatoBancario::class);
            $datosBancarios = $repoDatos->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $cbuExistenteSIGA = $repoProvDatoPersonal->findCBUInSIGA($data['form']['cbu_datos_bancario'], $data['form']['id_dato_personal']);
            $cbuExistentePortal = $repoProvDatoPersonal->findCBUInPortal($data['form']['cbu_datos_bancario'], $data['form']['id_dato_personal']);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if (!empty($cbuExistenteSIGA)){
                return new JsonResponse([
                    'sts' => 201,
                    'msg' => 'CBU Existente en nuestra base de datos.',
                ]);
            }

            if (!empty($cbuExistentePortal)){
                return new JsonResponse([
                    'sts' => 201,
                    'msg' => 'CBU Existente en nuestra base de datos.',
                ]);
            }

            if ($datosBancarios instanceof ProveedorDatoBancario) {

                $datosBancarios->setIdEntidadBancaria($data['form']['entidad_bancaria']);
                $datosBancarios->setSucursalBancaria($data['form']['sucursal']);
                $datosBancarios->setNumeroSucursal($data['form']['numero_sucursal']);
                $datosBancarios->setCbu($data['form']['cbu_datos_bancario']);
                $datosBancarios->setNumeroCuenta($data['form']['numero_cuenta_dato_bancario']);
                $datosBancarios->setIdUsuario($this->getUser());
                $datosBancarios->setCuentaLocal(1);

                $em->merge($datosBancarios);
                $em->flush();


            } else {
                $pdb = New ProveedorDatoBancario();
                $pdb->setIdEntidadBancaria($data['form']['entidad_bancaria']);
                $pdb->setSucursalBancaria($data['form']['sucursal']);
                $pdb->setNumeroSucursal($data['form']['numero_sucursal']);
                $pdb->setCbu($data['form']['cbu_datos_bancario']);
                $pdb->setNumeroCuenta($data['form']['numero_cuenta_dato_bancario']);
                $pdb->setIdUsuario($this->getUser());
                $pdb->setCuentaLocal(1);
                $pdb->setIdDatoPersonal($provDatoPersonal);


                $em->persist($pdb);
                $em->flush();

            }

            $request->getSession()->set('finanzas', true);
            $this->guardarTimeline('timeline_datos_bancarios', 'completo', 1, $data['form']['id_dato_personal']);

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
     * Agregar dato bancario extranjero.
     *
     * @Route("/formulariopreinscripcion/agregardatobancarioextranjero", name="agregar_dato_bancario_extranjero")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarDatoBancarioExtranjeroAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();


            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoDatos = $em->getRepository(ProveedorDatoBancario::class);
            $datosBancarios = $repoDatos->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($datosBancarios instanceof ProveedorDatoBancario) {

                $datosBancarios->setLocalidadExtranjero($data['form']['localidad_dato_bancario']);
                $datosBancarios->setTipoMoneda($data['form']['moneda']);
                $datosBancarios->setAba($data['form']['aba']);
                $datosBancarios->setBeneficiario($data['form']['beneficiario']);
                $datosBancarios->setBancoCorresponsal($data['form']['banco_corresponsal']);
                $datosBancarios->setSwiftBancoCorresponsal($data['form']['swift_banco_corresponsal']);
                $datosBancarios->setNumeroCuenta($data['form']['numero_cuenta_dato_bancario']);
                $datosBancarios->setIban($data['form']['iban']);
                $datosBancarios->setSwift($data['form']['swift']);
                $datosBancarios->setIdUsuario($this->getUser());
                $datosBancarios->setCuentaLocal($data['form']['cuenta_local'] == 'true' ? 0 : 1);

                $em->merge($datosBancarios);
                $em->flush();

            } else {

                $pdb = New ProveedorDatoBancario();
                $pdb->setLocalidadExtranjero($data['form']['localidad_dato_bancario']);
                $pdb->setTipoMoneda($data['form']['moneda']);
                $pdb->setAba($data['form']['aba']);
                $pdb->setBeneficiario($data['form']['beneficiario']);
                $pdb->setBancoCorresponsal($data['form']['banco_corresponsal']);
                $pdb->setSwiftBancoCorresponsal($data['form']['swift_banco_corresponsal']);
                $pdb->setNumeroCuenta($data['form']['numero_cuenta_dato_bancario']);
                $pdb->setIdUsuario($this->getUser());
                $pdb->setIban($data['form']['iban']);
                $pdb->setSwift($data['form']['swift']);
                $pdb->setCuentaLocal($data['form']['cuenta_local'] == 'true' ? 0 : 1);
                $pdb->setIdDatoPersonal($provDatoPersonal);

                $em->persist($pdb);
                $em->flush();

            }
            $this->guardarTimeline('timeline_datos_bancarios', 'completo', 1, $data['form']['id_dato_personal']);
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
     * Datos Bancarios.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosBancariosAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorDatoBancario::class);
        $datosBancarios = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal));
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_bancarios']);

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

        $sql = "SELECT id , nombre FROM banco WHERE fecha_baja IS NULL";
        $emRrhh = $this->getDoctrine()->getManager('adif_rrhh');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        $bancos = $stmt->fetchAll();

        $choices = [];
        foreach ($bancos as $table2Obj) {
            $choices[$table2Obj['nombre']] = $table2Obj['id'];
        }

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDocCBU = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_bancarios_cbu'));
        $tipoDocBancarios = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_bancarios'));
        $documentacionCBU = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocCBU->getId()));
        $documentacionDatosBancarios = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocBancarios->getId()));


        $formDatosBancarios = $this->createFormBuilder()
            ->add('entidad_bancaria', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices
            ))
            ->add('sucursal', TextType::class, array('label' => ' ', 'data' => $datosBancarios ? $datosBancarios->getSucursalBancaria() : '',
                'attr' => array('maxlength' => 36)))
            ->add('numero_sucursal', TextType::class, array('label' => ' ', 'data' => $datosBancarios ? $datosBancarios->getNumeroSucursal() : null, 'attr' => array('maxlength' => 5)))
            ->add('cbu_datos_bancario', TextType::class, array('label' => ' ', 'data' => $datosBancarios ? $datosBancarios->getCbu() : null, 'attr' => array('maxlength' => 22)))
            ->add('numero_cuenta_dato_bancario', TextType::class, array('label' => ' ', 'data' => $datosBancarios ? $datosBancarios->getNumeroCuenta() : null, 'attr' => array('maxlength' => 14)))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_datos_bancarios.html.twig', array(
            'formDatosBancarios' => $formDatosBancarios->createView(),
            'documentacionComprobanteCBU' => $documentacionCBU ? $documentacionCBU : array(),
            'documentacionDatosBancarios' => $documentacionDatosBancarios ? $documentacionDatosBancarios : array(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }


    /**
     * Datos Bancarios Extranjero.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosBancariosExtranjeroAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorDatoBancario::class);
        $datosBancarios = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal));

        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_bancarios']);

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

        $sql = "SELECT id,denominacion FROM tipo_moneda";
        $emRrhh = $this->getDoctrine()->getManager('adif_contable');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        $monedas = $stmt->fetchAll();

        $choicesMoneda = [];
        foreach ($monedas as $table2Obj) {
            $choicesMoneda[$table2Obj['denominacion']] = $table2Obj['id'];
        }


        $formDatosBancarios = $this->createFormBuilder()
            ->add('localidad_dato_bancario', TextType::class, array('label' => ' ', 'data' => $datosBancarios ? $datosBancarios->getLocalidadExtranjero() : null,'attr' => array('maxlength' => 36)))
            ->add('moneda', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choicesMoneda,
                'data' => $datosBancarios ? $datosBancarios->getTipoMoneda() : null
            ))
            ->add('swift', TextType::class, array(
                'label' => ' ',
                'data' => $datosBancarios ? $datosBancarios->getSwift() : null,
                'attr' => array('maxlength' => 11)))
            ->add('aba', TextType::class, array(
                'label' => ' ',
                'data' => $datosBancarios ? $datosBancarios->getAba() : null,
                'attr' => array('maxlength' => 9)))
            ->add('iban', TextType::class, array(
                'label' => ' ',
                'required' => false,
                'data' => $datosBancarios ? $datosBancarios->getIban() : null,
                'attr' => array('maxlength' => 34)))
            ->add('beneficiario', TextType::class, array(
                'label' => ' ',
                'data' => $datosBancarios ? $datosBancarios->getBeneficiario() : null,
                'attr' => array('maxlength' => 36)))
            ->add('banco_corresponsal', TextType::class, array(
                'label' => ' ',
                'required' => false,
                'data' => $datosBancarios ? $datosBancarios->getBancoCorresponsal() : null,
                'attr' => array('maxlength' => 36)))
            ->add('swift_banco_corresponsal', TextType::class, array(
                'label' => ' ',
                'required' => false,
                'data' => $datosBancarios ? $datosBancarios->getSwiftBancoCorresponsal() : null,
                'attr' => array('maxlength' => 36)))
            ->add('numero_cuenta_dato_bancario', TextType::class, array(
                'label' => ' ',
                'data' => $datosBancarios ? $datosBancarios->getNumeroCuenta() : null))
            ->add('cuenta_local', HiddenType::class, array(
                'label' => ' '))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_datos_bancarios_extranjero.html.twig', array(
            'formDatosBancariosExtranjero' => $formDatosBancarios->createView(),
            'datoBancario' => $datosBancarios ? $datosBancarios : null,
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }
}
