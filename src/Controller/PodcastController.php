<?php

namespace App\Controller;

# Fichero de Adrián

use App\Entity\Podcast;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class PodcastController extends AbstractController
{
    public function podcasts(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $podcasts = $this->getDoctrine()
                ->getRepository(Podcast::class)
                ->findAll();

            $podcasts = $serializer->serialize(
                    $podcasts,
                    "json",
                    ["groups" => ["podcast"]]
                );

            return new Response($podcasts);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function podcast(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        if ($request->isMethod("GET")) {
            $podcast = $this->getDoctrine()
                ->getRepository(Podcast::class)
                ->findOneBy(["id" => $id]);

            $podcast = $serializer->serialize(
                    $podcast,
                    "json",
                    ["groups" => ["podcast", "usuariosPodcast", "usuarioPodcast", "cancion", "album", "artista", "playlist"]]
                );

            return new Response($podcast);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function podcastsUsuario(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        if ($request->isMethod("GET")) {
            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => $id]);

            $podcasts = $this->getDoctrine()
                ->getRepository(Podcast::class)
                ->findAll();
            
            $podcastsUsuario = [];
            foreach ($podcasts as $podcast)
            {
                $usuariosPodcast = $podcast->getUsuario();
                foreach ($usuariosPodcast as $usuarioPodcast)
                {
                    if ($usuarioPodcast == $usuario)
                    {
                        $podcastsUsuario[] = $podcast;
                    }
                }
            }

            $podcastsUsuario = $serializer->serialize(
                    $podcastsUsuario,
                    "json",
                    ["groups" => ["podcast"]]
                );

            return new Response($podcastsUsuario);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function seguirPodcast(SerializerInterface $serializer, Request $request)
    {
        $idUsuario = $request->get("idUsuario");
        $idPodcast = $request->get("idPodcast");

        if ($request->isMethod("POST")) {
            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => $idUsuario]);

            $podcast = $this->getDoctrine()
                ->getRepository(Podcast::class)
                ->findOneBy(["id" => $idPodcast]);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
