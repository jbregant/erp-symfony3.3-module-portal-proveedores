<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ute
 *
 * @ORM\Table(name="proveedor_ute")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\UteRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class ProveedorUte extends BaseAuditoria
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
     * @ORM\Column(name="denominacion", type="string", length=50, nullable=false, unique=true)
     */
    private $denominacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_constitucion", type="date", nullable=true)
     */
    private $fechaConstitucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_finalizacion", type="date", nullable=true)
     */
    private $fechaFinalizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inscripcion", type="string", length=50, nullable=true, unique=true)
     */
    private $numeroInscripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=50, nullable=true, unique=true)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=120, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_fantasia", type="string", length=50, nullable=true)
     */
    private $nombreFantasia;

    /**
     * @var GYL\UsuarioBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="GYL\UsuarioBundle\Entity\Usuario")     * 
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
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
     * @ORM\OneToMany(targetEntity="ProveedorUteMiembros", mappedBy="ute")
     */
    protected $proveedorUteMiembros;


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
     * @return ProveedorUte
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
     * Set fechaConstitucion
     *
     * @param \DateTime $fechaConstitucion
     *
     * @return ProveedorUte
     */
    public function setFechaConstitucion($fechaConstitucion)
    {
        $this->fechaConstitucion = $fechaConstitucion;

        return $this;
    }

    /**
     * Get fechaConstitucion
     *
     * @return \DateTime
     */
    public function getFechaConstitucion()
    {
        return $this->fechaConstitucion;
    }

    /**
     * Set fechaFinalizacion
     *
     * @param \DateTime $fechaFinalizacion
     *
     * @return ProveedorUte
     */
    public function setFechaFinalizacion($fechaFinalizacion)
    {
        $this->fechaFinalizacion = $fechaFinalizacion;

        return $this;
    }

    /**
     * Get fechaFinalizacion
     *
     * @return \DateTime
     */
    public function getFechaFinalizacion()
    {
        return $this->fechaFinalizacion;
    }

    /**
     * Set numeroInscripcion
     *
     * @param string $numeroInscripcion
     *
     * @return ProveedorUte
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;

        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return string
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return ProveedorUte
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return ProveedorUte
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set nombreFantasia
     *
     * @param string $nombreFantasia
     *
     * @return ProveedorUte
     */
    public function setNombreFantasia($nombreFantasia)
    {
        $this->nombreFantasia = $nombreFantasia;

        return $this;
    }

    /**
     * Get nombreFantasia
     *
     * @return string
     */
    public function getNombreFantasia()
    {
        return $this->nombreFantasia;
    }

    /**
     * Set IdUsuario
     *
     * @param integer $idUsuario
     *
     * @return ProveedorUte
     */
    public function setId_Usuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get Id_Usuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getId_Usuario()
    {
        return $this->idUsuario;
    }

    /**
     * @return mixed
     */
    public function getProveedorUteMiembros()
    {
        return $this->proveedorUteMiembros;
    }

    /**
     * @param mixed $proveedorUteMiembros
     */
    public function setProveedorUteMiembros($proveedorUteMiembros)
    {
        $this->proveedorUteMiembros = $proveedorUteMiembros;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorUte
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
}

