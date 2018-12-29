<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoActividadSeccion
 *
 * @ORM\Table(name="tipo_actividad_seccion")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoActividadSeccionRepository")
 */
class TipoActividadSeccion extends BaseAuditoria
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
     * @ORM\OneToMany(targetEntity="TipoActividadGrupo", mappedBy="seccion")
     */
    private $tipoActividadGrupo;


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
     * @return TipoActividadSeccion
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
     * @return TipoActividadSeccion
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
    public function getTipoActividadGrupo()
    {
        return $this->tipoActividadGrupo;
    }

    /**
     * @param mixed $tipoActividadGrupo
     */
    public function setTipoActividadGrupo($tipoActividadGrupo)
    {
        $this->tipoActividadGrupo = $tipoActividadGrupo;
    }
}

