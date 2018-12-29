<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TipoTimeline
 *
 * @ORM\Table(name="tipo_timeline")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoTimelineRepository")
 */
class TipoTimeline extends BaseAuditoria
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
     * @ORM\Column(name="denominacion", type="string", length=255)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="TipoObservacion", mappedBy="tipoTimeline")
     */
    private $tipoObservacion;

    /**
     * @var int
     * @ORM\Column(name="id_gerencia", type="integer")
     */
    private $idGerencia;

    /**
     * Get idGerencia
     *
     * @return int
     */
    public function getIdGerencia()
    {
        return $this->idGerencia;
    }
    
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
     * @return TipoTimeline
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
     * Set tipoObservacion
     *
     * @return TipoTimeline
     */
    public function setTipoObservacion($tipoObservacion)
    {
        $this->tipoObservacion = $tipoObservacion;

        return $this;
    }

    /**
     * Get tipoObservacion
     *
     * @return TipoObservacion
     */
    public function getTipoObservacion()
    {
        return $this->tipoObservacion;
    }
}

