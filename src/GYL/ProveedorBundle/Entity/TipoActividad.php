<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoActividad
 *
 * @ORM\Table(name="tipo_actividad")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoActividadRepository")
 */
class TipoActividad extends BaseAuditoria
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
     * @ORM\ManyToOne(targetEntity="TipoActividadGrupo", inversedBy="tipoActividad")
     * @ORM\JoinColumn(name="id_grupo", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=16, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=1024, nullable=false)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorActividad", mappedBy="tipoActividad")
     */
    private $proveedorActividad;



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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return TipoActividad
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     *
     * @return TipoActividad
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
    public function getProveedorActividad()
    {
        return $this->proveedorActividad;
    }

    /**
     * @param mixed $proveedorActividad
     */
    public function setProveedorActividad($proveedorActividad)
    {
        $this->proveedorActividad = $proveedorActividad;
    }

    /**
     * @return int
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * @param int $grupo
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;
    }
}

