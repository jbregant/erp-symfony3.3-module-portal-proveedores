<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TipoIvaInscripto
 *
 * @ORM\Table(name="tipo_iva_inscripto")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoIvaInscriptoRepository")
 */
class TipoIvaInscripto extends BaseAuditoria
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
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string")
     */
    private $denominacion;


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
     * @return TipoIvaInscripto
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

