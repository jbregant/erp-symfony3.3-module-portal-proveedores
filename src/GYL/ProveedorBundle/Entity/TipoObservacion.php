<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoObservacion
 *
 * @ORM\Table(name="tipo_observacion")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoObservacionRepository")
 */
class TipoObservacion extends BaseAuditoria
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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ObservacionEvaluacion", mappedBy="tipoObservacion")
     */
    protected $observacionEvaluacion;

    /**
     * @ORM\ManyToOne(targetEntity="TipoTimeline", inversedBy="tipoObservacion") 
     * @ORM\JoinColumn(name="id_tipo_timeline", referencedColumnName="id", nullable=false)
     */
    protected $tipoTimeline;
    
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
     * @return TipoObservacion
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
    public function getObservacionEvaluacion()
    {
        return $this->observacionEvaluacion;
    }

    /**
     * @param mixed $observacionEvaluacion
     */
    public function setObservacionEvaluacion($observacionEvaluacion)
    {
        $this->observacionEvaluacion = $observacionEvaluacion;
    }
    
    /**
     * @return mixed
     */
    public function getTipoTimeline()
    {
        return $this->tipoTimeline;
    }

    /**
     * @param mixed $tipoTimeline
     *
     * @return self
     */
    public function setTipoTimeline($tipoTimeline)
    {
        $this->tipoTimeline = $tipoTimeline;

        return $this;
    }
}

