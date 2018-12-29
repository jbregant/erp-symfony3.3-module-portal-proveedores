<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rubro
 *
 * @ORM\Table(name="rubro")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\RubroRepository")
 */
class Rubro extends BaseAuditoria
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
     * @ORM\OneToMany(targetEntity="RubroClase", mappedBy="rubro")
     */
    private $rubroClase;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
    public function getRubroClase()
    {
        return $this->rubroClase;
    }

    /**
     * @param mixed $rubroClase
     */
    public function setRubroClase($rubroClase)
    {
        $this->rubroClase = $rubroClase;
    }

}

