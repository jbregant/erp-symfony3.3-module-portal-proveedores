<?php

namespace GYL\ProveedorBundle\Repository;

/**
 * TipoActividadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TipoActividadRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByDenomPart($codigo)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT t.id, t.denominacion, g.denominacion as denominacion_grupo, s.denominacion as denominacion_seccion 
          FROM tipo_actividad as t LEFT JOIN tipo_actividad_grupo as g ON t.id_grupo=g.id 
          LEFT JOIN  tipo_actividad_seccion as s  ON g.id_seccion= s.id 
          WHERE  t.denominacion REGEXP :denominacion OR t.codigo LIKE :codigo";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('denominacion', str_replace(" ","|",$codigo));
        $stmt->bindValue('codigo', $codigo);

        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

}
