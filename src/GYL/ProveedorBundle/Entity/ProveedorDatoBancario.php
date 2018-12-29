<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * ProveedorDatoBancario
 *
 * @ORM\Table(name="proveedor_dato_bancario")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDatoBancarioRepository")
 */
class ProveedorDatoBancario extends BaseAuditoria
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
     * @var bool
     *
     * @ORM\Column(name="cuenta_local", type="boolean")
     */
    private $cuentaLocal;

    /**
     * @var int
     *
     * @ORM\Column(name="id_entidad_bancaria", type="integer", nullable=true)
     */
    private $idEntidadBancaria;

    /**
     * @var string
     *
     * @ORM\Column(name="sucursal_bancaria", type="string", length=64, nullable=true)
     */
    private $sucursalBancaria;

    /**
     * @var int
     *
     * @ORM\Column(name="numero_sucursal", type="integer", nullable=true)
     */
    private $numeroSucursal;

    /**
     * @var string
     *
     * @ORM\Column(name="cbu", type="string", length=22, nullable=true)
     */
    private $cbu;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_cuenta", type="string", length=14, nullable=true)
     */
    private $numeroCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="swift", type="string", length=255, nullable=true)
     */
    private $swift;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=true)
     */
    private $tipoMoneda;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad_extranjero", type="string", nullable=true)
     */
    private $localidadExtranjero;

    /**
     * @var string
     *
     * @ORM\Column(name="aba", type="string", length=64, nullable=true)
     */
    private $aba;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=64, nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiario", type="string", length=64, nullable=true)
     */
    private $beneficiario;

    /**
     * @var string
     *
     * @ORM\Column(name="banco_corresponsal", type="string", length=64, nullable=true)
     */
    private $bancoCorresponsal;

    /**
     * @var string
     *
     * @ORM\Column(name="swift_banco_corresponsal", type="string", length=64, nullable=true)
     */
    private $swiftBancoCorresponsal;


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
     * @return ProveedorDatoBancario
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
     * @return ProveedorActividad
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
     * Set cuentaLocal
     *
     * @param boolean $cuentaLocal
     *
     * @return ProveedorDatoBancario
     */
    public function setCuentaLocal($cuentaLocal)
    {
        $this->cuentaLocal = $cuentaLocal;

        return $this;
    }

    /**
     * Get cuentaLocal
     *
     * @return bool
     */
    public function getCuentaLocal()
    {
        return $this->cuentaLocal;
    }

    /**
     * Set idEntidadBancaria
     *
     * @param integer $idEntidadBancaria
     *
     * @return ProveedorDatoBancario
     */
    public function setIdEntidadBancaria($idEntidadBancaria)
    {
        $this->idEntidadBancaria = $idEntidadBancaria;

        return $this;
    }

    /**
     * Get idEntidadBancaria
     *
     * @return int
     */
    public function getIdEntidadBancaria()
    {
        return $this->idEntidadBancaria;
    }

    /**
     * Set sucursalBancaria
     *
     * @param string $sucursalBancaria
     *
     * @return ProveedorDatoBancario
     */
    public function setSucursalBancaria($sucursalBancaria)
    {
        $this->sucursalBancaria = $sucursalBancaria;

        return $this;
    }

    /**
     * Get sucursalBancaria
     *
     * @return string
     */
    public function getSucursalBancaria()
    {
        return $this->sucursalBancaria;
    }

    /**
     * Set numeroSucursal
     *
     * @param integer $numeroSucursal
     *
     * @return ProveedorDatoBancario
     */
    public function setNumeroSucursal($numeroSucursal)
    {
        $this->numeroSucursal = $numeroSucursal;

        return $this;
    }

    /**
     * Get numeroSucursal
     *
     * @return int
     */
    public function getNumeroSucursal()
    {
        return $this->numeroSucursal;
    }

    /**
     * Set cbu
     *
     * @param integer $cbu
     *
     * @return ProveedorDatoBancario
     */
    public function setCbu($cbu)
    {
        $this->cbu = $cbu;

        return $this;
    }

    /**
     * Get cbu
     *
     * @return int
     */
    public function getCbu()
    {
        return $this->cbu;
    }

    /**
     * Set numeroCuenta
     *
     * @param integer $numeroCuenta
     *
     * @return ProveedorDatoBancario
     */
    public function setNumeroCuenta($numeroCuenta)
    {
        $this->numeroCuenta = $numeroCuenta;

        return $this;
    }

    /**
     * Get numeroCuenta
     *
     * @return int
     */
    public function getNumeroCuenta()
    {
        return $this->numeroCuenta;
    }

    /**
     * Set swift
     *
     * @param string $swift
     *
     * @return ProveedorDatoBancario
     */
    public function setSwift($swift)
    {
        $this->swift = $swift;

        return $this;
    }

    /**
     * Get swift
     *
     * @return string
     */
    public function getSwift()
    {
        return $this->swift;
    }

    /**
     * Set tipoMoneda
     *
     * @param integer $tipoMoneda
     *
     * @return ProveedorDatoBancario
     */
    public function setTipoMoneda($tipoMoneda)
    {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return int
     */
    public function getTipoMoneda()
    {
        return $this->tipoMoneda;
    }

    /**
     * @return string
     */
    public function getLocalidadExtranjero()
    {
        return $this->localidadExtranjero;
    }

    /**
     * @param string $localidadExtranjero
     * @return ProveedorDatoBancario
     */
    public function setLocalidadExtranjero($localidadExtranjero)
    {
        $this->localidadExtranjero = $localidadExtranjero;
        return $this;
    }

    /**
     * @return string
     */
    public function getAba()
    {
        return $this->aba;
    }

    /**
     * @param string $aba
     * @return ProveedorDatoBancario
     */
    public function setAba($aba)
    {
        $this->aba = $aba;
        return $this;
    }

    /**
     * @param string $swiftBancoCorresponsal
     * @return ProveedorDatoBancario
     */
    public function setSwiftBancoCorresponsal($swiftBancoCorresponsal)
    {
        $this->swiftBancoCorresponsal = $swiftBancoCorresponsal;
        return $this;
    }

    /**
     * @return string
     */
    public function getSwiftBancoCorresponsal()
    {
        return $this->swiftBancoCorresponsal;
    }

    /**
     * @param string $bancoCorresponsal
     * @return ProveedorDatoBancario
     */
    public function setBancoCorresponsal($bancoCorresponsal)
    {
        $this->bancoCorresponsal = $bancoCorresponsal;
        return $this;
    }

    /**
     * @return string
     */
    public function getBancoCorresponsal()
    {
        return $this->bancoCorresponsal;

    }

    /**
     * @return string
     */
    public function getBeneficiario()
    {
        return $this->beneficiario;
    }

    /**
     * @param string $beneficiario
     * @return ProveedorDatoBancario
     */
    public function setBeneficiario($beneficiario)
    {
        $this->beneficiario = $beneficiario;
        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     * @return ProveedorDatoBancario
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }
}

