<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorUteMiembros
 *
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorUteMiembrosRepository")
 * @ORM\Table(name="proveedor_ute_miembros")ProveedorUteMiembrosRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class ProveedorUteMiembros extends BaseAuditoria
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
     * @ORM\ManyToOne(targetEntity="ProveedorUte" , inversedBy="proveedorUteMiembros")
     * @ORM\JoinColumn(name="id_ute", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $ute;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=true)
     * @Assert\Regex("/[0-9](11)/")
     */
    private $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=64)
     */
    private $razonSocial;

    /**
     * @var int
     *
     * @ORM\Column(name="numero_inscripcion", type="integer")
     */
    private $numeroInscripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="participacion_ganancias", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $participacionGanancias;

    /**
     * @var bool
     *
     * @ORM\Column(name="empleador", type="boolean")
     */
    private $empleador;

    /**
     * @var string
     *
     * @ORM\Column(name="participacion_remunerativa", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $participacionRemunerativa;


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
     * @return ProveedorUteMiembros
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
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return ProveedorUteMiembros
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set numeroInscripcion
     *
     * @param integer $numeroInscripcion
     *
     * @return ProveedorUteMiembros
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;

        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return int
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Set participacionGanancias
     *
     * @param string $participacionGanancias
     *
     * @return ProveedorUteMiembros
     */
    public function setParticipacionGanancias($participacionGanancias)
    {
        $this->participacionGanancias = $participacionGanancias;

        return $this;
    }

    /**
     * Get participacionGanancias
     *
     * @return string
     */
    public function getParticipacionGanancias()
    {
        return $this->participacionGanancias;
    }


    /**
     * Set empleador
     *
     * @param boolean $empleador
     *
     * @return ProveedorUteMiembros
     */
    public function setEmpleador($empleador)
    {
        $this->empleador = $empleador;

        return $this;
    }

    /**
     * Get empleador
     *
     * @return bool
     */
    public function getEmpleador()
    {
        return $this->empleador;
    }

    /**
     * Set participacionRemunerativa
     *
     * @param string $participacionRemunerativa
     *
     * @return ProveedorUteMiembros
     */
    public function setParticipacionRemunerativa($participacionRemunerativa)
    {
        $this->participacionRemunerativa = $participacionRemunerativa;

        return $this;
    }

    /**
     * Get participacionRemunerativa
     *
     * @return string
     */
    public function getParticipacionRemunerativa()
    {
        return $this->participacionRemunerativa;
    }


    /**
     * @return int
     */
    public function getUte()
    {
        return $this->ute;
    }

    /**
     * @param int $ute
     */
    public function setUte($ute)
    {
        $this->ute = $ute;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorUteMiembros
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
}

