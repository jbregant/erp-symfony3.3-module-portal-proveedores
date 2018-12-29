<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorEvaluacion;
use GYL\ProveedorBundle\Entity\TipoObservacion;
use GYL\ProveedorBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as UniqueConstrait;


/**
 * DatosUte controller.
 *
 * @Route("/")
 */
class PersonaFisicaExtranjeraController extends BaseController
{
    /**
     * Agregar Persona Fisica .
     *
     * @Route("/formulariopreinscripcion/agregarpersonafisicaextranjera", name="agregar_persona_fisica_extranjera")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function agregarPersonaFisicaAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $user = $this->getUser();
            $repo = $em->getRepository(ProveedorDatoPersonal::class);
            $dp = $repo->findOneBy(['id' => $data['form']['id_dato_personal']]);

            if ($dp instanceof ProveedorDatoPersonal) {


                if (isset($data['form']['nacionalidad_persona_fisica_extranjera'])) {
                    $dp->setIdPaisRadicacion($data['form']['nacionalidad_persona_fisica_extranjera']);
                }
                $dp->setNombre($data['form']['nombre_persona_fisica_extranjera']);
                $dp->setApellido($data['form']['apellido_persona_fisica_extranjera']);
                $dp->setTipoDocumento($data['form']['tipo_documento_persona_fisica_extranjera']);
                $dp->setNumeroDocumento($data['form']['numero_documento_persona_fisica_extranjera']);
                $dp->setIdPaisRadicacion($data['form']['nacionalidad_persona_fisica_extranjera']);
                $em->merge($dp);
                $em->flush();
                $response = 200;
                $msg = 'ok';

                $this->guardarTimeline('timeline_persona_fisica_extranjera', 'completo', 1, $data['form']['id_dato_personal']);

            }


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
     * Datos Persona FisicaExtranjera .
     *
     * @param Request $request
     *
     * @return Response
     */
    public function datosPersonaFisicaExtranjeraAction(Request $request, $estadoEvaluacionGral, $idProvDatoPersonal, $unlockForm)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('No tienes acceso a esta sección');
        }
        ($estadoEvaluacionGral) ? $estadoEvaluacionGral: 0;

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        
        $proveedorEvaluacion = $em->getRepository(ProveedorEvaluacion::class)->findOneBy(['idDatoPersonal' => $idProvDatoPersonal]);
        $tipoObsevacion = $em->getRepository(TipoObservacion::class)->findOneBy(['denominacion' => 'datos_persona_fisica_extranjera']);

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

        $repo = $em->getRepository(ProveedorDatoPersonal::class);
        $persona = $repo->findOneBy(array('id' => $idProvDatoPersonal));

        $sql = "SELECT id , nombre FROM nacionalidad";

        $emAdif = $this->getDoctrine()->getManager('adif_rrhh');

        $stmtc = $emAdif->getConnection()->prepare($sql);
        $stmtc->execute();
        $nacionalidades = $stmtc->fetchAll();

        $nacionalidadesChoices = array();

        foreach ($nacionalidades as $table2ObjN) {
            $nacionalidadesChoices[$table2ObjN['nombre']] = $table2ObjN['id'];
        }

        $sql = "SELECT * FROM tipo_documento";
        $emRrhh = $this->getDoctrine()->getManager('adif_rrhh');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        $tipos_documentos = $stmt->fetchAll();

        $choicesTipoDoc = [];
        foreach ($tipos_documentos as $table2Obj) {
            $choicesTipoDoc[$table2Obj['nombre']] = $table2Obj['id'];
        }

        $form = $this->createFormBuilder()
            ->add('nombre_persona_fisica_extranjera', TextType::class, array('label' => '*Nombre', 'data' => $persona ? $persona->getNombre() : ''))
            ->add('apellido_persona_fisica_extranjera', TextType::class, array('label' => '*Apellido', 'data' => $persona ? $persona->getApellido() : ''))
            ->add('tipo_documento_persona_fisica_extranjera', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $choicesTipoDoc,
                'placeholder' => 'Seleccione una opcion',
                'data' => $persona ? $persona->getTipoDocumento() : null,
                'choice_attr' => function ($val, $key, $index) {//se disablea la opcion 'DNI'
                    return ($key == ProveedorDatoPersonal::DNI) ? array('disabled' => true): array('disabled' => false);
                }
            ))
            ->add('numero_documento_persona_fisica_extranjera', TextType::class, array('label' => 'Numero de Documento', 'data' => $persona ? $persona->getNumeroDocumento() : ''))
            ->add('nacionalidad_persona_fisica_extranjera', ChoiceType::class, array(
                'mapped' => false,
                'choices' => $nacionalidadesChoices,
                'data' => $persona != null ? $persona->getIdPaisRadicacion() : null
            ))
            ->getForm();


        return $this->render('ProveedorBundle::Preinscripcion/panel_persona_fisica_extranjera.html.twig', array(
            'formPersonaFisicaExtranjera' => $form->createView(),
            'estadoEvaluacionGral' => $estadoEvaluacionGral,
            'observacionEvaluacion' => $observacionEvaluacion,
            'unlockForm' => $unlockForm
        ));

    }

}
