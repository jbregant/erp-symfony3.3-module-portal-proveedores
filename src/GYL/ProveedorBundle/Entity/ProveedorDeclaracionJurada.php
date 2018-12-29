<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * ProveedorDeclaracionJurada
 *
 * @ORM\Table(name="proveedor_declaracion_jurada")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDeclaracionJuradaRepository")
 */
class ProveedorDeclaracionJurada extends BaseAuditoria
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
     * @var int
     *
     * @ORM\Column(name="id_usuario", type="integer")
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
     * @ORM\Column(name="acepta", type="boolean", nullable=true)
     */
    private $acepta;


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
     * @param integer $idUsuario
     *
     * @return ProveedorDeclaracionJurada
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return int
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
     * @return ProveedorDeclaracionJurada
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
     * Set acepta
     *
     * @param boolean $acepta
     *
     * @return ProveedorDeclaracionJurada
     */
    public function setAcepta($acepta)
    {
        $this->acepta = $acepta;

        return $this;
    }

    /**
     * Get acepta
     *
     * @return bool
     */
    public function getAcepta()
    {
        return $this->acepta;
    }
}

