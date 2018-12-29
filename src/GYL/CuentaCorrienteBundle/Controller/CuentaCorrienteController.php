<?php

namespace GYL\CuentaCorrienteBundle\Controller;

use GYL\UsuarioBundle\Entity\Usuario;
use mPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GYL\ProveedorBundle\Entity\NotificacionUsuario;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Cuenta corriente controller.
 *
 * @Route("/")
 */
class CuentaCorrienteController extends Controller {

    /**
     * Obtener la cuenta corriente del usuario logueado
     *
     * @param Request request
     * @Route("/cuentacorriente", name="cuenta_corriente_index")
     */
    public function indexAction(Request $request) {

        $user = $this->getUser();
        $notificaciones = $this->container->get('app.repository.usuario')->getNotificaciones($user->getId());
        // Obtengo los datos de contacto del usuario logueado.
        $em = $this->getDoctrine()->getManager('adif_proveedores');

        $repo = $em->getRepository(ProveedorDatoPersonal::class);
//        $datosUsuario = $em->getRepository(ProveedorDatoPersonal::class)->findBy(
//                ['idUsuario' => $this->getUser()->getId()]);
        $repoUsuario           = $em->getRepository(Usuario::class)->findOneBy(['id' => $user->getId()]);
        $datosUsuario = $repoUsuario->getProveedorDatoPersonal();
        $proveedores = $this->container->get('app.repository.usuario')->getListaProveedores($this->getUser()->getUsername());
        $idProveedor = $proveedores[0]['idProveedor'];
        $registros = $this->container->get('app.repository.cuenta_corriente')->getCuentaCorriente($idProveedor, $user);

        return $this->render('GYLCuentaCorrienteBundle:Default:index.html.twig', array(
                    'notificaciones' => $notificaciones,
                    'registros' => $registros,
                    'proveedores' => $proveedores,
                    'idProveedor' => $idProveedor,
                    'datosUsuario' => $datosUsuario));
    }

    /**
     * Obtener la cuenta corriente del proveedor seleccionado
     *
     * @param Request request
     * @Route("/cuentacorrienteajax", name="cuenta_corriente_ajax")
     */
    public function ajaxAction(Request $request) {

        //si el usuario no está logueado o no es una petición ajax o el parámetro no existe, arroja excepción
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ||
                !$request->request->has('idProveedor') ||
                !$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $idProveedor = $request->request->get('idProveedor');
        $user = $this->getUser();

        $registros = $this->container->get('app.repository.cuenta_corriente')->getCuentaCorriente($idProveedor, $user);

        return $this->render('GYLCuentaCorrienteBundle:Partials:table.html.twig', array(
                    'registros' => $registros,
                    'idProveedor' => $idProveedor));
    }

    /**
     * Actualizar la notificacion a leido
     *
     * @param Request request
     * @Route("/notificacionajax", name="notificacion_ajax")
     */
    public function notificacionAction(Request $request) {
        $idNotificacion = $request->get('idNotificacion');
        $user = $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager('adif_proveedores');
        $repoDatos = $em->getRepository(NotificacionUsuario::class)->findBy(
                ['usuarioIdusuario' => $user, 'notificacionIdnotificacion' => $idNotificacion]);
        
        $notificaciones = $this->container->get('app.repository.usuario')->getNotificaciones($user);

        foreach ($repoDatos as $notificacion) {
            $notificacion->setLeido(1); // leido
            $notificacion->setFechaHora(new \DateTime('now'));
            $em->persist($notificacion);
        }

        $em->flush();

        return new JsonResponse([
            'id' => $idNotificacion,
            'notificaciones' => $notificaciones
        ]);
    }

