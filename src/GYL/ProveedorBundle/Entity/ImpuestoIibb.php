<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ImpuestoIibb
 *
 * @ORM\Table(name="impuesto_iibb")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ImpuestoIibbRepository")
 */
class ImpuestoIibb  extends BaseAuditoria
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoIvaInscripto")
     * @ORM\JoinColumn(name="id_tipo_iva_inscripto", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idTipoIvaInscripto;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inscripcion", type="string", length=13)
     */
    private $numeroInscripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="id_jurisdiccion", type="integer")
     */
    private $jurisdiccion;

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
     * Set idTipoIvaInscripto
     *
     * @param integer $idTipoIvaInscripto
     *
     * @return ImpuestoIIbb
     */
    public function setIdTipoIvaInscripto($idTipoIvaInscripto)
    {
        $this->idTipoIvaInscripto = $idTipoIvaInscripto;

        return $this;
    }

    /**
     * Get idTipoIvaInscripto
     *
     * @return int
     */
    public function getIdTipoIvaInscripto()
    {
        return $this->idTipoIvaInscripto;
    }

    /**
     * Set numeroInscripcion
     *
     * @param integer $numeroInscripcion
     *
     * @return ImpuestoIIbb
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;

        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return int
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Set jurisdiccion
     *
     * @param integer $jurisdiccion
     *
     * @return ImpuestoIIbb
     */
    public function setJurisdiccion($jurisdiccion)
    {
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return int
     */
    public function getJurisdiccion()
    {
        return $this->jurisdiccion;
    }

    /**
     * Set exento
     *
     * @param boolean $exento
     *
     * @return ImpuestoIIbb
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
     * @return ImpuestoIIbb
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
     * @return ImpuestoIIbb
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

