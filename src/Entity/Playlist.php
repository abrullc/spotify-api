<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Playlist
 *
 * @ORM\Table(name="playlist", indexes={@ORM\Index(name="fk_playlist_usuario1_idx", columns={"usuario_id"})})
 * @ORM\Entity
 */
class Playlist
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=150, nullable=false)
     * @Groups("Playlist")
     */
    private $titulo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_canciones", type="integer", nullable=true, options={"unsigned"=true})
     * @Groups("Playlist")
     */
    private $numeroCanciones;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_creacion", type="date", nullable=true)
     * @Groups("Playlist")
     */
    private $fechaCreacion;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     * @Groups("Playlist")
     */
    private $usuario;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="playlist")
     * @Groups("Playlist")
     */
    private $usuarioSeguidor = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarioSeguidor = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
