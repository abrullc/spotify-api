<?php

namespace App\Controller;
# Fichero de Julio
# Fichero INcompleto

use App\Entity\Playlist;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PlaylistController extends AbstractController
{
    public function playlists(SerializerInterface $serializer)
    {
        $playlists = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findAll();

        $playlists = $serializer->serialize(
            $playlists,
            "json",
            ["groups" => ["playlist"]]
        );

        return new Response($playlists);
    }
    public function playlist(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");
        $playlist = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findOneBy(["id" => $id]);

        $playlist = $serializer->serialize(
            $playlist,
            "json",
            ["groups" => ["playlist"]]
        );
        return new Response($playlist);
    }
    public function playlistsUsuario(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("usuario");
        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $id]);
        if ($request->isMethod("GET")) {

            $playlists = $this->getDoctrine()
                ->getRepository(Playlist::class)
                ->findBy([$usuario->getId() => ["id"]]);

            $playlists = $serializer->serialize(
                $playlists,
                "json",
                ["groups" => ["playlist"]]
            );
            return new Response($playlists);
        }
        if ($request->isMethod("POST")) {
            $bodyData = $request->getContent();
            $playlist = $serializer->deserialize(
                $bodyData,
                Playlist::class,
                "json"
            );
            $playlist->setUsuario($usuario);
            $this->getDoctrine()->getManager()->persist($playlist);
            $this->getDoctrine()->getManager()->flush();

            $playlist = $serializer->serialize(
                $playlist,
                "json",
                ["groups" => ["playlist"]]
            );

            return new Response($playlist);
        }
    }
    public function playlistUsuario(SerializerInterface $serializer, Request $request)
    {
        $id1 = $request->get("playlistId");
        $id2 = $request->get("usuario");
        $playlist = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findOneBy(["id" => $id1]);

        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $id2]);
        if ($request->isMethod("GET")) {

            if ($usuario->getId() == $playlist->getUsuario()->getId()) {

                $playlist = $serializer->serialize(
                    $playlist,
                    "json",
                    ["groups" => ["playlist"]]
                );
            }
            return new Response($playlist);
        }
        if ($request->isMethod("PUT")) {
            $bodyData = $request->getContent();
            $playlist = $serializer->deserialize(
                $bodyData,
                Playlist::class,
                "json",
                ["object_to_populate" => $playlist]
            );
            $this->getDoctrine()->getManager()->persist($playlist);
            $this->getDoctrine()->getManager()->flush();

            $playlist = $serializer->serialize(
                $playlist,
                "json",
                ["groups" => ["playlist"]]
            );

            return new Response($playlist);
        }
        if ($request->isMethod("DELETE")) {
            /* meter la playlist en eliminadas, no borrarla sin mas */
        }
    }
}
