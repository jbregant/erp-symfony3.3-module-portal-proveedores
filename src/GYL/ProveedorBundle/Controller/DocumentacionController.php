<?php

namespace GYL\ProveedorBundle\Controller;


use Exception;
use FOS\UserBundle\Model\UserInterface;
use GYL\ProveedorBundle\Entity\ProveedorDatoPersonal;
use GYL\ProveedorBundle\Entity\ProveedorDocumentacion;
use GYL\ProveedorBundle\Entity\TipoDocumentacion;
use GYL\ProveedorBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Documentacion controller.
 *
 * @Route("/")
 */
class DocumentacionController extends BaseController
{
    /**
     * Agregar Documento.
     *
     * @Route("/formulariopreinscripcion/subirdocumentacion/{panel}", name="subir_documentacion")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function subirDocumentacionAction(Request $request, $panel)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {
            $file = $request->files->get('doc');
            $idDatoPersonal = $request->request->get('idDatoPersonal');

            $em = $this->getDoctrine()->getManager('adif_proveedores');

            $repo = $em->getRepository(TipoDocumentacion::class);
            $tipoDoc = $repo->findOneBy(['denominacion' => $panel]);

            $repoProvDatoPersonal = $em->getRepository(ProveedorDatoPersonal::class);
            $provDatoPersonal = $repoProvDatoPersonal->findOneBy(['id' => $idDatoPersonal]);


            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('upload_directory'),
                $filename
            );

            $document = new ProveedorDocumentacion();
            $document->setIdUsuario($this->getUser());
            $document->setFile($file);
            $document->setIdTipoDocumentacion($tipoDoc);
            $document->setPath($filename);
            $document->setPublicfilename($file->getClientOriginalName());
            $document->setIdDatoPersonal($provDatoPersonal);

            $em->persist($document);
            $em->flush();


            $data = array('id' => $document->getId(), 'path' => $document->getPath(), 'nombreoriginal' => $document->getPublicfilename());
            $response = 200;
            $msg = 'ok';

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $data = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }catch (\Throwable $e) {
            $response = 500;
            $msg = 'error';
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
     * Quitar  Documento.
     *
     * @Route("/formulariopreinscripcion/eliminardocumento/{id}", name="quitar_documento")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function quitarDocumentoAction(Request $request, $id)
    {

        if (!$request->isXmlHttpRequest()) {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }

        try {

            $em = $this->getDoctrine()->getManager('adif_proveedores');
            $repoD = $em->getRepository(ProveedorDocumentacion::class);
            $doc = $repoD->findOneBy(['id' => $id]);

            $em->remove($doc);
            $em->flush();


            $response = 200;
            $msg = 'ok';

        } catch (Exception $e) {
            $response = 500;
            $msg = 'error';
            $this->logger->critical('500', array(
                'cause' => $e->getMessage()
            ));
        }catch (\Throwable $e) {
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
     * @Route("/uploads/documentacion/{file}")
     * @return binary file
     */
    public function documentosAction($file)
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'].'/uploads/documentacion/'.$file;

        if (!file_exists($filePath))
            throw new FileNotFoundException($file);

        return $this->file($filePath);
    }


}
