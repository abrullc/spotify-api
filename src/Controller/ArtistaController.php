<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Album;
use App\Entity\Artista;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ArtistaController extends AbstractController
{
    public function artistas(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $artistas = $this->getDoctrine()
                ->getRepository(Artista::class)
                ->findAll();

            $artistas = $serializer->serialize(
                $artistas,
                    "json",
                    ["groups" => ["artista"]]
                );

            return new Response($artistas);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function albumsArtista(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $id = $request->get("id");

            $artista = $this->getDoctrine()
                ->getRepository(Artista::class)
                ->findOneBy(["id" => $id]);

            $albumsArtista = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findBy(["artista" => $artista]);

            if (!empty($artista))
            {
                $albumsArtista = $serializer->serialize(
                    $albumsArtista,
                    "json",
                    ["groups" => ["albumArtista"]]
                );

                return new Response($albumsArtista);
            }

            return new JsonResponse(["msg" => "Artista no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function infoAlbumArtista(Request $request, SerializerInterface $serializer)
    {
        $artistaId = $request->get("artistaId");
        $albumId = $request->get("albumId");
        
        $artista = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->findOneBy(["id" => $artistaId]);

        $album = $this->getDoctrine()
            ->getRepository(Album::class)
            ->findOneBy(["id" => $albumId]);

        if ($artista->getId() == $album->getArtista()->getId()) {
            $respuesta = $serializer->serialize(
                $album,
                "json",
                ["groups" => ["albumArtista"]]
            );
            return new Response($respuesta);
        }

        return new JsonResponse(["msg" => "Artista y/o album no encontrado"], 404);
    }
}
