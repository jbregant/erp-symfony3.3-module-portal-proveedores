<?php

namespace GYL\ProveedorBundle\Entity;

use GYL\ProveedorBundle\Entity\BaseEliminadoLogico as BaseEliminadoLogico;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BaseAuditoria
 *
 * @ORM\MappedSuperclass
 */
class BaseAuditoria extends BaseEliminadoLogico
{

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=false)
     */
    protected $fechaCreacion;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="fecha_ultima_actualizacion", type="datetime", nullable=false)
     */
    protected $fechaUltimaActualizacion;

    /**
     * @ORM\Column(name="id_usuario_creacion", type="integer", nullable=true)
     */
    protected $idUsuarioCreacion;

    /**
     * @ORM\Column(name="id_usuario_ultima_modificacion", type="integer", nullable=true)
     */
    protected $idUsuarioUltimaModificacion;

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set eliminado
     *
     * @param \DateTime $fechaCreacion
     * @return BaseAuditoria
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacion
     *
     * @return \DateTime
     */
    public function getUltimaActualizacion()
    {
        return $this->fechaUltimaActualizacion;
    }

    /**
     * Set eliminado
     *
     * @param \DateTime $fechaUltimaActualizacion
     * @return BaseAuditoria
     */
    public function setFechaUltimaActualizacion($fechaUltimaActualizacion)
    {
        $this->fechaUltimaActualizacion = $fechaUltimaActualizacion;

        return $this;
    }

    /**
     *
     * @return type
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     *
     * @return type
     */
    public function getIdUsuarioUltimaModificacion()
    {
        return $this->idUsuarioUltimaModificacion;
    }

}