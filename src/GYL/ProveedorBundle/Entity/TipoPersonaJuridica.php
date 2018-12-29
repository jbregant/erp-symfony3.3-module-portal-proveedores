<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TipoPersonaJuridica
 *
 * @ORM\Table(name="tipo_persona_juridica")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoPersonaJuridicaRepository")
 */
class TipoPersonaJuridica extends BaseAuditoria
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
     * @var TipoPersona
     *
     * @ORM\ManyToOne(targetEntity="TipoPersona")
     * @ORM\JoinColumn(name="id_tipo_persona", referencedColumnName="id", nullable=false)
     */
    private $tipoPersona;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="proveedorDatoPersonal", mappedBy="tipoPersonaJuridica")
     */
    protected $proveedorDatoPersonal;


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
     * @return TipoPersonaJuridica
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
     * @return TipoPersona
     */
    public function getTipoPersona()
    {
        return $this->tipoPersona;
    }

    /**
     * @param TipoPersona $tipoPersona
     */
    public function setTipoPersona($tipoPersona)
    {
        $this->tipoPersona = $tipoPersona;
    }
}

