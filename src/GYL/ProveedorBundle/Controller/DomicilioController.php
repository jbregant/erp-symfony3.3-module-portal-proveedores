<?php

namespace GYL\ProveedorBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorDomicilio;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\TipoDomicilio;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Domicilio controller.
 *
 * @Route("/")
 */
class DomicilioController extends BaseController
{
    /**
     * Buscar Localidad.
     *
     * @Route("/formulariopreinscripcion/buscarlocalidad/{id}", name="buscar_localidad")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function buscarLocalidadAction(Request $request, $id)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $sql = "SELECT id,nombre FROM localidad WHERE id_provincia = :id";

            $em = $this->getDoctrine()->getManager('adif_rrhh');

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $msg = 'ok';
            $response = 200;

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy';
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
            'data' => $serializer->serialize($data, 'json')

        ]);


    }

    /**
     * Agregar Domicilio.
     *
     * @Route("/formulariopreinscripcion/agregardomicilio/{tipo}", name="agregar_domicilio")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarDomicilioAction(Request $request, $tipo)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }

        try {
            switch ($tipo) {
                case 3:
                    $sufijo = '_fiscal';
                    $timelinetype = 'timeline_domicilio_fiscal';
                    break;
                case 2:
                    $sufijo = '_legal';
                    $timelinetype = 'timeline_domicilio_legal';
                    break;
                case 1:
                    $sufijo = '_real';
                    $timelinetype = 'timeline_domicilio_real';
                    break;
                case 5:
                    $sufijo = '_exterior';
                    $timelinetype = 'timeline_domicilio_exterior';
                    break;
                default:
                    $sufijo = '';
                    $timelinetype = 'timeline_domicilio_contractual';
            }
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager('adif_proveedores');


            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $proveedorDatoPersonal = $repoProveedorDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            $repo = $em->getRepository(ProveedorDomicilio::class);
            $domi = $repo->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal'], 'tipoDomicilio' => $tipo]);


            $repo = $em->getRepository(TipoDomicilio::class);
            $tipoDomicilio = $repo->findOneBy(['id' => $tipo]);

            if ($domi instanceof ProveedorDomicilio) {

                $domi->setIdUsuario($user);
                $domi->setTipoDomicilio($tipoDomicilio);
                if (isset($data['form']['localidad' . $sufijo])) {
                    $domi->setIdLocalidad($data['form']['localidad' . $sufijo]);
                }
                if (isset($data['form']['provincias' . $sufijo])) {
                    $domi->setIdProvincia($data['form']['provincias' . $sufijo]);
                }
                if (isset($data['form']['provincia_exterior'])) {
                    $domi->setProvinciaEstadoExterior($data['form']['provincia_exterior']);
                }
                if (isset($data['form']['nacionalidad_exterior'])) {
                    $domi->setIdPais($data['form']['nacionalidad_exterior']);
                }

                $domi->setCodigoPostal($data['form']['codigo_postal']);
                $domi->setCalle($data['form']['calle']);
                $domi->setDepartamento($data['form']['departamento']);
                $domi->setPiso($data['form']['piso']);
                $domi->setTelefono($data['form']['telefono']);
                $domi->setIdDatoPersonal($proveedorDatoPersonal);
                $em->merge($domi);
                $em->flush();
            } else {
                $pd = New ProveedorDomicilio();

                $pd->setIdUsuario($user);
                $pd->setTipoDomicilio($tipoDomicilio);
                if (isset ($data['form']['localidad' . $sufijo])) {
                    $pd->setIdLocalidad($data['form']['localidad' . $sufijo]);
                }
                if (isset($data['form']['provincias' . $sufijo])) {
                    $pd->setIdProvincia($data['form']['provincias' . $sufijo]);
                }
                if (isset($data['form']['provincia_exterior'])) {
                    $pd->setProvinciaEstadoExterior($data['form']['provincia_exterior']);
                }
                if (isset($data['form']['nacionalidad_exterior'])) {
                    $pd->setIdPais($data['form']['nacionalidad_exterior']);
                }

                $pd->setCodigoPostal($data['form']['codigo_postal']);
                $pd->setCalle($data['form']['calle']);
                $pd->setDepartamento($data['form']['departamento']);
                $pd->setPiso($data['form']['piso']);
                $pd->setTelefono($data['form']['telefono']);
                $pd->setIdDatoPersonal($proveedorDatoPersonal);

                $em->persist($pd);
                $em->flush();

            }
            $msg = 'ok';
            $response = 200;
            $this->guardarTimeline($timelinetype, 'completo', 1, $data['form']['id_dato_personal']);

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy';
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
            'data' => 'ok'

        ]);


    }

    /**
     * Buscar Provincia por localidad.
     *
     * @Route("/formulariopreinscripcion/buscarprovinciaporlocalidad/{id}", name="buscar_provincia_por_localidad")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function buscarProvinciaPorLocalidadAction(Request $request, $id)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $sql = "SELECT id_provincia FROM localidad WHERE id = :id LIMIT 1";

            $em = $this->getDoctrine()->getManager('adif_rrhh');

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $msg = 'ok';
            $response = 200;

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
            'data' => $serializer->serialize($data, 'json')

        ]);


    }

    /**
     * Domicilio.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function domicilioLegalAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'domicilio_legal']);

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
     
        $repo = $em->getRepository(ProveedorDomicilio::class);
        $proveedorDomicilio = $repo->findOneBy(array('idUsuario' => $user->getId(), 'tipoDomicilio' => 2, 'idDatoPersonal' => $idProvDatoPersonal));

        $sql = "SELECT * FROM provincia";

        $em2 = $this->getDoctrine()->getManager('adif_rrhh');

        $stmt = $em2->getConnection()->prepare($sql);
        $stmt->execute();
        $provincias = $stmt->fetchAll();

        $choices = [];
        $choices = ['Elija una provincia' => ''];
        foreach ($provincias as $provincia) {
            $choices[$provincia['nombre']] = $provincia['id'];
        }


        $domicilioForm = $this->createFormBuilder()
            ->add('localidad_legal', HiddenType::class, array('label' => 'Localidad', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdLocalidad() : ''))
            ->add('codigo_postal', TextType::class, array('label' => '*Código Postal', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCodigoPostal() : '',
                'attr' => array('maxlength' => 15)))
            ->add('calle', TextType::class, array('label' => 'Calle/Nº de Calle', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCalle() : '',
                'attr' => array('maxlength' => 36)))
            ->add('piso', TextType::class, array('label' => 'Piso', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getPiso() : '',
                'attr' => array('maxlength' => 2),
                'required' => false))
            ->add('departamento', TextType::class, array('label' => 'Dpto', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getDepartamento() : '',
                'attr' => array('maxlength' => 6),
                'required' => false))
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getTelefono() : null, 'attr' => array('maxlength' => 15)))
            ->add('provincias_legal', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices,
                'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdProvincia() : null
            ))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_domicilio_legal.html.twig', array(
            'domicilioFormLegal' => $domicilioForm->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     * Domicilio.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function domicilioRealAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'domicilio_real']);

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

        $repo = $em->getRepository(ProveedorDomicilio::class);
        $proveedorDomicilio = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDomicilio' => 1));

        $sql = "SELECT * FROM provincia";

        $em2 = $this->getDoctrine()->getManager('adif_rrhh');

        $stmt = $em2->getConnection()->prepare($sql);
        $stmt->execute();
        $provincias = $stmt->fetchAll();

        $choices = ['Elija una provincia' => ''];
        foreach ($provincias as $provincia) {
            $choices[$provincia['nombre']] = $provincia['id'];
        }

        $domicilioForm = $this->createFormBuilder()
            ->add('localidad_real', HiddenType::class, array('label' => 'Localidad', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdLocalidad() : '',
                'attr' => array('maxlength' => 36)))
            ->add('codigo_postal', TextType::class, array('label' => '*Código Postal', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCodigoPostal() : '',
                'attr' => array('maxlength' => 15)))
            ->add('calle', TextType::class, array('label' => 'Calle/Nº de Calle', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCalle() : '',
                'attr' => array('maxlength' => 36)))
            ->add('piso', TextType::class, array('label' => 'Piso', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getPiso() : '',
                'attr' => array('maxlength' => 2),
                'required' => false))
            ->add('departamento', TextType::class, array('label' => 'Dpto', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getDepartamento() : '',
                'attr' => array('maxlength' => 6),
                'required' => false))
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getTelefono() : null,  'attr' => array('maxlength' => 20)))
            ->add('provincias_real', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices,
                'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdProvincia() : null
            ))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_domicilio_real.html.twig', array(
            'domicilioFormReal' => $domicilioForm->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     * Domicilio Fiscal.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function domicilioFiscalAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        


        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'domicilio_fiscal']);

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
     
        $repo = $em->getRepository(ProveedorDomicilio::class);
        $proveedorDomicilio = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDomicilio' => 3));
        $repo2 = $em->getRepository(ProveedorDatoPersonal::class);
        $proveedorDatoPersonal = $repo2->findOneBy(array('id' => $idProvDatoPersonal));
//        $proveedorDatoPersonal = $user->getProveedorDatoPersonal();

        $sql = "SELECT * FROM provincia";

        $em2 = $this->getDoctrine()->getManager('adif_rrhh');

        $stmt = $em2->getConnection()->prepare($sql);
        $stmt->execute();
        $provincias = $stmt->fetchAll();

        $choices = ['Elija una provincia' => ''];
        foreach ($provincias as $provincia) {
            $choices[$provincia['nombre']] = $provincia['id'];
        }

        $domicilioFormFiscal = $this->createFormBuilder()
            ->add('localidad_fiscal', HiddenType::class, array('label' => 'Localidad', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdLocalidad() : ''))
            ->add('codigo_postal', TextType::class, array('label' => '*Código Postal', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCodigoPostal() : '',
                'attr' => array('maxlength' => 15)))
            ->add('calle', TextType::class, array('label' => 'Calle/Nº de Calle', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCalle() : '',
                'attr' => array('maxlength' => 36)))
            ->add('piso', TextType::class, array('label' => 'Piso', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getPiso() : '',
                'attr' => array('maxlength' => 2),
                'required' => false))
            ->add('departamento', TextType::class, array('label' => 'Dpto', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getDepartamento() : '',
                'attr' => array('maxlength' => 6),
                'required' => false))
            ->add('provincias_fiscal', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choices,
                'data' => $proveedorDomicilio ? $proveedorDomicilio->getIdProvincia() : null
            ))
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getTelefono() : null,  'attr' => array('maxlength' => 15)))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_domicilio_fiscal.html.twig', array(
            'domicilioFormFiscal' => $domicilioFormFiscal->createView(),
            'datoPersonal' => $proveedorDatoPersonal,
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     * Domicilio Contractual.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function domicilioContractualAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');        
                
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'domicilio_contractual']);

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
        
        $repo = $em->getRepository(ProveedorDomicilio::class);
        $proveedorDomicilio = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDomicilio' => 4));

        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_domicilio_contraactual'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));


        $domicilioContractual = $this->createFormBuilder()
            ->add('codigo_postal', TextType::class, array('label' => '*Código Postal', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCodigoPostal() : '',
                'attr' => array('maxlength' => 15)))
            ->add('calle', TextType::class, array('label' => 'Calle/Nº de Calle', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCalle() : '',
                'attr' => array('maxlength' => 36)))
            ->add('piso', TextType::class, array('label' => 'Piso', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getPiso() : '',
                'attr' => array('maxlength' => 2),
                'required' => false))
            ->add('departamento', TextType::class, array('label' => 'Dpto', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getDepartamento() : '',
                'attr' => array('maxlength' => 6),
                'required' => false))
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getTelefono() : null, 'attr' => array('maxlength' => 15)))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_domicilio_contractual.html.twig', array(
            'domicilioFormContractual' => $domicilioContractual->createView(),
            'documentacionContractual' => $documentacion ? $documentacion : array(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     * Domicilio Exterior.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function domicilioExteriorAction(Request $request,$estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'domicilio_exterior']);

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
                
        $repo = $em->getRepository(ProveedorDomicilio::class);
        $proveedorDomicilio = $repo->findOneBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDomicilio' => 5));
        $repo2 = $em->getRepository(ProveedorDatoPersonal::class);
        $datoPersonal = $repo2->findOneBy(array('id' => $idProvDatoPersonal));

        $sql = "SELECT id , nombre FROM nacionalidad WHERE nombre != 'Argentina'";

        $emAdif = $this->getDoctrine()->getManager('adif_rrhh');

        $stmtc = $emAdif->getConnection()->prepare($sql);
        $stmtc->execute();
        $nacionalidades = $stmtc->fetchAll();

        $nacionalidadesChoices = array();

        foreach ($nacionalidades as $table2ObjN) {
            $nacionalidadesChoices[$table2ObjN['nombre']] = $table2ObjN['id'];
        }


        $domicilioFormExterior = $this->createFormBuilder()
            ->add('provincia_exterior', TextType::class, array('label' => 'Provincia/Estado', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getProvinciaEstadoExterior() : '',
                'attr' => array('maxlength' => 36)))
            ->add('codigo_postal', TextType::class, array('label' => '*Código Postal', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCodigoPostal() : '',
                'attr' => array('maxlength' => 15)))
            ->add('calle', TextType::class, array('label' => 'Calle/Nº de Calle', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getCalle() : '',
                'attr' => array('maxlength' => 36)))
            ->add('piso', TextType::class, array('label' => 'Piso', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getPiso() : '',
                'attr' => array('maxlength' => 2),
                'required' => false))
            ->add('departamento', TextType::class, array('label' => 'Dpto', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getDepartamento() : '',
                'attr' => array('maxlength' => 6),
                'required' => false))
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'data' => $proveedorDomicilio ? $proveedorDomicilio->getTelefono() : null, 'attr' => array('maxlength' => 15)))
            ->add('nacionalidad_exterior', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $nacionalidadesChoices,
                'data' => $datoPersonal != null ? $datoPersonal->getIdPaisRadicacion() : null
//                'disabled' => false
            ))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_domicilio_exterior.html.twig', array(
            'domicilioFormExterior' => $domicilioFormExterior->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));
    }

}