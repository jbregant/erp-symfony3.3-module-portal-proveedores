<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ImpuestoGanancias;
use GYL\ProveedorBundle\Entity\ImpuestoIibb;
use GYL\ProveedorBundle\Entity\ImpuestoIva;
use GYL\ProveedorBundle\Entity\ImpuestoSuss;
use GYL\ProveedorBundle\Entity\ProveedorDatoImpositivos;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\TipoIva;
use GYL\ProveedorBundle\Entity\TipoIvaInscripto;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use GYL\ProveedorBundle\Entity\DatoExento;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
class DatosImpositivosController extends BaseController
{
    /**
     * Agregar datos impositivos.
     *
     * @Route("/formulariopreinscripcion/agregardatosimpositivos", name="agregar_datos_impositivos")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarDatosImpositivosAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoDatos = $em->getRepository(ProveedorDatoImpositivos::class);
            $datosImpositivos = $repoDatos->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);

            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($datosImpositivos instanceof ProveedorDatoImpositivos) {

                $repTipoIva = $em->getRepository(TipoIva::class);
                if (isset($data['form']['id_tipo_iva_iva']))
                    $tipoiva_iva = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_iva']]);
                if (isset($data['form']['id_tipo_iva_suss']))
                    $tipoiva_suss = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_suss']]);
                if (isset($data['form']['id_tipo_iva_ganancias']))
                    $tipoiva_ganancias = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_ganancias']]);
                if (isset($data['form']['id_tipo_iva_iibb']))
                    $tipoiva_iibb = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_iibb']]);

                if (isset($data['form']['id_tipo_iva_inscripto'])){
                    $repTipoIvaInscripto = $em->getRepository(TipoIvaInscripto::class);
                    $tipoiva_inscripto = $repTipoIvaInscripto->findOneBy(['id' => $data['form']['id_tipo_iva_inscripto']]);
                }
                $repDatoExento = $em->getRepository(DatoExento::class);


                //Actualiza ImpuestoIVA
                $impuestoIvaRepo = $em->getRepository(ImpuestoIva::class);
                $impuestoIva = $impuestoIvaRepo->findOneBy(['id' => $datosImpositivos->getProveedorIva()]);

                if (isset($data['form']['exento_iva']))
                    $impuestoIva->setExento($data['form']['exento_iva'] == 'true' ? 1 : 0);
                if (isset($tipoiva_iva))
                    $impuestoIva->setTipoIva($tipoiva_iva);
                if (isset($data['form']['otros_iva']))
                    $impuestoIva->setOtros($data['form']['otros_iva']);
                if (isset($data['form']['retencion_iva']))
                    $impuestoIva->setRetencion($data['form']['retencion_iva'] == 'true' ? 1 : 0);
                $dato_exento = $repDatoExento->findOneBy(['id' => $impuestoIva->getDatoExento()]);
                if (!isset($dato_exento) || is_null($dato_exento)) $dato_exento = new DatoExento();
                if (isset($data['form']['exento_fecha_desde']))
                    $dato_exento->setFechaDesde(new \DateTime($data['form']['exento_fecha_desde']));
                if (isset($data['form']['exento_fecha_hasta']))
                    $dato_exento->setFechaHasta(new \DateTime($data['form']['exento_fecha_hasta']));
                if (isset($data['form']['exento_porcentaje_exencion']))
                    $dato_exento->setPorcentajeExencion(is_null($data['form']['exento_porcentaje_exencion']) ? 0 : $data['form']['exento_porcentaje_exencion']);
                if (isset($data['form']['exento_regimen']))
                    $dato_exento->setRegimen($data['form']['exento_regimen']);
                if (isset($data['form']['exento_otros']))
                    $dato_exento->setOtros($data['form']['exento_otros']);
                $impuestoIva->setDatoExento($dato_exento);

                if ($data['form']['exento_iva'] == 'true') {
                    if ($repDatoExento->findOneBy(['id' => $impuestoIva->getDatoExento()]) == null) {
                        $em->persist($dato_exento);
                    } else {
                        $em->merge($dato_exento);
                    }
                } else {
                    $impuestoIva->setDatoExento(null);
                    $em->remove($dato_exento);
                }

                //Actualiza ImpuestoSuss
                $ImpuestoSussRepo = $em->getRepository(ImpuestoSuss::class);
                $ImpuestoSuss = $ImpuestoSussRepo->findOneBy(['id' => $datosImpositivos->getIdProveedorSuss()]);
                if (isset($data['form']['retencion_suss']))
                    $ImpuestoSuss->setRetencion($data['form']['retencion_suss'] == 'true' ? 1 : 0);
                if (isset($tipoiva_suss))
                    $ImpuestoSuss->setTipoIva($tipoiva_suss);
                if (isset($data['form']['exento_suss']))
                    $ImpuestoSuss->setExento($data['form']['exento_suss'] == 'true' ? 1 : 0);
                if (isset($data['form']['personal_a_cargo_suss']))
                    $ImpuestoSuss->setPersonalACargo($data['form']['personal_a_cargo_suss']);

                //Actualiza ImpuestoGanancias
                $impuestoGananciasRepo = $em->getRepository(ImpuestoGanancias::class);
                $impuestoGanancias = $impuestoGananciasRepo->findOneBy(['id' => $datosImpositivos->getIdProveedorGanancias()]);
                if (isset($data['form']['exento_ganancias']))
                    $impuestoGanancias->setExento($data['form']['exento_ganancias'] == 'true' ? 1 : 0);
                if (isset($tipoiva_ganancias))
                    $impuestoGanancias->setTipoIva($tipoiva_ganancias);
                if (isset($data['form']['retencion_ganancias']))
                    $impuestoGanancias->setRetencion($data['form']['retencion_ganancias'] == 'true' ? 1 : 0);
                if (isset($data['form']['otros_ganancias']))
                    $impuestoGanancias->setOtros($data['form']['otros_ganancias']);

                //Actualiza ImpuestoIibb
                $impuestoIibbRepo = $em->getRepository(ImpuestoIibb::class);
                $impuestoIibb = $impuestoIibbRepo->findOneBy(['id' => $datosImpositivos->getIdProveedorIibb()]);
                if (isset($data['form']['otros_iibb']))
                    $impuestoIibb->setOtros(@$data['form']['otros_iibb']);
                if (isset($data['form']['retencion_iibb']))
                    $impuestoIibb->setRetencion($data['form']['retencion_iibb'] == 'true' ? 1 : 0);
                if (isset($tipoiva_iibb))
                    $impuestoIibb->setTipoIva($tipoiva_iibb);
                if (isset($data['form']['exento_iibb']))
                    $impuestoIibb->setExento($data['form']['exento_iibb'] == 'true' ? 1 : 0);
                if (isset($tipoiva_inscripto))
                    $impuestoIibb->setIdTipoIvaInscripto($tipoiva_inscripto);
                if (isset($data['form']['jurisdiccion_iibb']))
                    $impuestoIibb->setJurisdiccion($data['form']['jurisdiccion_iibb']);
                if (isset($data['form']['numero_inscripcion_iibb']))
                    $impuestoIibb->setNumeroInscripcion($data['form']['numero_inscripcion_iibb']);


                $datosImpositivos->setProveedorIva($impuestoIva);
                $datosImpositivos->setIdProveedorSuss($ImpuestoSuss);
                $datosImpositivos->setIdProveedorGanancias($impuestoGanancias);
                $datosImpositivos->setIdProveedorIibb($impuestoIibb);

                if (isset ($data['form']['cae_dato_impositivo'])) {
                    $datosImpositivos->setCae($data['form']['cae_dato_impositivo']);
                }

                if (isset ($data['form']['cai_dato_impositivo'])) {
                    $datosImpositivos->setCai($data['form']['cai_dato_impositivo']);
                }

                if (isset ($data['form']['otros_dato_impositivo'])) {
                    $datosImpositivos->setOtros($data['form']['otros_dato_impositivo']);
                }

                $em->merge($impuestoIva);
                $em->merge($ImpuestoSuss);
                $em->merge($impuestoIibb);
                $em->merge($impuestoGanancias);

                $em->merge($datosImpositivos);
                $em->flush();


            } else {

                $repTipoIva = $em->getRepository(TipoIva::class);
                $tipoiva_iva = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_iva']]);
                $tipoiva_suss = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_suss']]);
                $tipoiva_ganancias = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_ganancias']]);
                $tipoiva_iibb = $repTipoIva->findOneBy(['id' => $data['form']['id_tipo_iva_iibb']]);
                $repTipoIvaInscripto = $em->getRepository(TipoIvaInscripto::class);
                $tipoiva_inscripto = $repTipoIvaInscripto->findOneBy(['id' => $data['form']['id_tipo_iva_inscripto']]);


                //SETEA  NUEVO ImpuestoIVA
                $impuestoIva = New ImpuestoIva();
                $impuestoIva->setExento($data['form']['exento_iva'] == 'true' ? 1 : 0);
                $impuestoIva->setTipoIva($tipoiva_iva);
                $impuestoIva->setOtros($data['form']['otros_iva']);
                $impuestoIva->setRetencion($data['form']['retencion_iva'] == 'true' ? 1 : 0);
                $dato_exento = new DatoExento();
                $dato_exento->setFechaDesde(new \DateTime($data['form']['exento_fecha_desde']));
                $dato_exento->setFechaHasta(new \DateTime($data['form']['exento_fecha_hasta']));
                $dato_exento->setPorcentajeExencion(empty($data['form']['exento_porcentaje_exencion']) ? 0 : $data['form']['exento_porcentaje_exencion']);
                $dato_exento->setRegimen(empty($data['form']['exento_regimen']) ? 0 : $data['form']['exento_regimen']);
                $dato_exento->setOtros($data['form']['exento_otros']);
                $impuestoIva->setDatoExento($data['form']['exento_iva'] == 'true' ? $dato_exento : null);


                //SETEA NUEVO ImpuestoSuss
                $ImpuestoSuss = New ImpuestoSuss();
                $ImpuestoSuss->setRetencion($data['form']['retencion_suss'] == 'true' ? 1 : 0);
                $ImpuestoSuss->setTipoIva($tipoiva_suss);
                $ImpuestoSuss->setExento($data['form']['exento_suss'] == 'true' ? 1 : 0);
                $ImpuestoSuss->setPersonalACargo($data['form']['personal_a_cargo_suss']);

                //SETEA NUEVO ImpuestoGanancias
                $impuestoGanancias = New ImpuestoGanancias();
                $impuestoGanancias->setExento($data['form']['exento_ganancias'] == 'true' ? 1 : 0);
                $impuestoGanancias->setTipoIva($tipoiva_ganancias);
                $impuestoGanancias->setRetencion($data['form']['retencion_ganancias'] == 'true' ? 1 : 0);
                $impuestoGanancias->setOtros($data['form']['otros_ganancias']);

                //SETEA NUEVO ImpuestoIibb
                $impuestoIibb = New ImpuestoIibb();
                $impuestoIibb->setOtros($data['form']['otros_iibb']);
                $impuestoIibb->setRetencion($data['form']['retencion_iibb'] == 'true' ? 1 : 0);
                $impuestoIibb->setTipoIva($tipoiva_iibb);
                $impuestoIibb->setExento($data['form']['exento_iibb'] == 'true' ? 1 : 0);
                $impuestoIibb->setIdTipoIvaInscripto($tipoiva_inscripto);
                $impuestoIibb->setJurisdiccion($data['form']['jurisdiccion_iibb']);
                $impuestoIibb->setNumeroInscripcion($data['form']['numero_inscripcion_iibb']);


                $dimp = New ProveedorDatoImpositivos();
                $dimp->setProveedorIva($impuestoIva);
                $dimp->setIdProveedorSuss($ImpuestoSuss);
                $dimp->setIdProveedorGanancias($impuestoGanancias);
                $dimp->setIdProveedorIibb($impuestoIibb);
                $dimp->setIdDatoPersonal($provDatoPersonal);

                if (isset ($data['form']['cae_dato_impositivo'])) {
                    $dimp->setCae($data['form']['cae_dato_impositivo']);
                } else {
                    $dimp->setCae(0);
                }
                if (isset ($data['form']['cai_dato_impositivo'])) {
                    $dimp->setCai($data['form']['cai_dato_impositivo']);
                } else {
                    $dimp->setCai(0);
                }
                if (isset ($data['form']['otros_dato_impositivo'])) {
                    $dimp->setOtros($data['form']['otros_dato_impositivo']);
                } else {
                    $dimp->setOtros("");
                }
                $dimp->setIdUsuario($this->getUser());

                $em->persist($impuestoIva);
                $em->persist($dato_exento);
                $em->persist($ImpuestoSuss);
                $em->persist($impuestoIibb);
                $em->persist($impuestoGanancias);
                $em->persist($dimp);

                $em->flush();

            }

            $response = 200;
            $msg = 'ok';
            $this->guardarTimeline('timeline_datos_impositivos', 'completo', 1, $data['form']['id_dato_personal']);

        } catch (Exception $e) {
            $msg = 'error';
            $response = 500;
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
     * Datos Impositivos.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosImpositivosAction(Request $request, $estadoEvaluacionGral, $ddjjFilename, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorDatoImpositivos::class);
        $datosImpositivos = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal));
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);



        //observaciones
        $tipoObsevacionDatosImpositivos = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_impositivos']);
        $tipoObsevacionIva = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'iva']);
        $tipoObsevacionSuss = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'suss']);
        $tipoObsevacionGanancias = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'ganancias']);
        $tipoObsevacionIngresosBrutos = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'ingresos_brutos']);
        $tipoObsevacionCae = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'cae_cai']);


        //hotfix: genero las observaciones para cada panel
        if ($proveedorEvaluacion)
        {
            $estadoEvaluacionGafImpuestos = $proveedorEvaluacion->getEstadoEvaluacionGafImpuestos()->getId();

            $observacionEvaluacion = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionDatosImpositivos->getId(),
                'activo' => 1
            ]);
            $observacionEvaluacionIva = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionIva->getId(),
                'activo' => 1
            ]);
            $observacionEvaluacionSuss = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionSuss->getId(),
                'activo' => 1
            ]);
            $observacionEvaluacionGanancias = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionGanancias->getId(),
                'activo' => 1
            ]);
            $observacionEvaluacionIIBB = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionIngresosBrutos->getId(),
                'activo' => 1
            ]);
            $observacionEvaluacionCae = $em->getRepository(ObservacionEvaluacion::class)->findBy([
                'proveedorEvaluacion' => $proveedorEvaluacion->getId(),
                'tipoObservacion' => $tipoObsevacionCae->getId(),
                'activo' => 1
            ]);

            if(empty($observacionEvaluacion))
                $observacionEvaluacion = null;

            if(empty($observacionEvaluacionIva))
                $observacionEvaluacionIva = null;

            if(empty($observacionEvaluacionSuss))
                $observacionEvaluacionSuss = null;

            if(empty($observacionEvaluacionGanancias))
                $observacionEvaluacionGanancias = null;

            if(empty($observacionEvaluacionIIBB))
                $observacionEvaluacionIIBB = null;

            if(empty($observacionEvaluacionCae))
                $observacionEvaluacionCae = null;

        }
        else
        {
            $estadoEvaluacionGafImpuestos = null;
            $observacionEvaluacion = null;
            $observacionEvaluacionIva = null;
            $observacionEvaluacionSuss = null;
            $observacionEvaluacionGanancias = null;
            $observacionEvaluacionIIBB = null;
            $observacionEvaluacionCae = null;
        }

        $estadoEvaluacionGerencia = null;

        $isDisabled = false;
        ($estadoEvaluacionGral && $estadoEvaluacionGral != 4 && $estadoEvaluacionGerencia != 4) ? $isDisabled = "'disabled'" : false ;

        if ($datosImpositivos instanceof ProveedorDatoImpositivos) {
            $repoImpIva = $em->getRepository(ImpuestoIva::class);
            $repoImpSuss = $em->getRepository(ImpuestoSuss::class);
            $repoImpGncias = $em->getRepository(ImpuestoGanancias::class);
            $repoImpIibb = $em->getRepository(ImpuestoIibb::class);
            $repoDatoExento = $em->getRepository(DatoExento::class);
            $panelImpIva = $repoImpIva->findOneBy(array('id' => $datosImpositivos->getProveedorIva()));
            $panelImpSuss = $repoImpSuss->findOneBy(array('id' => $datosImpositivos->getIdProveedorSuss()));
            $panelImpGncias = $repoImpGncias->findOneBy(array('id' => $datosImpositivos->getIdProveedorGanancias()));
            $panelImpIibb = $repoImpIibb->findOneBy(array('id' => $datosImpositivos->getIdProveedorIibb()));
            $datoExento = $repoDatoExento->findOneBy(array('id' => $panelImpIva->getDatoExento()));
        } else {
            $panelImpIva = null;
            $panelImpSuss = null;
            $panelImpGncias = null;
            $panelImpIibb = null;
            $datoExento = null;
        }


        $repoTipoIva = $em->getRepository(TipoIva::class);
        $repoTipoIvaInscripto = $em->getRepository(TipoIvaInscripto::class);
        $tiposIva = $repoTipoIva->findAll();
        $tiposIvaInscripto = $repoTipoIvaInscripto->findAll();

        $sqlProvincia = "SELECT id , denominacion as nombre FROM jurisdiccion";

        $emProv = $this->getDoctrine()->getManager('adif_contable');

        $stmtP = $emProv->getConnection()->prepare($sqlProvincia);
        $stmtP->execute();
        $provincias = $stmtP->fetchAll();

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDocIva = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_iva'));
        $tipoDocSuss = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_suss'));
        $tipoDocGcias = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_ganancias'));
        $tipoDocIibb = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_iibb'));
        $tipoDocConstancia = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_constancia_inscripcion'));
        $tipoDocDdjj = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_datos_impositivos_ddjj'));

        $documentacionIva = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocIva->getId()));
        $documentacionSuss = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocSuss->getId()));
        $documentacionGncias = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocGcias->getId()));
        $documentacionIibb = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocIibb->getId()));
        $documentacionDatosImpositivos = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocConstancia->getId()));
        $documentacionDdjj = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDocDdjj->getId()));

        $choicesTipoIva = [];
        foreach ($tiposIva as $table2Obj) {
            $choicesTipoIva[$table2Obj->getDenominacion()] = $table2Obj->getId();
        }

        $choicesTipoIvaInscriptos = [];
        foreach ($tiposIvaInscripto as $table2Obj2) {
            $choicesTipoIvaInscriptos[$table2Obj2->getDenominacion()] = $table2Obj2->getId();
        }

        $choicesProvincias = [];
        foreach ($provincias as $provincia) {
            $choicesProvincias[$provincia['nombre']] = $provincia['id'];
        }

        $formIva = $this->createFormBuilder()
            ->add('id_tipo_iva_iva', ChoiceType::class, array(
                'choices' => $choicesTipoIva,
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpIva != null ? $panelImpIva->getTipoIva()->getId() : '' ,
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionIva != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionIva == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('exento_iva', HiddenType::class, array('label' => ''))
            ->add('retencion_iva', HiddenType::class, array('label' => ''))
            ->add('otros_iva', TextType::class, array('label' => 'Otros', 'data' => $panelImpIva == null ? '' : $panelImpIva->getOtros(),
                'attr' => array('maxlength' => 36),'required' => false))
            ->getForm();

        $formSuss = $this->createFormBuilder()
            ->add('id_tipo_iva_suss', ChoiceType::class, array(
                'choices' => $choicesTipoIva,
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpSuss != null ? $panelImpSuss->getTipoIva()->getId() : '',
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionSuss != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionSuss == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('personal_a_cargo_suss', ChoiceType::class, array(
                'choices' => array(
                    'Sin Empleados' => 0,
                    'Con empleados' => 1),
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpSuss != null ? $panelImpSuss->getPersonalACargo() : '',
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionSuss != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionSuss == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('exento_suss', HiddenType::class, array('label' => ''))
            ->add('retencion_suss', HiddenType::class, array('label' => ''))
            ->getForm();

        $formGanancias = $this->createFormBuilder()
            ->add('id_tipo_iva_ganancias', ChoiceType::class, array(
                'choices' => $choicesTipoIva,
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpGncias != null ? $panelImpGncias->getTipoIva()->getId() : '',
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionGanancias != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionGanancias == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('exento_ganancias', HiddenType::class, array('label' => ''))
            ->add('retencion_ganancias', HiddenType::class, array('label' => ''))
            ->add('otros_ganancias', TextType::class, array('label' => 'Otros', 'data' => $panelImpGncias == null ? '' : $panelImpGncias->getOtros(), 'attr' => array('maxlength' => 36), 'required' => false))
            ->getForm();


        $formIibb = $this->createFormBuilder()
            ->add('id_tipo_iva_iibb', ChoiceType::class, array(
                'choices' => $choicesTipoIva,
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpIibb != null ? $panelImpIibb->getTipoIva()->getId() : '',
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionIIBB != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionIIBB == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('id_tipo_iva_inscripto', ChoiceType::class, array(
                'choices' => $choicesTipoIvaInscriptos,
                'expanded' => true,
                'multiple' => false,
                'data' => $panelImpIibb != null ? $panelImpIibb->getIdTipoIvaInscripto()->getId() : '' ,
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionIIBB != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionIIBB == null) || $unlockForm != null) ? '' : 'disabled'))
            ->add('exento_iibb', HiddenType::class, array('label' => ''))
            ->add('retencion_iibb', HiddenType::class, array('label' => ''))
            ->add('otros_iibb', TextType::class, array('label' => 'Otros', 'data' => $panelImpIibb == null ? '' : $panelImpIibb->getOtros(),
                'attr' => array('maxlength' => 36),'required' => false))
            ->add('numero_inscripcion_iibb', TextType::class, array('label' => 'Numero de Inscripción ', 'data' => $panelImpIibb == null ? null : $panelImpIibb->getNumeroInscripcion(),
                'attr' => array('maxlength' => 13)))
            ->add('jurisdiccion_iibb', ChoiceType::class, array('label' => 'Jurisdicción Sede',
                'mapped' => false,
                'choices' => $choicesProvincias,
                'data' => $panelImpIibb == null ? '' : $panelImpIibb->getJurisdiccion(),
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionIIBB != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionIIBB == null) || $unlockForm != null) ? '' : 'disabled'))
            ->getForm();

        $formCae = $this->createFormBuilder()
            ->add('cae_dato_impositivo', CheckboxType::class, array('label' => 'CAE', 'data' => $datosImpositivos != null ? $datosImpositivos->getCae() == 1 ? true : false : null, 'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionCae != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionCae == null) || $unlockForm != null) ? '' : 'disabled', 'required' => false))
            ->add('cai_dato_impositivo', CheckboxType::class, array('label' => 'CAI', 'data' => $datosImpositivos != null ? $datosImpositivos->getCai() == 1 ? true : false : null, 'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionCae != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionCae == null) || $unlockForm != null) ? '' : 'disabled', 'required' => false))
            ->add('otros_dato_impositivo', TextType::class, array('label' => 'Otros', 'data' => $datosImpositivos != null ? $datosImpositivos->getOtros() : '',
                'attr' => array('maxlength' => 36), 'required' => false,
                'disabled' => (($estadoEvaluacionGafImpuestos == 4 && $observacionEvaluacionCae != null) || ($estadoEvaluacionGafImpuestos == null && $observacionEvaluacionCae == null) || $unlockForm != null) ? '' : 'disabled'))
            ->getForm();

        $formExento = $this->createFormBuilder()
            ->add('exento_fecha_desde', DateType::class, array('label' => ' ', 'data' => $datoExento != null ? $datoExento->getFechaDesde() : new \DateTime,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']))
            ->add('exento_fecha_hasta', DateType::class, array('label' => ' ', 'data' => $datoExento != null ? $datoExento->getFechaHasta() : new \DateTime,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']))
            ->add('exento_porcentaje_exencion', IntegerType::class, array('label' => '*Porcentaje exención', 'data' => $datoExento != null ? $datoExento->getPorcentajeExencion() : null,
                'attr' => array('maxlength' => 3)))
            ->add('exento_regimen', IntegerType::class, array('label' => '*Régimen', 'data' => $datoExento != null ? $datoExento->getRegimen() : null,
                'attr' => array('maxlength' => 36)))
            ->add('exento_otros', TextType::class, array('label' => 'Otros', 'data' => $datoExento != null ? $datoExento->getOtros() : '',
                'attr' => array('maxlength' => 36), 'required' => false))
            ->getForm();

        $formDdjj = $this->createFormBuilder()
            ->add('inputFileNameDdjj',FileType::class, array('label' => 'inputDocDdjj', 'attr' => array('class' => 'inputfile')))
            ->getForm();

        return $this->render('ProveedorBundle::Preinscripcion/panel_datos_impositivos.html.twig', array(
            'formIva' => $formIva->createView(),
            'formSuss' => $formSuss->createView(),
            'formGanancias' => $formGanancias->createView(),
            'formIibb' => $formIibb->createView(),
            'formCae' => $formCae->createView(),
            'formExento' => $formExento->createView(),
            'formDdjj' => $formDdjj->createView(),
            'panelImpIva' => $panelImpIva == null ? new ImpuestoIva() : $panelImpIva,
            'panelImpSuss' => $panelImpSuss == null ? new ImpuestoSuss() : $panelImpSuss,
            'panelImpGncias' => $panelImpGncias == null ? new ImpuestoGanancias() : $panelImpGncias,
            'panelImpIibb' => $panelImpIibb == null ? new ImpuestoIibb() : $panelImpIibb,
            'documentacionIva' => $documentacionIva ? $documentacionIva : array(),
            'documentacionSuss' => $documentacionSuss ? $documentacionSuss : array(),
            'documentacionGncias' => $documentacionGncias ? $documentacionGncias : array(),
            'documentacionIibb' => $documentacionIibb ? $documentacionIibb : array(),
            'documentacionDdjj' => $documentacionDdjj ? $documentacionDdjj : array(),
            'documentacionDatosImpositivos' => $documentacionDatosImpositivos ? $documentacionDatosImpositivos : array(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'observacionEvaluacionIva' => $observacionEvaluacionIva,
            'observacionEvaluacionSuss' => $observacionEvaluacionSuss,
            'observacionEvaluacionGanancias' => $observacionEvaluacionGanancias,
            'observacionEvaluacionIIBB' => $observacionEvaluacionIIBB,
            'observacionEvaluacionCae' => $observacionEvaluacionCae,
            'ddjjFilename' => $ddjjFilename,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     * @Route("/preinscripcion/documentos/{file}")
     * @return binary file
     */
    public function documentosAction($file)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            return new Response("Access denied", 403);
        }
        $filePath = $_SERVER['DOCUMENT_ROOT'].'/documentos/'.$file;
        return $this->file($filePath);

    }
}
