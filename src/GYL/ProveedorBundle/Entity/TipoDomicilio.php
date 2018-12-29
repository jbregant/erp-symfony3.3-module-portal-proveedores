<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TipoDomicilio
 *
 * @ORM\Table(name="tipo_domicilio")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoDomicilioRepository")
 */
class TipoDomicilio extends BaseAuditoria
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="proveedorDomicilio", mappedBy="tipoDomicilio")
     */
    protected $proveedorDomicilio;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     *
     * @return TipoDomicilio
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * @return mixed
     */
    public function getProveedorDomicilio()
    {
        return $this->proveedorDomicilio;
    }

    /**
     * @param mixed $proveedorDomicilio
     */
    public function setProveedorDomicilio($proveedorDomicilio)
    {
        $this->proveedorDomicilio = $proveedorDomicilio;
    }
}

