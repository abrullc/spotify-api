<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

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
                ["groups" => ["podcast", "usuariosPodcast", "usuarioPodcast"]]
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
                ["groups" => ["podcast", "usuariosPodcast", "usuarioPodcast"]]
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

            if (!empty($usuario))
            {
                $podcastsUsuario = $usuario->getPodcast();

                $podcastsUsuario = $serializer->serialize(
                    $podcastsUsuario,
                    "json",
                    ["groups" => ["podcast"]]
                );

                return new Response($podcastsUsuario);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function seguirPodcast(SerializerInterface $serializer, Request $request)
    {
        $idUsuario = $request->get("idUsuario");
        $idPodcast = $request->get("idPodcast");

        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $idUsuario]);

        $podcast = $this->getDoctrine()
            ->getRepository(Podcast::class)
            ->findOneBy(["id" => $idPodcast]);

        if ($request->isMethod("POST")) {

            if (!empty($usuario))
            {
                if (!empty($podcast))
                {
                    $podcastsUsuario = $usuario->getPodcast();
                    
                    foreach ($podcastsUsuario as $podcastUsuario)
                    {
                        if ($podcastUsuario == $podcast)
                        {
                            return new JsonResponse(["msg" => "El usuario especificado ya esta siguiendo el podcast especificado"]);
                        }
                    }

                    $podcastsUsuario[] = $podcast;
                    $usuario->setPodcast($podcastsUsuario);
                    
                    $this->getDoctrine()->getManager()->persist($usuario);
                    $this->getDoctrine()->getManager()->flush();
    
                    $podcastsUsuario = $serializer->serialize(
                        $podcastsUsuario,
                        "json",
                        ["groups" => ["podcast", "usuariosPodcast", "usuarioPodcast"]]
                    );
    
                    return new Response($podcastsUsuario);
                }
                
                return new JsonResponse(["msg" => "Podcast no encontrado"], 404);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        if ($request->isMethod("DELETE")) {
            if (!empty($usuario))
            {
                if (!empty($podcast))
                {
                    $podcastsUsuario = $usuario->getPodcast();
                    
                    foreach ($podcastsUsuario as $i=>$podcastUsuario)
                    {
                        if ($podcastUsuario == $podcast)
                        {
                            unset($podcastsUsuario[$i]);
                            
                            $usuario->setPodcast($podcastsUsuario);
                    
                            $this->getDoctrine()->getManager()->persist($usuario);
                            $this->getDoctrine()->getManager()->flush();
            
                            $podcastsUsuario = $serializer->serialize(
                                $podcastsUsuario,
                                "json",
                                ["groups" => ["podcast", "usuariosPodcast", "usuarioPodcast"]]
                            );
            
                            return new Response($podcastsUsuario);
                        }
                    }

                    return new JsonResponse(["msg" => "El usuario especificado no está siguiendo el podcast especificado"]);
                }

                return new JsonResponse(["msg" => "Podcast no encontrado"], 404);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
