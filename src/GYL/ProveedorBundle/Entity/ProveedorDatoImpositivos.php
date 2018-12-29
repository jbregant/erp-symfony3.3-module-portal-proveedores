<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GYL\UsuarioBundle\GYLUsuarioBundle;

/**
 * ProveedorDatoImpositivos
 *
 * @ORM\Table(name="proveedor_dato_impositivo")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDatoImpositivosRepository")
 */
class ProveedorDatoImpositivos extends BaseAuditoria
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
     * @var ImpuestoIva
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoIva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_iva", referencedColumnName="id", nullable=false)
     * })
     */
    private $proveedorIva;

    /**
     * @var ImpuestoSuss
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoSuss")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_suss", referencedColumnName="id", nullable=false)
     * })
     */
    private $idProveedorSuss;

    /**
     * @var ImpuestoGanancias
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoGanancias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_ganancias", referencedColumnName="id", nullable=false)
     * })
     */
    private $idProveedorGanancias;

    /**
     * @var ImpuestoIibb
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoIibb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_iibb", referencedColumnName="id", nullable=false)
     * })
     */
    private $idProveedorIibb;

    /**
     * @var bool
     *
     * @ORM\Column(name="cae", type="boolean")
     */
    private $cae;

    /**
     * @var bool
     *
     * @ORM\Column(name="cai", type="boolean")
     */
    private $cai;

    /**
     * @var string
     *
     * @ORM\Column(name="otros", type="string", length=255, nullable=true)
     */
    private $otros;


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
     * @return ProveedorDatoImpositivos
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
     * @return ProveedorDatoImpositivos
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
     * Set idProveedorSuss
     *
     * @param ImpuestoSuss $idProveedorSuss
     *
     * @return ProveedorDatoImpositivos
     */
    public function setIdProveedorSuss($idProveedorSuss)
    {
        $this->idProveedorSuss = $idProveedorSuss;

        return $this;
    }

    /**
     * Get idProveedorSuss
     *
     * @return ImpuestoSuss
     */
    public function getIdProveedorSuss()
    {
        return $this->idProveedorSuss;
    }

    /**
     * Set idProveedorGanancias
     *
     * @param ImpuestoGanancias $idProveedorGanancias
     *
     * @return ProveedorDatoImpositivos
     */
    public function setIdProveedorGanancias($idProveedorGanancias)
    {
        $this->idProveedorGanancias = $idProveedorGanancias;

        return $this;
    }

    /**
     * Get idProveedorGanancias
     *
     * @return ImpuestoGanancias
     */
    public function getIdProveedorGanancias()
    {
        return $this->idProveedorGanancias;
    }

    /**
     * Set idProveedorIibb
     *
     * @param ImpuestoIibb $idProveedorIibb
     *
     * @return ProveedorDatoImpositivos
     */
    public function setIdProveedorIibb($idProveedorIibb)
    {
        $this->idProveedorIibb = $idProveedorIibb;

        return $this;
    }

    /**
     * Get idProveedorIibb
     *
     * @return ImpuestoIibb
     */
    public function getIdProveedorIibb()
    {
        return $this->idProveedorIibb;
    }

    /**
     * Set cae
     *
     * @param boolean $cae
     *
     * @return ProveedorDatoImpositivos
     */
    public function setCae($cae)
    {
        $this->cae = $cae;

        return $this;
    }

    /**
     * Get cae
     *
     * @return bool
     */
    public function getCae()
    {
        return $this->cae;
    }

    /**
     * Set cai
     *
     * @param boolean $cai
     *
     * @return ProveedorDatoImpositivos
     */
    public function setCai($cai)
    {
        $this->cai = $cai;

        return $this;
    }

    /**
     * Get cai
     *
     * @return bool
     */
    public function getCai()
    {
        return $this->cai;
    }

    /**
     * Set otros
     *
     * @param string $otros
     *
     * @return ProveedorDatoImpositivos
     */
    public function setOtros($otros)
    {
        $this->otros = $otros;

        return $this;
    }

    /**
     * Get otros
     *
     * @return string
     */
    public function getOtros()
    {
        return $this->otros;
    }

    /**
     * @return ImpuestoIva
     */
    public function getProveedorIva()
    {
        return $this->proveedorIva;
    }

    /**
     * @param ImpuestoIva $proveedorIva
     */
    public function setProveedorIva($proveedorIva)
    {
        $this->proveedorIva = $proveedorIva;
    }
}

