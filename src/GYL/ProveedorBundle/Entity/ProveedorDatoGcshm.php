<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProveedorDatoGcshm
 *
 * @ORM\Table(name="proveedor_dato_gcshm")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDatoGcshmRepository")
 */
class ProveedorDatoGcshm extends BaseAuditoria
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
     * @var bool
     *
     * @ORM\Column(name="certificacion_iso9001", type="boolean")
     */
    private $certificacionIso9001;

    /**
     * @var bool
     *
     * @ORM\Column(name="certificacion_iso14001", type="boolean")
     */
    private $certificacionIso14001;

    /**
     * @var bool
     *
     * @ORM\Column(name="certificacion_osha18001", type="boolean")
     */
    private $certificacionOsha18001;

    /**
     * @var bool
     *
     * @ORM\Column(name="permisos_ambientales", type="boolean")
     */
    private $permisosAmbientales;

    /**
     * @var bool
     *
     * @ORM\Column(name="documentacion_evaluacion", type="boolean")
     */
    private $documentacionEvaluacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="organigrama_institucional_obra", type="boolean")
     */
    private $organigramaInstitucionalObra;

    /**
     * @var bool
     *
     * @ORM\Column(name="pese", type="boolean")
     */
    private $pese;


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
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorDatoGcshm
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
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
     * @return ProveedorDatoGcshm
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
     * Set certificacionIso9001
     *
     * @param boolean $certificacionIso9001
     *
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionIso9001($certificacionIso9001)
    {
        $this->certificacionIso9001 = $certificacionIso9001;

        return $this;
    }

    /**
     * Get certificacionIso9001
     *
     * @return bool
     */
    public function getCertificacionIso9001()
    {
        return $this->certificacionIso9001;
    }

    /**
     * Set certificacionIso14001
     *
     * @param boolean $certificacionIso14001
     *
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionIso14001($certificacionIso14001)
    {
        $this->certificacionIso14001 = $certificacionIso14001;

        return $this;
    }

    /**
     * Get certificacionIso14001
     *
     * @return bool
     */
    public function getCertificacionIso14001()
    {
        return $this->certificacionIso14001;
    }

    /**
     * Set certificacionOsha18001
     *
     * @param boolean $certificacionOsha18001
     *
     * @return ProveedorDatoGcshm
     */
    public function setCertificacionOsha18001($certificacionOsha18001)
    {
        $this->certificacionOsha18001 = $certificacionOsha18001;

        return $this;
    }

    /**
     * Get certificacion_osha18001
     *
     * @return bool
     */
    public function getCertificacionOsha18001()
    {
        return $this->certificacionOsha18001;
    }

    /**
     * Set permisosAmbientales
     *
     * @param boolean $permisosAmbientales
     *
     * @return ProveedorDatoGcshm
     */
    public function setPermisosAmbientales($permisosAmbientales)
    {
        $this->permisosAmbientales = $permisosAmbientales;

        return $this;
    }

    /**
     * Get permisosAmbientales
     *
     * @return bool
     */
    public function getPermisosAmbientales()
    {
        return $this->permisosAmbientales;
    }

    /**
     * Set documentacionEvaluacion
     *
     * @param boolean $documentacionEvaluacion
     *
     * @return ProveedorDatoGcshm
     */
    public function setDocumentacionEvaluacion($documentacionEvaluacion)
    {
        $this->documentacionEvaluacion = $documentacionEvaluacion;

        return $this;
    }

    /**
     * Get documentacionEvaluacion
     *
     * @return bool
     */
    public function getDocumentacionEvaluacion()
    {
        return $this->documentacionEvaluacion;
    }

    /**
     * Set organigramaInstitucionalObra
     *
     * @param boolean $organigramaInstitucionalObra
     *
     * @return ProveedorDatoGcshm
     */
    public function setOrganigramaInstitucionalObra($organigramaInstitucionalObra)
    {
        $this->organigramaInstitucionalObra = $organigramaInstitucionalObra;

        return $this;
    }

    /**
     * Get organigramaInstitucionalObra
     *
     * @return bool
     */
    public function getOrganigramaInstitucionalObra()
    {
        return $this->organigramaInstitucionalObra;
    }

    /**
     * Set pese
     *
     * @param boolean $pese
     *
     * @return ProveedorDatoGcshm
     */
    public function setPese($pese)
    {
        $this->pese = $pese;

        return $this;
    }

    /**
     * Get pese
     *
     * @return bool
     */
    public function getPese()
    {
        return $this->pese;
    }
}

