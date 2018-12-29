<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorDatoPersonal
 *
 * @ORM\Table(name="proveedor_dato_personal")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDatoPersonalRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class ProveedorDatoPersonal extends BaseAuditoria
{

    /**
     * constantes para tipos de documento
     */
    const DNI = "DNI";
    const EXTRANJERO = "EXTRANJERO";
//    const CI = "CI";
//    const PASAPORTE = "PASAPORTE";


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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=true)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_documento", type="string", length=255, nullable=true)
     */
    private $tipoDocumento;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_documento", type="string", length=255, nullable=true)
     * @Assert\Regex("/[0-9](8)/")
     */
    private $numeroDocumento;

    /**
     * @var GYL\UsuarioBundle\Entity\Usuario
     *
     * @ORM\ManyToMany(targetEntity="GYL\UsuarioBundle\Entity\Usuario", inversedBy="proveedorDatoPersonal")
     * @JoinTable(name="usuario_dato_personal")
     * @Assert\NotBlank()
     */
    private $idUsuario;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoPersona", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_persona", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoPersona;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoPersonaJuridica", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_persona_juridica", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoPersonaJuridica;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoProveedor", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_proveedor", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoProveedor;

    /**
    * @var bigint
     *
    * @ORM\Column(name="cuit", type="string", length=13, nullable=true)
    * @Assert\Regex("/[0-9](2)-[0-9](8)-[0-9](1)/")
    */
    private $cuit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="proveedor", type="boolean", nullable=false)
     */
    private $proveedor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="extranjero", type="boolean", nullable=true)
     */
    private $extranjero;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_id_tributaria", type="string", length=255, nullable=true)
     */
    private $numeroIdTributaria;

    /**
     * @var int
     *
     * @ORM\Column(name="id_pais_radicacion", type="integer", nullable=true)
     */
    private $idPaisRadicacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio_actividades", type="date", nullable=true)
     */
    private $fechaInicioActividades;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_web", type="string", length=255, nullable=true)
     */
    private $direccionWeb;


    /**
     * @ORM\OneToMany(targetEntity="ProveedorPersonaJuridicaMiembros", mappedBy="proveedorDatoPersonal")
     */
    private $proveedorPersonaJuridicaMiembros;

    /**
     * @ORM\Column(name="proveedor_id", type="integer", nullable=true)
     */
    private $idProveedorAsoc;
    
    //------------------------------------------------------------//
    /**
     * @ORM\OneToMany(targetEntity="ProveedorDatoContacto", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoContacto;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorActividad", mappedBy="idDatoPersonal")
     */
    private $proveedorActividad;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDomicilio", mappedBy="idDatoPersonal")
     */
    private $proveedorDomicilio;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoImpositivos", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoImpositivo;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRubro", mappedBy="idDatoPersonal")
     */
    private $proveedorRubro;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorEvaluacion", mappedBy="idDatoPersonal")
     */
    private $proveedorEvaluacion;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorUte", mappedBy="idDatoPersonal")
     */
    private $proveedorUte;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoBancario", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoBancario;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoGcshm", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoGcshm;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRepresentanteApoderado", mappedBy="idDatoPersonal")
     */
    private $proveedorRepresentanteApoderado;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDocumentacion", mappedBy="idDatoPersonal")
     */
    private $proveedorDocumentacion;

    /**
     * @ORM\OneToMany(targetEntity="GYL\ProveedorBundle\Entity\BackupUserData", mappedBy="idDatoPersonal")
     */
    private $backupUserData;

    public function __construct() {
        $this->idUsuario = new ArrayCollection();
    }

    /**
     * Get backupUserData
     *
     * @return string
     */
    public function getBackupUserData()
    {
        return $this->backupUserData;
    }

    /**
     * Set backupUserData
     *
     * @return ProveedorDatoPersonal
     */
    public function setBackupUserData($data)
    {
        $this->backupUserData = $data;

        return $this;
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ProveedorDatoPersonal
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
     * @return ProveedorDatoPersonal
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
     * Set tipoDocumento
     *
     * @param string $tipoDocumento
     *
     * @return ProveedorDatoPersonal
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return string
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     *
     * @return ProveedorDatoPersonal
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Get IdUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getId_Usuario()
    {
        return $this->idUsuario;
    }
    
    public function addIdUsuario($idUsuario){
        $this->idUsuario[] = $idUsuario;
    }

    /**
     *
     * @return int
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set Cuit
     *
     * @param int $cuit
     *
     * @return ProveedorDatoPersonal
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
        return $this;
    }

    /**
     * @return bool
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @param bool $proveedor
     * @return ProveedorDatoPersonal
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * @return bool
     */
    public function esProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @return bool
     */
    public function getExtranjero()
    {
        return $this->extranjero;
    }

    /**
     * @param bool $extranjero
     * @return ProveedorDatoPersonal
     */
    public function setExtranjero($extranjero)
    {
        $this->extranjero = $extranjero;
        return $this;
    }

    /**
     * @return bool
     */
    public function esExtranjero()
    {
        return $this->extranjero;
    }

    /**
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * @param string $razonSocial
     * @return ProveedorDatoPersonal
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumeroIdTributaria()
    {
        return $this->numeroIdTributaria;
    }

    /**
     * @param string $numeroIdTributaria
     * @return ProveedorDatoPersonal
     */
    public function setNumeroIdTributaria($numeroIdTributaria)
    {
        $this->numeroIdTributaria = $numeroIdTributaria;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdPaisRadicacion()
    {
        return $this->idPaisRadicacion;
    }

    /**
     * @param int $idPaisRadicacion
     * @return ProveedorDatoPersonal
     */
    public function setIdPaisRadicacion($idPaisRadicacion)
    {
        $this->idPaisRadicacion = $idPaisRadicacion;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFechaInicioActividades()
    {
        return $this->fechaInicioActividades;
    }

    /**
     * @param \DateTime $fechaInicioActividades
     * @return ProveedorDatoPersonal
     */
    public function setFechaInicioActividades($fechaInicioActividades)
    {
        $this->fechaInicioActividades = $fechaInicioActividades;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return ProveedorDatoPersonal
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getDireccionWeb()
    {
        return $this->direccionWeb;
    }

    /**
     * @param string $direccionWeb
     *
     * @return ProveedorDatoPersonal
     */
    public function setDireccionWeb($direccionWeb)
    {
        $this->direccionWeb = $direccionWeb;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoPersonaJuridica()
    {
        return $this->tipoPersonaJuridica;
    }

    /**
     * @param int $tipoPersonaJuridica
     */
    public function setTipoPersonaJuridica($tipoPersonaJuridica)
    {
        $this->tipoPersonaJuridica = $tipoPersonaJuridica;
    }

    /**
     * @return int
     */
    public function getTipoProveedor()
    {
        return $this->tipoProveedor;
    }

    /**
     * @param TipoProveedor $tipoProveedor
     */
    public function setTipoProveedor($tipoProveedor)
    {
        $this->tipoProveedor = $tipoProveedor;
    }

    /**
     * @return int
     */
    public function getTipoPersona()
    {
        return $this->tipoPersona;
    }

    /**
     * @param TipoPersona $tipoPersona
     */
    public function setTipoPersona($tipoPersona)
    {
        $this->tipoPersona = $tipoPersona;
    }
    
    public function getProveedorActividad() {
        return $this->proveedorActividad;
    }

    public function setProveedorActividad($proveedorActividad) {
        $this->proveedorActividad = $proveedorActividad;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacion()
    {
        return $this->proveedorEvaluacion;
    }

    /**
     * @param mixed $proveedorEvaluacion
     *
     * @return self
     */
    public function setProveedorEvaluacion($proveedorEvaluacion)
    {
        $this->proveedorEvaluacion = $proveedorEvaluacion;

        return $this;
    }

    public function getProveedorDomicilio() {
        return $this->proveedorDomicilio;
    }

    public function setProveedorDomicilio($proveedorDomicilio) {
        $this->proveedorDomicilio = $proveedorDomicilio;
        return $this;
    }

    public function getProveedorRubro()
    {
        return $this->proveedorRubro;
    }

    public function setProveedorRubro($proveedorRubro)
    {
        $this->proveedorRubro = $proveedorRubro;
    }

    public function getProveedorDatoContacto() {
        return $this->proveedorDatoContacto;
    }

    public function getProveedorDatoImpositivo()
    {
        return $this->proveedorDatoImpositivo;
    }

    public function setProveedorDatoImpositivo($proveedorDatoImpositivo)
    {
        $this->proveedorDatoImpositivo = $proveedorDatoImpositivo;
    }

    public function getProveedorDatoBancario()
    {
        return $this->proveedorDatoBancario;
    }

    public function setProveedorDatoBancario($proveedorDatoBancario)
    {
        $this->proveedorDatoBancario = $proveedorDatoBancario;
    }

    public function getProveedorUte()
    {
        return $this->proveedorUte;
    }

    public function setProveedorUte($proveedorUte)
    {
        $this->proveedorUte = $proveedorUte;
    }

    public function getProveedorRepresentanteApoderado()
    {
        return $this->proveedorRepresentanteApoderado;
    }

    public function setProveedorRepresentanteApoderado($proveedorRepresentanteApoderado)
    {
        $this->proveedorRepresentanteApoderado = $proveedorRepresentanteApoderado;
    }
    public function getProveedorDocumentacion()
    {
        return $this->proveedorDocumentacion;
    }

    public function setProveedorDocumentacion($proveedorDocumentacion)
    {
        $this->proveedorDocumentacion = $proveedorDocumentacion;
    }

    public function getProveedorDatoGcshm()
    {
        return $this->proveedorDatoGcshm;
    }

    public function setProveedorDatoGcshm($proveedorDatoGcshm)
    {
        $this->proveedorDatoGcshm = $proveedorDatoGcshm;
    }

    public function getIdProveedorAsoc()
    {
        return $this->idProveedorAsoc;
    }

    public function setIdProveedorAsoc($idProveedor)
    {
        $this->idProveedorAsoc = $idProveedor;
        return $this;
    }

    public function getUsuarioDatosContacto()
    {
        return $this->usuarioDatosContacto;
    }

}
