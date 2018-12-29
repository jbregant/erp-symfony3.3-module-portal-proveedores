<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoActividadGrupo
 *
 * @ORM\Table(name="tipo_actividad_grupo")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoActividadGrupoRepository")
 */
class TipoActividadGrupo extends BaseAuditoria
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
     * @ORM\Column(name="codigo", type="string", length=16, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoActividadSeccion", inversedBy="tipoActividadGrupo")
     * @ORM\JoinColumn(name="id_seccion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $seccion;

    /**
     * @ORM\OneToMany(targetEntity="TipoActividad", mappedBy="grupo")
     */
    private $tipoActividad;


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
     * @return TipoActividadGrupo
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
     * @return TipoActividadGrupo
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
    public function getTipoActividad()
    {
        return $this->tipoActividad;
    }

    /**
     * @param mixed $tipoActividad
     */
    public function setTipoActividad($tipoActividad)
    {
        $this->tipoActividad = $tipoActividad;
    }

    /**
     * @return int
     */
    public function getSeccion()
    {
        return $this->seccion;
    }

    /**
     * @param int $seccion
     */
    public function setSeccion($seccion)
    {
        $this->seccion = $seccion;
    }

}

