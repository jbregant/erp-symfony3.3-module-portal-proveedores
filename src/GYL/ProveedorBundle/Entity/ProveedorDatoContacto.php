<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ProveedorDatoContacto
 *
 * @ORM\Table(name="proveedor_dato_contacto")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDatoContactoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class ProveedorDatoContacto extends BaseAuditoria
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
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=true)
     * })
     */
    private $idUsuario;

    /**
     * @var ProveedorDatoPersonal
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
     * @ORM\Column(name="nombre", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="posicion", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $posicion;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string",nullable=false)
     */
    private $telefono;


    public function __construct()
    {

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
     * Get idUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getId_usuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set Id_Usuario
     *
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorDatoContacto
     */
    public function setId_usuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorDatoContacto
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
     * Get Nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set Nombre
     *
     * @param string $nombre
     *
     * @return ProveedorDatoContacto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get Apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set Apellido
     *
     * @param string $apellido
     *
     * @return ProveedorDatoContacto
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get Posicion
     *
     * @return string
     */
    public function getPosicion()
    {
        return $this->posicion;
    }

    /**
     * Set Posicion
     *
     * @param string $posicion
     *
     * @return ProveedorDatoContacto
     */
    public function setPosicion($posicion)
    {
        $this->posicion = $posicion;

        return $this;
    }

    /**
     * Get Area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set Area
     *
     * @param string $area
     *
     * @return ProveedorDatoContacto
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get Telefono
     *
     * @return integer
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set Telefono
     *
     * @param int $telefono
     *
     * @return ProveedorDatoContacto
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * 
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    
}
