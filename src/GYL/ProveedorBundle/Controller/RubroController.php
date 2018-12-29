<?php

namespace GYL\ProveedorBundle\Controller;

use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorRubro;
use GYL\ProveedorBundle\Entity\Rubro;
use GYL\ProveedorBundle\Entity\RubroClase;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Rubro controller.
 *
 * @Route("/")
 */
class RubroController extends BaseController
{

    /**
     * Buscar RubroClases.
     *
     * @Route("/formulariopreinscripcion/listarclases", name="listar_clases")
     * @Method("POST")
     */
    public function listarClases(Request $request)
    {


        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $palabra = $request->request->get('palabra');
            $id = $request->request->get('id');


            if ($palabra != null) {


                //BUSCAR Clase
                $em = $this->getDoctrine()->getManager('adif_proveedores');

                $result = $em->getRepository(RubroClase::class)->createQueryBuilder('o')
                    ->where('o.rubro = :id')
                    ->andWhere('o.denominacion LIKE :palabra')
                    ->setParameter('id', $id)
                    ->setParameter('palabra', '%' . $palabra . '%')
                    ->getQuery()
                    ->getResult();

                $objects = $result->getResult();

                foreach ($objects as $obj) {
                    $output[] = array('id' => $obj->getId(), 'denominacion' => $obj->getDenominacion());
                }

                return new JsonResponse($output);

            } else {


                $em = $this->getDoctrine()->getManager('adif_proveedores');


                $result = $em->getRepository(RubroClase::class)->createQueryBuilder('o')
                    ->where('o.rubro = :id')
                    ->setParameter('id', $id)
                    ->getQuery();

                $objects = $result->getResult();

                foreach ($objects as $obj) {
                    $output[] = array('id' => $obj->getId(), 'denominacion' => $obj->getDenominacion());
                }

                return new JsonResponse($output);

            }
        } catch
        (Exception $e) {
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
            'data' => $data
        ]);

    }


    /**
     * Agregar RubroClases.
     *
     * @Route("/formulariopreinscripcion/agregarclases", name="agregar_clases")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarClasesAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoRubro = $em->getRepository(RubroClase::class);
            $user = $this->getUser();
            $id = $request->request->get('id');
            $idDatoPersonal = $request->request->get('idDatoPersonal');


            $rubroClase = $em->getRepository(RubroClase::class)->findOneBy([
                'id' => $id
            ]);

            $queryRubros = $repoRubro->createQueryBuilder('r')
                ->select(array('r.denominacion rubro','cat.denominacion categoria'))
                ->innerJoin('ProveedorBundle:Rubro', 'cat', 'WITH', 'r.rubro = cat.id')
                ->where('r.id IN (:ids)')
                ->setParameter('ids', $id)
                ->getQuery();

            $rubros = $queryRubros->getResult();

            $repoProveedorDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $proveedorDatoPersonal = $repoProveedorDatoPersonal->findOneBy(['id' => $idDatoPersonal]);

            $entity = $em->getRepository(ProveedorRubro::class)->findOneBy(['rubroClase' => $rubroClase, 'idDatoPersonal' => $idDatoPersonal]);

            if ($entity == null) {
                $pr = New ProveedorRubro();
                $pr->setIdUsuario($user);
                $pr->setRubroClase($rubroClase);
                $pr->setIdDatoPersonal($proveedorDatoPersonal);
                $em->persist($pr);
                $em->flush();
                $this->guardarTimeline('timeline_rubro', 'completo', 1, $idDatoPersonal);

                $output = array('id' => $pr->getId(), 'data' => $rubros);
                return new JsonResponse($output);

            }
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy1';

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy2';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        } catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
            $data = 'Emtpy3';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }

        return new JsonResponse([
            'sts' => $response,
            'msg' => $msg,
            'data' => $data
        ]);


    }

    /**
     * Eliminar ProveedorRubro.
     *
     * @Route("/formulariopreinscripcion/quitarrubro", name="quitar_rubro")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function quitarRubro(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoPR = $em->getRepository(ProveedorRubro::class);

            $rubroClase = $em->getRepository(RubroClase::class)->findOneBy([
                'id' => $data['id']
            ]);

            $prs = $repoPR->findBy(['rubroClase' => $rubroClase]);

            foreach ($prs as $pr) {
                $em->remove($pr);
                $em->flush();
            }

            if ($data['rubros'] == 'true') {
                $this->guardarTimeline('timeline_rubro', 'completo', 1, $data['idDatoPersonal']);
            } else {
                $this->guardarTimeline('timeline_rubro', 'incompleto', 2, $data['idDatoPersonal']);
            }

            $response = 200;
            $msg = 'ok';
            $data = 'ok';

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
            'data' => $data
        ]);


    }

    /**
     * Rubro.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function rubroAction(Request $request,$estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'rubro']);

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
     
        $repo = $em->getRepository(ProveedorRubro::class);
        $proveedorRubro = $repo->findBy(array('idDatoPersonal' => $idProvDatoPersonal));
        $repoDocumentacion = $em->getRepository(ProveedorDocumentacion::class);
        $repoTipoDocumentacion = $em->getRepository(TipoDocumentacion::class);
        $tipoDoc = $repoTipoDocumentacion->findOneBy(array('denominacion' => 'proveedor_rubro'));
        $documentacion = $repoDocumentacion->findBy(array('idDatoPersonal' => $idProvDatoPersonal, 'tipoDocumentacion' => $tipoDoc->getId()));
        $rubroIds = array();

        foreach ($proveedorRubro as $prS) {
            array_push($rubroIds, $prS->getRubroClase());
        }

        $repo2 = $em->getRepository(RubroClase::class);

        $queryRubros = $repo2->createQueryBuilder('r')
            ->select(array('r.id','r.denominacion rubro','cat.denominacion categoria'))
            ->innerJoin('ProveedorBundle:Rubro', 'cat', 'WITH', 'r.rubro = cat.id')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $rubroIds)
            ->getQuery();
        $rubros = $queryRubros->getResult();

        $rubrosDelUser = $this->createFormBuilder()
            ->add('userId', HiddenType::class, array('data' => $user->getId()))
            ->add('rubroId', HiddenType::class, array())
            ->getForm();


        $selectRubros = $this->createFormBuilder()
            ->add('categoria', EntityType::class, array(
                'mapped' => false,
                'class' => 'GYL\ProveedorBundle\Entity\Rubro',
                'choice_label' => 'denominacion',
            ))
            ->add('rubro', ChoiceType::class, array('disabled' => true))->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_rubro.html.twig', array(
            'actividadesForm' => $rubrosDelUser->createView(),
            'rubros' => $rubros,
            'documentacionRubro' => $documentacion ? $documentacion : array(),
            'selectRubros' => $selectRubros->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

    /**
     *
     * Crea la sesion cuando se actualiza algun dato en galo
     *
     * @Route("/formulariopreinscripcion/actualiza-galo", name="galo_sesion")
     * @Method("GET|POST")
     */
    public function galoSesion(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $request->getSession()->set('galo', true);
            $this->guardarTimeline('timeline_rubro', 'completo', 1, key($data));
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


}