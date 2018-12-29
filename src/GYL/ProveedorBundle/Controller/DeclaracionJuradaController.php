<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDeclaracionJurada;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Declaracion Jurada controller.
 *
 * @Route("/")
 */
class DeclaracionJuradaController extends BaseController
{
    /**
     * Declaracion Jurada.
     *
     * @Route("/formulariopreinscripcion/declaracionjurada", name="declaracion_jurada")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarUteAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repoDdjj = $em->getRepository(ProveedorDeclaracionJurada::class);
            $exist = $repoDdjj->findOneBy(['idDatoPersonal' => $data['form']['id_dato_personal']]);
            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $data['form']['id_dato_personal']]);

            $acepta = isset($data['form']['acepta']) ? 1 : 0; //variable que verifica si se tildo o destildo el check

            if ($exist instanceof ProveedorDeclaracionJurada) {
                // $exist->setAcepta(isset($data['form']['acepta']) ? 1 : 0); OLD
                $exist->setAcepta($acepta);
                $em->persist($exist);
                $em->flush();

                // se agrego esta validacion para verificar si destilda la declaracion jurada y se vea reflejada en el timeline
                if($acepta == 1){
                    $this->guardarTimeline('timeline_ddjj', 'completo', 1, $data['form']['id_dato_personal']);
                }elseif ($acepta == 0) {
                    $this->guardarTimeline('timeline_ddjj', 'incompleto', 2, $data['form']['id_dato_personal']);
                }

            } else {
                $dj = New ProveedorDeclaracionJurada();
                $dj->setIdUsuario($user = $this->getUser()->getId());
                $dj->setAcepta($acepta);
                $dj->setIdDatoPersonal($provDatoPersonal);
                $em->persist($dj);
                $em->flush();

                // se agrego esta validacion para verificar si destilda la declaracion jurada y se vea reflejada en el timeline
                if($acepta == 1){
                    $this->guardarTimeline('timeline_ddjj', 'completo', 1, $data['form']['id_dato_personal']);
                }elseif ($acepta == 0) {
                    $this->guardarTimeline('timeline_ddjj', 'incompleto', 2, $data['form']['id_dato_personal']);
                }
            }

            //$this->guardarTimeline('timeline_ddjj', 'completo', 1); OLD
            
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
            'msg' => $msg
        ]);


    }

    /**
     * Declaracion Jurada.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function declaracionJuradaAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repo = $em->getRepository(ProveedorDeclaracionJurada::class);
        $ddjj = $repo->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $form = $this->createFormBuilder()
            ->add('acepta', CheckboxType::class, array('label' => ' ', 'data' => $ddjj != null ? $ddjj->getAcepta() == 1 ? true : false : null))                
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_ddjj.html.twig', array(
            'formDeclaracionJurada' => $form->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'unlockForm' => $unlockForm
        ));

    }


    /**
     * @Route("/ddjjdetalles", name="ddjj-detalles")
     */
    public function hipervinculoAction()
    {

        return $this->render('ProveedorBundle::Preinscripcion/declaracion_jurada.html.twig');
    }


}
