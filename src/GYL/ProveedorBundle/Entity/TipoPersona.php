<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TipoPersona
 *
 * @ORM\Table(name="tipo_persona")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoPersonaRepository")
 */
class TipoPersona extends BaseAuditoria
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
     * @ORM\Column(name="denominacion", type="string", length=64, nullable=false)
     * @Assert\NotBlank() 
     * 
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="proveedorDatoPersonal", mappedBy="tipoPersona")
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
     * @return TipoPersona
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
}

