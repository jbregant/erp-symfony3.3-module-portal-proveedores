<?php

namespace  GYL\UsuarioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ManyToMany;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use GYL\UsuarioBundle\Entity\Traits\SoftdeleteableTrait;
use GYL\UsuarioBundle\Entity\Traits\TimestampableTrait;
use GYL\UsuarioBundle\Entity\Traits\UserAuditableTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;


/**
 * @ORM\Entity 
 * @ORM\Table(name="usuario")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 * @Gedmo\Loggable
 */
class Usuario extends BaseUser
{
    use SoftdeleteableTrait, TimestampableTrait, UserAuditableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Gedmo\Versioned
     */
    protected $id;

    /**
     * @var string
     * @Gedmo\Versioned
     */
    protected $username;

    /**
     * Muchos usuarios tienen muchos Proveedor Dato Personal
     * @ManyToMany(targetEntity="GYL\ProveedorBundle\Entity\ProveedorDatoPersonal", mappedBy="idUsuario")
     */
    protected $proveedorDatoPersonal;
    
    public function __construct()
    {
        parent::__construct();
        $this->proveedorDatoPersonal = new ArrayCollection();
    }

    // Override de los metodos para usar el username como email

    /**
     * Sets the email.
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
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
     *
     * @return self
     */
    public function addProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $proveedorDatoPersonal->addIdUsuario($this);
        $this->proveedorDatoPersonal[] = $proveedorDatoPersonal;

        return $this;
    }
}
