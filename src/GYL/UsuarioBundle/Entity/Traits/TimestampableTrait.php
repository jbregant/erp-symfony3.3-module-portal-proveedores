<?php

namespace GYL\UsuarioBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Trait Auditable
 * @author Carlos Sanchez <sanchezcl@gmail.com>
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="fecha_alta")
     */
    protected $fechaAlta;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="fecha_modificacion")
     */
    protected $fechaModificacion;

    /**
     * Sets fechaAlta.
     *
     * @param  \DateTime $fechaAlta
     * @return $this
     */
    public function setFechaAlta(\DateTime $fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Returns fechaAlta.
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }


    /**
     * Sets fechaModificacion.
     *
     * @param  \DateTime $fechaModificacion
     * @return $this
     */
    public function setFechaModificacion(\DateTime $fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Returns fechaModificacion.
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

}
