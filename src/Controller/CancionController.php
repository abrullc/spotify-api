<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\AnyadeCancionPlaylist;
use App\Entity\Cancion;
use App\Entity\Playlist;
use App\Entity\Usuario;
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

            $cancionesAnyadidasPlaylist = $this->getDoctrine()
                ->getRepository(AnyadeCancionPlaylist::class)
                ->findBy(["playlist" => $playlist]);

            if (!empty($playlist))
            {
                $cancionesPlaylist = [];
                foreach ($cancionesAnyadidasPlaylist as $cancionAynadidaPlaylist)
                {
                    $cancionPlaylist = $this->getDoctrine()
                        ->getRepository(Cancion::class)
                        ->findOneBy(["id" => $cancionAynadidaPlaylist->getCancion()->getId()]);
                    
                    $cancionesPlaylist[] = $cancionPlaylist;
                }

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

    public function cancionPlaylist(SerializerInterface $serializer, Request $request)
    {
        $playlistId = $request->get("playlistId");
        $cancionId = $request->get("cancionId");

        $playlist = $this->getDoctrine()
            ->getRepository(Playlist::class)
            ->findOneBy(["id" => $playlistId]);

        $cancion = $this->getDoctrine()
            ->getRepository(Cancion::class)
            ->findOneBy(["id" => $cancionId]);

        if ($request->isMethod("POST")) {
            if (!empty($playlist))
            {
                if (!empty($cancion))
                {
                    $cancionAnyadidaPlaylist = $this->getDoctrine()
                        ->getRepository(AnyadeCancionPlaylist::class)
                        ->findOneBy(["playlist" => $playlist, "cancion" => $cancion]);
                    
                    if (empty($cancionAnyadidaPlaylist))
                    {
                        $usuario = $this->getDoctrine()
                            ->getRepository(Usuario::class)
                            ->findOneBy(["id" => 104]);
                        
                        $newCancionAnyadidaPlaylist = new AnyadeCancionPlaylist();
                        $newCancionAnyadidaPlaylist->setPlaylist($playlist);
                        $newCancionAnyadidaPlaylist->setCancion($cancion);
                        $newCancionAnyadidaPlaylist->setUsuario($usuario);

                        $this->getDoctrine()->getManager()->persist($newCancionAnyadidaPlaylist);
                        $this->getDoctrine()->getManager()->flush();

                        $newCancionAnyadidaPlaylist = $serializer->serialize(
                            $newCancionAnyadidaPlaylist,
                            "json",
                            ["groups" => ["usuarioAnyadeCancionPlaylist", "playlist", "cancion", "album", "artista"]]
                        );

                        return new Response($newCancionAnyadidaPlaylist);
                    }

                    return new JsonResponse(["msg" => "La canción especificada ya esta añadida a la playlist especificada"]);
                }
                
                return new JsonResponse(["msg" => "Cancion no encontrada"], 404);
            }

            return new JsonResponse(["msg" => "Playlist no encontrada"], 404);
        }

        if ($request->isMethod("DELETE")) {
            if (!empty($playlist))
            {
                if (!empty($cancion))
                {
                    $cancionAnyadidaPlaylist = $this->getDoctrine()
                        ->getRepository(AnyadeCancionPlaylist::class)
                        ->findOneBy(["playlist" => $playlist, "cancion" => $cancion]);
                    
                    if (!empty($cancionAnyadidaPlaylist))
                    {
                        $deletedCancionAnyadidaPlaylist = clone $cancionAnyadidaPlaylist;
                        
                        $this->getDoctrine()->getManager()->remove($cancionAnyadidaPlaylist);
                        $this->getDoctrine()->getManager()->flush();
                        
                        $deletedCancionAnyadidaPlaylist = $serializer->serialize(
                            $deletedCancionAnyadidaPlaylist, 
                            "json", 
                            ["groups" => ["usuarioAnyadeCancionPlaylist", "playlist", "cancion", "album", "artista"]]
                        );

                        return new Response($deletedCancionAnyadidaPlaylist);
                    }

                    return new JsonResponse(["msg" => "La canción especificada no está añadida a la playlist especificada"]);
                }

                return new JsonResponse(["msg" => "Cancion no encontrada"], 404);
            }

            return new JsonResponse(["msg" => "Playlist no encontrada"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
