<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Album;
use App\Entity\Cancion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class AlbumController extends AbstractController
{
    public function albums(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $albums = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findAll();

            $albums = $serializer->serialize(
                $albums,
                    "json",
                    ["groups" => ["album", "artista"]]
                );

            return new Response($albums);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function album(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $id = $request->get("id");

            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findOneBy(["id" => $id]);

            $album = $serializer->serialize(
                    $album,
                    "json",
                    ["groups" => ["album", "artista"]]
                );

            return new Response($album);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function cancionesAlbum(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $id = $request->get("id");

            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findOneBy(["id" => $id]);

            $cancionesAlbum = $this->getDoctrine()
                ->getRepository(Cancion::class)
                ->findBy(["album" => $album]);

            if (!empty($album))
            {
                $cancionesAlbum = $serializer->serialize(
                    $cancionesAlbum,
                    "json",
                    ["groups" => ["cancionAlbum"]]
                );

                return new Response($cancionesAlbum);
            }

            return new JsonResponse(["msg" => "Album no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
