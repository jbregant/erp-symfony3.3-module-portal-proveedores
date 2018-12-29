<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImpuestoSuss
 *
 * @ORM\Table(name="impuesto_suss")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ImpuestoSussRepository")
 */
class ImpuestoSuss extends BaseAuditoria
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoIva")
     * @ORM\JoinColumn(name="id_tipo_iva", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoIva;

    /**
     * @var bool
     *
     * @ORM\Column(name="personal_a_cargo", type="boolean")
     */
    private $personalACargo;

    /**
     * @var bool
     *
     * @ORM\Column(name="exento", type="boolean")
     */
    private $exento;

    /**
     * @var bool
     *
     * @ORM\Column(name="retencion", type="boolean")
     */
    private $retencion;


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
     * Set personalACargo
     *
     * @param boolean $personalACargo
     *
     * @return ImpuestoSuss
     */
    public function setPersonalACargo($personalACargo)
    {
        $this->personalACargo = $personalACargo;

        return $this;
    }

    /**
     * Get personalACargo
     *
     * @return bool
     */
    public function getPersonalACargo()
    {
        return $this->personalACargo;
    }

    /**
     * Set exento
     *
     * @param boolean $exento
     *
     * @return ImpuestoSuss
     */
    public function setExento($exento)
    {
        $this->exento = $exento;

        return $this;
    }

    /**
     * Get exento
     *
     * @return bool
     */
    public function getExento()
    {
        return $this->exento;
    }

    /**
     * Set retencion
     *
     * @param boolean $retencion
     *
     * @return ImpuestoSuss
     */
    public function setRetencion($retencion)
    {
        $this->retencion = $retencion;

        return $this;
    }

    /**
     * Get retencion
     *
     * @return bool
     */
    public function getRetencion()
    {
        return $this->retencion;
    }

    /**
     * @return int
     */
    public function getTipoIva()
    {
        return $this->tipoIva;
    }

    /**
     * @param int $tipoIva
     */
    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
    }

}