    /**
     * Exportar a PDF la cuenta corriente del proveedor seleccionado
     *
     * @Route("/cuentacorriente/print/{idProveedor}", name="cuenta_corriente_print")
     * @Method("GET")
     *
     */
    public function printAction($idProveedor) {

        $proveedores = $this->container->get('app.repository.usuario')->getListaProveedores($this->getUser()->getUsername());

        //si el proveedor no pertenece al listado
        if (!in_array($idProveedor, array_column($proveedores, 'idProveedor'))) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        $user = $this->getUser();
        $registros = $this->container->get('app.repository.cuenta_corriente')->getCuentaCorriente($idProveedor, $user);

        $key = array_search($idProveedor, array_column($proveedores, 'idProveedor'));
        $proveedor = $proveedores[$key]['cuit'] . ' - ' . $proveedores[$key]['razonSocial'];

        $render = $this->renderView('GYLCuentaCorrienteBundle:Partials:pdf.html.twig', array(
            'registros' => $registros,
            'proveedor' => $proveedor));

        $adif = 'ADIF S.E.';

        $mpdfService = new Mpdf();

        $headerFooter = '<table style="width: 100%; font-size: 65%;">
                <tr>
                    <td style="width: 33%">{DATE j-m-Y}</td>
                    <td style="width: 33%; text-align: center;">{PAGENO}/{nbpg}</td>
                    <td style="width: 33%; text-align: right; overflow: hidden; white-space: nowrap;">' . $proveedor . '</td>
                </tr>
            </table>';

        $mpdfService->SetHTMLHeader($headerFooter);
        $mpdfService->SetHTMLFooter($headerFooter);
        $mpdfService->SetAuthor($adif);
        $mpdfService->SetCreator($adif);
        $mpdfService->SetSubject($adif);
        $mpdfService->SetTitle($proveedor);
        $mpdfService->WriteHTML($render);

        return $mpdfService->Output($proveedor . '.pdf', 'D');
    }

    /**
     * Exportar a PDF la cuenta corriente del proveedor seleccionado
     *
     * @param Request request
     * @Route("/cuentacorriente/printordenpago/", name="cuenta_corriente_print_orden_pago")
     *
     */
    public function printOrdenPagoAction(Request $request) {

        //si el usuario no está logueado o no es una petición ajax o el parámetro no existe, arroja excepción
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $idProveedor = $request->get('idProveedor');
        $user = $this->getUser();
        $proveedores = $this->container->get('app.repository.usuario')->getListaProveedores($user->getUsername());

        //si el proveedor no pertenece al listado
        if (!in_array($idProveedor, array_column($proveedores, 'idProveedor'))) {
            throw $this->createAccessDeniedException();
        }

        $pathValido = true;

        if (in_array($this->container->getParameter('kernel.environment'), ['dev'], true)) {
            $host = $this->container->getParameter('app_ADIF_SIGA_dev');
        } else {
            $host = $this->container->getParameter('app_ADIF_SIGA_prod');
        }

        $discriminador = $request->get('discriminador');

        switch ($discriminador) {
            case 'orden_pago_pago_parcial':
                $path = "/ordenpago/pagoparcial/print/";
                break;
            case 'orden_pago_obra':
                $path = "/ordenpago/obra/print/";
                break;
            case 'orden_pago_comprobante':
                $path = "/ordenpago/comprobante/print/";
                break;
            default:
                $pathValido = false;
        }

        //si el path no es válido
        if (!$pathValido) {
            throw $this->createAccessDeniedException();
        }

        $opValida = false;
        $idOP = $request->get('id');
        $registros = $this->container->get('app.repository.cuenta_corriente')->getCuentaCorriente($idProveedor, $user);
        $ordenesPago = array_column($registros, 'ordenesPago');

        foreach ($ordenesPago as $ordenPago) {

            foreach ($ordenPago as $comprobantes) {

                foreach ($comprobantes as $comprobante) {

                    if ($idOP === $comprobante['idOrdenPago']) {
                        $opValida = true;
                    }
                }
            }
        }

        //si la OP no pertenece al proveedor
        if (!$opValida) {
            throw $this->createAccessDeniedException();
        }

        $url = $host . $path . $idOP;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseSiga = curl_exec($ch);

        if (!curl_errno($ch)) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($responseSiga, 0, $headerSize);
            curl_close($ch);

            if (preg_match('/filename="([^ ]+)"/', $header, $matches)) {
                $filename = $matches[1];
            } else {
                $filename = "orden_pago";
            }

            $response = new Response($responseSiga);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-disposition', 'attachment; filename="' . $filename . '";');
            return $response;
        } else {
            throw new NotFoundHttpException("Error al obtener orden de pago");
        }
    }

}
