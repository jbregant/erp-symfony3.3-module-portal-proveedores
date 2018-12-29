<?php

namespace GYL\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * ProveedorDocumentacion
 *
 * @ORM\Table(name="proveedor_documentacion")
 * @ORM\Entity(repositoryClass="GYL\ProveedorBundle\Repository\ProveedorDocumentacionRepository")
 * @Vich\Uploadable
 */
class ProveedorDocumentacion extends BaseAuditoria
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
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * })
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
     * @var TipoDocumentacion
     *
     * @ORM\ManyToOne(targetEntity="TipoDocumentacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_documentacion", referencedColumnName="id")
     * })
     */
    private $tipoDocumentacion;

    /**
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="documento", fileNameProperty="documentName")
     *
     * @var File
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="public_filename", type="string", length=255, nullable=true)
     */
    private $publicfilename;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $document
     */
    public function setFile( $document = null)
    {
        $this->file = $document;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
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
     * Set idUsuario
     *
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorDocumentacion
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
     * @return ProveedorActividad
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
     * Set tipoDocumentacion
     *
     * @param TipoDocumentacion $tipoDocumentacion
     *
     * @return ProveedorDocumentacion
     */
    public function setIdTipoDocumentacion($tipoDocumentacion)
    {
        $this->tipoDocumentacion = $tipoDocumentacion;

        return $this;
    }

    /**
     * Get tipoDocumentacion
     *
     * @return TipoDocumentacion
     */
    public function getTipoDocumentacion()
    {
        return $this->tipoDocumentacion;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return ProveedorDocumentacion
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/documentacion';
    }


    /**
     * @return string
     */
    public function getPublicfilename()
    {
        return $this->publicfilename;
    }

    /**
     * @param string $publicfilename
     *
     * @return ProveedorDocumentacion
     */
    public function setPublicfilename($publicfilename)
    {
        $this->publicfilename = $publicfilename;
        return $this;
    }
}

