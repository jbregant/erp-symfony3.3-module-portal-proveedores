<?php

namespace GYL\ProveedorBundle\Repository;

/**
 * UserXProveedorDatoPersonalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserXProveedorDatoPersonalRepository extends \Doctrine\ORM\EntityRepository
{
    public function findComboProveedoresData($idUsuario)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT
       pdp.id AS id,
       pdp.cuit AS cuit,
       pdp.numero_id_tributaria AS numeroIdTributaria,
       pdp.razon_social AS razonSocial,
       pdp.nombre AS nombre,
       pdp.apellido AS apellido
FROM
     adif_proveedores.proveedor_dato_personal pdp
            INNER JOIN
         adif_proveedores.usuario_x_proveedor_dato_personal updp
         ON pdp.id = updp.proveedor_dato_personal_id
WHERE
    updp.usuario_id = :idUsuario;";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('idUsuario', $idUsuario);

        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}