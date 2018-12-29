<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorPersonaJuridicaMiembros
 *
 * @ORM\Table(name="proveedor_persona_juridica_miembros")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorPersonaJuridicaMiembrosRepository")
 */
class ProveedorPersonaJuridicaMiembros extends BaseAuditoria
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal", inversedBy="proveedorPersonaJuridicaMiembros")
     * @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     */
    private $proveedorDatoPersonal;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/[0-9](2)-[0-9](8)-[0-9](1)/",
     *     match=false,
     *     message="El formato de CUIT es 00-00000000-0")
     *
     */
    private $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=false)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="participacion", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $participacion;


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
     * Set cuit
     *
     * @param string $cuit
     *
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ProveedorPersonaJuridicaMiembros
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
     * @return ProveedorPersonaJuridicaMiembros
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
     * Set participacion
     *
     * @param string $participacion
     *
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setParticipacion($participacion)
    {
        $this->participacion = $participacion;

        return $this;
    }

    /**
     * Get participacion
     *
     * @return string
     */
    public function getParticipacion()
    {
        return $this->participacion;
    }

    public function getProveedorDatoPersonal()
    {
        return $this->proveedorDatoPersonal;
    }

    public function setProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $this->proveedorDatoPersonal = $proveedorDatoPersonal;
    }
}

