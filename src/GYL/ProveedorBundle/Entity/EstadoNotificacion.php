<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GYL\ProveedorBundle\Entity\BaseAuditoria as BaseAuditoria;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * EstadoNotificacion
 *
 * @ORM\Table("estado_notificacion")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\EstadoNotificacionRepository")
 */
class EstadoNotificacion extends BaseAuditoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=45, nullable=false)
     * @Assert\NotBlank()
     */
    private $denominacion;


    /**
     * @return string
     */
    public function __toString() {
        return $this->getDenominacion();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return EstadoNotificacion
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
}
