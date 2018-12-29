<?php

namespace GYL\ProveedorBundle\Controller;


use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorTimeline;
use GYL\ProveedorBundle\Entity\TipoTimeline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Proveedor controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{

    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger('name');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../../var/logs/monolog.log', Logger::WARNING));
    }

    /**
     *
     *
     * @Route("/", name="home")
     *
     */
    public function indexAction()
    {
        //return $this->redirectToRoute('fos_user_security_login');
    }

    public function guardarTimeline($denom, $status, $statusId, $idDatoPersonal)
    {

        $em2e = $this->getDoctrine()->getManager('adif_proveedores');
        $tipoTimeLineRepository = $em2e->getRepository(TipoTimeline::class);
        $proveedorTimeLineRepo = $em2e->getRepository(ProveedorTimeline::class);
        $repoDatoPersonal = $em2e->getRepository(ProveedorDatoPersonal::class)->findOneBy(['id' => $idDatoPersonal]);
        $tipoTimeLine = $tipoTimeLineRepository->findOneBy(['denominacion' => $denom]);
        $proveedorTimeLine = $proveedorTimeLineRepo->findOneBy(['idUsuario' => $this->getUser()->getId(), 'denominacion' => $tipoTimeLine->getId(), 'idDatoPersonal' => $idDatoPersonal]);

        if ($proveedorTimeLine instanceof ProveedorTimeline) {
            $proveedorTimeLine->setStatus($status);
            $proveedorTimeLine->setIdStatus($statusId);
            $proveedorTimeLine->setDenominacion($tipoTimeLine);

            $em2e->merge($proveedorTimeLine);
            $em2e->flush();

        } else {
            $timeline = new ProveedorTimeline();
            $timeline->setIdUsuario($this->getUser());
            $timeline->setDenominacion($tipoTimeLine);
            $timeline->setStatus($status);
            $timeline->setIdStatus($statusId);
            $timeline->setIdDatoPersonal($repoDatoPersonal);

            $em2e->persist($timeline);
            $em2e->flush();
        }

    }


}
