<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImpuestoGanancias
 *
 * @ORM\Table(name="impuesto_ganancias")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ImpuestoGananciasRepository")
 */
class ImpuestoGanancias extends BaseAuditoria
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
     * @var string
     *
     * @ORM\Column(name="otros", type="string", length=255)
     */
    private $otros;


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
     * Set exento
     *
     * @param boolean $exento
     *
     * @return ImpuestoGanancias
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
     * @return ImpuestoGanancias
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
     * Set otros
     *
     * @param string $otros
     *
     * @return ImpuestoGanancias
     */
    public function setOtros($otros)
    {
        $this->otros = $otros;

        return $this;
    }

    /**
     * Get otros
     *
     * @return string
     */
    public function getOtros()
    {
        return $this->otros;
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

