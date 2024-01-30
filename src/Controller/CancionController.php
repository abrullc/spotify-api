<?php

namespace App\Controller;

# Fichero de Adrián

use App\Entity\Cancion;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class CancionController extends AbstractController
{
    public function canciones(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $canciones = $this->getDoctrine()
                ->getRepository(Cancion::class)
                ->findAll();

            $canciones = $serializer->serialize(
                $canciones,
                "json",
                ["groups" => ["cancion", "album", "artista"]]
            );

            return new Response($canciones);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function cancion(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        if ($request->isMethod("GET")) {
            $cancion = $this->getDoctrine()
                ->getRepository(Cancion::class)
                ->findOneBy(["id" => $id]);

            $cancion = $serializer->serialize(
                $cancion,
                "json",
                ["groups" => ["cancion", "album", "artista"]]
            );

            return new Response($cancion);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function cancionesPlaylist(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        if ($request->isMethod("GET")) {
            $playlist = $this->getDoctrine()
                ->getRepository(Playlist::class)
                ->findOneBy(["id" => $id]);

            $cancionesPlaylist = $this->getDoctrine()
                ->getRepository(Cancion::class)
                ->findAll();

            if (!empty($playlist))
            {
                $cancionesPlaylist = $serializer->serialize(
                    $cancionesPlaylist,
                    "json",
                    ["groups" => ["cancion", "album", "artista"]]
                );

                return new Response($cancionesPlaylist);
            }

            return new JsonResponse(["msg" => "Playlist no encontrada"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
