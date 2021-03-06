<?php

namespace GYL\UsuarioBundle\Repository;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioRepository
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container                         = $container;
        $this->entityManager['adif_compras']     = $container->get("doctrine")->getManager('adif_compras');
        $this->entityManager['adif_proveedores'] = $container->get("doctrine")->getManager('adif_proveedores');
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager($manager)
    {
        return $this->entityManager[$manager];
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * Obtener lista de proveedores con datos perosnales
     *
     * @param type string
     * @return type entity
     */
    public function getListaProveedores($email)
    {
        $em  = $this->getEntityManager('adif_compras');
        $sql = "SELECT
                    p.id AS idProveedor,
                    contp.nombre AS nombre,
                    cp.cuit,
                    cp.razon_social AS razonSocial,
                    cp.codigo_identificacion AS cdi,
                    LOWER(dc.descripcion) AS email,
                    cp.es_extranjero
                FROM
                    proveedor p
                INNER JOIN
                    contacto_proveedor contp
                ON
                    p.id = contp.id_proveedor
                INNER JOIN
                    cliente_proveedor cp
                ON
                    p.id_cliente_proveedor = cp.id
                INNER JOIN
                    contacto_proveedor_dato_contacto cpdc
                ON
                    contp.id = cpdc.id_contacto_proveedor
                INNER JOIN
                    dato_contacto dc
                ON
                    cpdc.id_dato_contacto = dc.id
                WHERE
                    dc.id_tipo_contacto = 3 AND
                    p.id_estado_proveedor = 1 AND
                    dc.descripcion = :email
               ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->execute();
        return $result = $stmt->fetchAll();
    }

    /**
     * Obtener todas las notificaciones del usuario logueado
     *
     * @param type integer
     * @return type entity
     */
    public function getNotificaciones($id)
    {
        $em  = $this->getEntityManager('adif_proveedores');
        $sql = "SELECT DISTINCT
                    no.id, no.titulo, no.autor, no.mensaje, no.fecha_desde, no.fecha_hasta, nu.leido
                FROM
                    notificacion_usuario nu
                JOIN
                    notificacion no 
                ON 
                    nu.notificacion_idnotificacion = no.id
                WHERE
                    DATE(NOW()) >= no.fecha_desde AND 
                    DATE(NOW()) <= no.fecha_hasta AND 
                    no.estado_id = 1 AND 
                    nu.usuario_idusuario = :id
                ORDER BY no.id DESC 
                -- LIMIT 1
                ";

        $em = $em->getConnection()->prepare($sql);
        $em->bindValue('id', $id);
        $em->execute();
        return $result = $em->fetchAll();
    }

    /**
     * Validar código captcha
     *
     * @param type string
     * @return type json
     */
    public function captchaverify($recaptcha)
    {
        $url    = $this->container->getParameter('recaptcha_url');
        $secret = $this->container->getParameter('recaptcha_private_key');
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret"   => $secret,
            "response" => $recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data;
    }

    /**
     * Setear id de usuario cuando se está registrando el usuario
     *
     * @param type string
     * @return type integer
     */
    public function setIdDatoPersonal($email)
    {
        $em = $this->getEntityManager('adif_proveedores');

        $sql =
            "UPDATE
                proveedor_dato_personal AS dp
            SET
                dp.id_usuario = (SELECT
                                    id
                                FROM
                                    usuario
                                WHERE
                                    email LIKE :email
                                )
            WHERE
                dp.id_usuario IS NULL AND
                dp.email LIKE :email";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * busca asociacion entre un usuario y la preinscripcion
     *
     * @param type integer
     * @param type integer
     * @return type integer
     */
    public function buscarAsociacion($idPdp, $idUser)
    {
        $em = $this->getEntityManager('adif_proveedores');

        $sql =
            "SELECT * FROM usuario_dato_personal WHERE proveedor_dato_personal_id = :idpdp AND usuario_id = :iduser";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('idpdp', $idPdp);
        $stmt->bindValue('iduser',$idUser);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *  crea asociacion entre usuario y proveedor dato personal a lo cabeza xq no se hacerlo con las entities xD y no tengo tiempo de averiguarlo
     *
     * @param type integer
     * @param type integer
     * @return type integer
     */
    public function crearAsociacion($idPdp, $idUser)
    {
        $em = $this->getEntityManager('adif_proveedores');

        $sql =
            "INSERT INTO usuario_dato_personal (proveedor_dato_personal_id, usuario_id) VALUES (:idpdp, :iduser)";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('idpdp', $idPdp);
        $stmt->bindValue('iduser',$idUser);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
