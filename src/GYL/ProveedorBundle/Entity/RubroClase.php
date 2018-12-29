<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * RubroClase
 *
 * @ORM\Table(name="rubro_clase")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\RubroClaseRepository")
 */
class RubroClase extends BaseAuditoria
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
     * @ORM\ManyToOne(targetEntity="Rubro", inversedBy="rubroClase")
     * @ORM\JoinColumn(name="id_rubro", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $rubro;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRubro", mappedBy="rubroClase")
     */
    private $proveedorRubro;


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
     * @return RubroClase
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
    public function getRubro()
    {
        return $this->rubro;
    }

    /**
     * @param int $rubro
     */
    public function setRubro($rubro)
    {
        $this->rubro = $rubro;
    }

    /**
     * @return mixed
     */
    public function getProveedorRubro()
    {
        return $this->proveedorRubro;
    }

    /**
     * @param mixed $proveedorRubro
     */
    public function setProveedorRubro($proveedorRubro)
    {
        $this->proveedorRubro = $proveedorRubro;
    }

}

