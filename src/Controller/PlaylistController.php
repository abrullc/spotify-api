<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Eliminada;
use App\Entity\Playlist;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class PlaylistController extends AbstractController
{
    public function playlists(Request $request, SerializerInterface $serializer)
    {
        if ($request->isMethod("GET"))
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

        if ($request->isMethod("POST"))
        {
            $bodyData = $request->getContent();
            $playlist = $serializer->deserialize(
                $bodyData,
                Playlist::class,
                "json"
            );

            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => 1]);

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

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
    
    public function playlist(Request $request, SerializerInterface $serializer)
    {
        if ($request->isMethod("GET"))
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

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
    
    public function playlistsUsuario(Request $request, SerializerInterface $serializer)
    {
        if ($request->isMethod("GET"))
        {
            $id = $request->get("id");

            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => $id]);

            if (!empty($usuario))
            {
                $playlists = $this->getDoctrine()
                    ->getRepository(Playlist::class)
                    ->findBy(["usuario" => $usuario]);

                $playlists = $serializer->serialize(
                    $playlists,
                    "json",
                    ["groups" => ["playlist"]]
                );
                return new Response($playlists);
            }
            
            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
    public function playlistUsuario(Request $request, SerializerInterface $serializer)
    {
        $usuarioId = $request->get("usuarioId");
        $playlistId = $request->get("playlistId");

        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $usuarioId]);

        $playlist = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findOneBy(["id" => $playlistId]);
        
        if (!empty($usuario))
        {
            if (!empty($playlist))
            {
                if ($request->isMethod("GET"))
                {
                    if ($usuario->getId() == $playlist->getUsuario()->getId())
                    {
                        $playlist = $serializer->serialize(
                            $playlist,
                            "json",
                            ["groups" => ["playlist"]]
                        );

                        return new Response($playlist);
                    }

                    return new JsonResponse(["msg" => "Playlist para usuario indicado no encontrada"], 404);
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

                    $playlistsEliminadas = $this->getDoctrine()
                        ->getRepository(Eliminada::class)
                        ->findOneBy(["playlist" => $playlist]);
                    
                    if (empty($playlistsEliminadas))
                    {
                        $playlistEliminada = new Eliminada();
                        $playlistEliminada->setPlaylist($playlist);

                        $this->getDoctrine()->getManager()->persist($playlistEliminada);
                        $this->getDoctrine()->getManager()->flush();

                        $playlistEliminada = $serializer->serialize(
                            $playlistEliminada,
                            "json",
                            ["groups" => ["eliminada", "playlist"]]
                        );

                        return new Response($playlistEliminada);
                    }

                    return new JsonResponse(["msg" => "La playlist especificada ya ha sido eliminada"]);
                }

                return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
            }
            
            return new JsonResponse(["msg" => "Playlist no encontrada"], 404);
        }

        return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
    }
}
