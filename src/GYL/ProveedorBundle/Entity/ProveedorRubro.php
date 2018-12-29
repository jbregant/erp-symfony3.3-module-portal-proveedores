<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ProveedorRubro
 *
 * @ORM\Table(name="proveedor_rubro")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorRubroRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 */
class ProveedorRubro extends BaseAuditoria
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
     * @ORM\ManyToOne(targetEntity="RubroClase", inversedBy="proveedorRubro")
     * @ORM\JoinColumn(name="id_rubro_clase", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $rubroClase;


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
     * @param @var GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorRubro
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return @var GYL\UsuarioBundle\Entity\Usuario
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
     * @return ProveedorRubro
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
     * Set idRubroClase
     *
     * @param integer $idRubroClase
     *
     * @return ProveedorRubro
     */
    public function setIdRubroClase($idRubroClase)
    {
        $this->idRubroClase = $idRubroClase;

        return $this;
    }

    /**
     * Get idRubroClase
     *
     * @return int
     */
    public function getIdRubroClase()
    {
        return $this->idRubroClase;
    }

    /**
     * @return int
     */
    public function getRubroClase()
    {
        return $this->rubroClase;
    }

    /**
     * @param int $rubroClase
     */
    public function setRubroClase($rubroClase)
    {
        $this->rubroClase = $rubroClase;
    }
}

