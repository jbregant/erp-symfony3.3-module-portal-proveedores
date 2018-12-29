<?php

namespace GYL\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GYL\UsuarioBundle\Entity\Traits\SoftdeleteableTrait;
use GYL\UsuarioBundle\Entity\Traits\TimestampableTrait;
use GYL\UsuarioBundle\Entity\Traits\UserAuditableTrait;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Invitacion
 *
 * @ORM\Table(name="invitacion")
 * @ORM\Entity(repositoryClass="GYL\UsuarioBundle\Repository\InvitacionRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class Invitacion
{
    use SoftdeleteableTrait, TimestampableTrait, UserAuditableTrait;
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
     * @ORM\Column(name="codigo", type="string", length=24)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180)
     * @Gedmo\Versioned
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="enviado", type="boolean", nullable=true)
     */
    private $enviado = false;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="caducada", type="boolean", nullable=false)
     */
    private $caducada = false;


    public function __construct()
    {
        // Genera el codigo de la invitacion
        $this->codigo = substr(md5(uniqid(rand(), true)), 0, 24);
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
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Invitacion
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enviado
     *
     * @param boolean $enviado
     *
     * @return Invitacion
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Get enviado
     *
     * @return bool
     */
    public function getEnviado()
    {
        return $this->enviado;
    }
    
    /**
     * Set caducada
     *
     * @param boolean $caducada
     *
     * @return Invitacion
     */
    public function setCaducada($caducada)
    {
        $this->caducada = $caducada;

        return $this;
    }

    /**
     * Get caducada
     *
     * @return bool
     */
    public function getCaducada()
    {
        return $this->caducada;
    }
}
