<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorDomicilio
 *
 * @ORM\Table(name="proveedor_domicilio")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDomicilioRepository")
 */
class ProveedorDomicilio extends BaseAuditoria
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoDomicilio", inversedBy="proveedorDomicilio")
     * @ORM\JoinColumn(name="id_tipo_domicilio", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoDomicilio;
    
    /** 
     * @var integer
     * 
     * @ORM\Column(name="id_pais", type="integer", nullable=true)
     */
    private $idPais;

    /**
     * @var int
     *
     * @ORM\Column(name="id_localidad", type="integer", nullable=true)
     */
    private $idLocalidad;

    /**
     * @var int
     *
     * @ORM\Column(name="id_provincia", type="integer", nullable=true)
     */
    private $idProvincia;


    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=64, nullable=true)
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=64, nullable=true)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="departamento", type="string", length=64, nullable=true)
     */
    private $departamento;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=64, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=64, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia_estado_exterior", type="string", length=255, nullable=true)
     */
    private $provinciaEstadoExterior;


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
     * @return ProveedorDomicilio
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
     * @return ProveedorDomicilio
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
     * Set idLocalidad
     *
     * @param integer $idLocalidad
     *
     * @return ProveedorDomicilio
     */
    public function setIdLocalidad($idLocalidad)
    {
        $this->idLocalidad = $idLocalidad;

        return $this;
    }

    /**
     * Get idLocalidad
     *
     * @return int
     */
    public function getIdLocalidad()
    {
        return $this->idLocalidad;
    }

    /**
     * Set codigoPostal
     *
     * @param string $codigoPostal
     *
     * @return ProveedorDomicilio
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set calle
     *
     * @param string $calle
     *
     * @return ProveedorDomicilio
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set Departamento
     *
     * @param string $departamento
     *
     * @return ProveedorDomicilio
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get Departamento
     *
     * @return string
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set piso
     *
     * @param string $piso
     *
     * @return ProveedorDomicilio
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set telefono
     *
     * @param string telefono
     *
     * @return ProveedorDomicilio
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @return int
     */
    public function getIdProvincia()
    {
        return $this->idProvincia;
    }

    /**
     * @param int $idProvincia
     * @return ProveedorDomicilio
     */
    public function setIdProvincia($idProvincia)
    {
        $this->idProvincia = $idProvincia;
        return $this;
    }

    /**
     * @return string
     */
    public function getProvinciaEstadoExterior()
    {
        return $this->provinciaEstadoExterior;
    }

    /**
     * @param string $provinciaEstadoExterior
     * @return ProveedorDomicilio
     */
    public function setProvinciaEstadoExterior($provinciaEstadoExterior)
    {
        $this->provinciaEstadoExterior = $provinciaEstadoExterior;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoDomicilio()
    {
        return $this->tipoDomicilio;
    }

    /**
     * @param int $tipoDomicilio
     */
    public function setTipoDomicilio($tipoDomicilio)
    {
        $this->tipoDomicilio = $tipoDomicilio;
    }
    
    public function setIdPais($idPais)
    {
        $this->idPais = $idPais;
    }
    
    public function getIdPais()
    {
        return $this->idPais;
    }
}
