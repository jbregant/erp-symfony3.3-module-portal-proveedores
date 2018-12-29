<?php

namespace GYL\UsuarioBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Trait Auditable
 * @author Carlos Sanchez <sanchezcl@gmail.com>
 *
 */
trait SoftdeleteableTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="fecha_baja")
     */
    protected $fechaBaja;

    /**
     * Sets fechaBaja.
     *
     * @param \Datetime|null $fechaBaja
     *
     * @return $this
     */
    public function setFechaBaja(\DateTime $fechaBaja = null)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Returns fechaBaja.
     *
     * @return \DateTime
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted()
    {
        return null !== $this->fechaBaja;
    }

}
