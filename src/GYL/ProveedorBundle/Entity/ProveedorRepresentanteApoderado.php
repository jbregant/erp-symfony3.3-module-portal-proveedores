<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ProveedorRepresentanteApoderado
 *
 * @ORM\Table(name="proveedor_representante_apoderado")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorRepresentanteApoderadoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 */
class ProveedorRepresentanteApoderado extends BaseAuditoria
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
     * @var GYL\UsuarioBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="GYL\UsuarioBundle\Entity\Usuario")
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=64)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit_cuil", type="string", length=11)
     */
    private $cuitCuil;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_documento", type="string", nullable=true)
     */
    private $tipoDocumento;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string")
     */
    private $numeroDocumento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_designacion", type="datetime")
     */
    private $fechaDesignacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="representante", type="boolean", nullable=true)
     */
    private $representante;

    /**
     * @var bool
     *
     * @ORM\Column(name="apoderado", type="boolean", nullable=true)
     */
    private $apoderado;

    /**
     * @var bool
     *
     * @ORM\Column(name="poder_judicial", type="boolean", nullable=true)
     */
    private $poderJudicial;

    /**
     * @var bool
     *
     * @ORM\Column(name="bancario", type="boolean", nullable=true)
     */
    private $bancario;

    /**
     * @var bool
     *
     * @ORM\Column(name="adm_especial", type="boolean", nullable=true)
     */
    private $admEspecial;

    /**
     * @var bool
     *
     * @ORM\Column(name="adm_general", type="boolean", nullable=true)
     */
    private $admGeneral;


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
     * Set idUsuario
     *
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorRepresentanteApoderado
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set cuitCuil
     *
     * @param string $cuitCuil
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setCuitCuil($cuitCuil)
    {
        $this->cuitCuil = $cuitCuil;

        return $this;
    }

    /**
     * Get cuitCuil
     *
     * @return string
     */
    public function getCuitCuil()
    {
        return $this->cuitCuil;
    }

    /**
     * Set tipoDocumento
     *
     * @param integer $tipoDocumento
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return int
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param integer $numeroDocumento
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return int
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set fechaDesignacion
     *
     * @param \DateTime $fechaDesignacion
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setFechaDesignacion($fechaDesignacion)
    {
        $this->fechaDesignacion = $fechaDesignacion;

        return $this;
    }

    /**
     * Get fechaDesignacion
     *
     * @return \DateTime
     */
    public function getFechaDesignacion()
    {
        return $this->fechaDesignacion;
    }

    /**
     * Set representante
     *
     * @param boolean $representante
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setRepresentante($representante)
    {
        $this->representante = $representante;

        return $this;
    }

    /**
     * Get representante
     *
     * @return bool
     */
    public function getRepresentante()
    {
        return $this->representante;
    }

    /**
     * Set apoderado
     *
     * @param boolean $apoderado
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setApoderado($apoderado)
    {
        $this->apoderado = $apoderado;

        return $this;
    }

    /**
     * Get apoderado
     *
     * @return bool
     */
    public function getApoderado()
    {
        return $this->apoderado;
    }

    /**
     * Set poderJudicial
     *
     * @param boolean $poderJudicial
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setPoderJudicial($poderJudicial)
    {
        $this->poderJudicial = $poderJudicial;

        return $this;
    }

    /**
     * Get poderJudicial
     *
     * @return bool
     */
    public function getPoderJudicial()
    {
        return $this->poderJudicial;
    }

    /**
     * Set bancario
     *
     * @param boolean $bancario
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setBancario($bancario)
    {
        $this->bancario = $bancario;

        return $this;
    }

    /**
     * Get bancario
     *
     * @return bool
     */
    public function getBancario()
    {
        return $this->bancario;
    }

    /**
     * Set admEspecial
     *
     * @param boolean $admEspecial
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setAdmEspecial($admEspecial)
    {
        $this->admEspecial = $admEspecial;

        return $this;
    }

    /**
     * Get admEspecial
     *
     * @return bool
     */
    public function getAdmEspecial()
    {
        return $this->admEspecial;
    }

    /**
     * Set admGeneral
     *
     * @param boolean $admGeneral
     *
     * @return ProveedorRepresentanteApoderado
     */
    public function setAdmGeneral($admGeneral)
    {
        $this->admGeneral = $admGeneral;

        return $this;
    }

    /**
     * Get admGeneral
     *
     * @return bool
     */
    public function getAdmGeneral()
    {
        return $this->admGeneral;
    }

}

