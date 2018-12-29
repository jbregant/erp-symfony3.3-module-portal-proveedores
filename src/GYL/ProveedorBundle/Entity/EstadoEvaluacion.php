<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * EstadoEvaluacion
 *
 * @ORM\Table(name="estado_evaluacion")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\EstadoEvaluacionRepository")
 */
class EstadoEvaluacion extends BaseAuditoria
{
    /**
     * @var integer
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
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacion", mappedBy="estadoEvaluacion")
     */
    protected $proveedorEvaluacion;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return EstadoEvaluacion
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
    public function getProveedorEvaluacion()
    {
        return $this->proveedorEvaluacion;
    }

    public function setProveedorEvaluacion($proveedorEvaluacion)
    {
        $this->proveedorEvaluacion = $proveedorEvaluacion;
    }
}

