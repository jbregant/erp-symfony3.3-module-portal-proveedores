<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Timeline
 *
 * @ORM\Table(name="proveedor_timeline")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TimelineRepository")
 */
class ProveedorTimeline extends BaseAuditoria
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
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var TipoTimeline
     *
     * @ORM\ManyToOne(targetEntity="TipoTimeline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_denominacion", referencedColumnName="id")
     * })
     */
    private $denominacion;

    /**
     * @var int
     *
     * @ORM\Column(name="id_status", type="integer")
     */
    private $idStatus;

    /**
     * @var GYL\UsuarioBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="GYL\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var GYL\ProveedorBundle\Entity\ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="GYL\ProveedorBundle\Entity\ProveedorDatoPersonal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

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
     * Set status
     *
     * @param string $status
     *
     * @return ProveedorTimeline
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set idStatus
     *
     * @param int $idStatus
     *
     * @return ProveedorTimeline
     */
    public function setIdStatus($idStatus)
    {
        $this->idStatus = $idStatus;

        return $this;
    }

    /**
     * Get idStatus
     *
     * @return int
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     * @return ProveedorTimeline
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorTimeline
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return GYL\ProveedorBundle\Entity\ProveedorDatoPersonal
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }

    /**
     * @return TipoTimeline
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * @param TipoTimeline $denominacion
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    }
}

