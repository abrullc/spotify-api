<?php

namespace App\Controller;
# Fichero de Julio
# Fichero INcompleto

use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PlaylistController extends AbstractController
{
    public function playlists(SerializerInterface $serializer, Request $request)
    {
        $playlists = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findAll();

        $playlists = $serializer->serialize(
            $playlists,
                "json",
                ["groups" => ["idioma"]]
            );

        return new Response($playlists);
    }
    
}
