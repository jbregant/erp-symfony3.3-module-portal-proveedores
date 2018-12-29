<?php

namespace GYL\UsuarioBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;


/**
 * Trait Auditable
 * @author Carlos Sanchez <sanchezcl@gmail.com>
 */
trait UserAuditableTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="id_usuario_creacion", referencedColumnName="id")
     */
    Private $usuarioCreacion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="id_usuario_ultima_modificacion", referencedColumnName="id")
     */
    Private $usuarioUltimaModificacion;
}
