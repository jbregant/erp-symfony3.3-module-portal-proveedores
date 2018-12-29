<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TipoProveedor
 *
 * @ORM\Table(name="tipo_proveedor")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\TipoProveedorRepository")
 */
class TipoProveedor extends BaseAuditoria
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
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="extranjero", type="boolean")
     */
    private $extranjero;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDatoPersonal", mappedBy="tipoProveedor")
     */
    private $proveedorDatoPersonal;



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
     * @return TipoProveedor
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
     * Set extranjero
     *
     * @param boolean $extranjero
     *
     * @return TipoProveedor
     */
    public function setExtranjero($extranjero)
    {
        $this->extranjero = $extranjero;

        return $this;
    }

    /**
     * Get extranjero
     *
     * @return bool
     */
    public function getExtranjero()
    {
        return $this->extranjero;
    }

    /**
     * @return mixed
     */
    public function getProveedorDatoPersonal()
    {
        return $this->proveedorDatoPersonal;
    }

    /**
     * @param mixed $proveedorDatoPersonal
     */
    public function setProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $this->proveedorDatoPersonal = $proveedorDatoPersonal;
    }
}

