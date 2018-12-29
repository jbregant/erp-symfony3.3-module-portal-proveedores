<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * BackupUserData
 *
 * @ORM\Table(name="backup_user_data")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\BackupUserDataRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 */
class BackupUserData extends BaseAuditoria
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
     * @var GYL\ProveedorBundle\Entity\ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="GYL\ProveedorBundle\Entity\ProveedorDatoPersonal", inversedBy="backupUserData")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

    /**
     * @var string
     *
     * @ORM\Column(name="backup_data", type="text", nullable=false)
     */
    private $backupData;

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
     * Set string
     *
     * @return string
     */
    public function setBackupData($backupData)
    {
        $this->backupData = $backupData;

        return $this;
    }

    /**
     * Set string
     *
     * @return string
     */
    public function getBackupData()
    {
        return $this->backupData;

    }

    /**
     * Set integer
     *
     * @return BackupUserData
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return integer
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }


}

