<?php

namespace App\Controller;

# Fichero de Adrián

use App\Entity\Suscripcion;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SuscripcionController extends AbstractController
{
    
    public function suscripcionesUsuario(Request $request, SerializerInterface $serializer)
    {
        if ($request->isMethod("GET"))
        {
            $id = $request->get("id");

            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => $id]);
        
            $suscripciones = $this->getDoctrine()
                ->getRepository(Suscripcion::class)
                ->findBy(["premiumUsuario" => $usuario]);
            
            if (!empty($usuario))
            {
                $suscripciones = $serializer->serialize(
                    $suscripciones,
                    "json",
                    ["groups" => ["suscripcion", "premium", "usuario", "podcast", "album", "artista", "playlist", "cancion"]]
                );
                return new Response($suscripciones);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
    
    public function suscripcionUsuario(Request $request, SerializerInterface $serializer)
    {
        $usuarioId = $request->get("usuarioId");
        $suscripcionId = $request->get("suscripcionId");
        
        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $usuarioId]);

        $suscripcion = $this->getDoctrine()
            ->getRepository(Suscripcion::class)
            ->findOneBy(["id" => $suscripcionId]);

        if ($usuario->getId() == $suscripcion->getPremiumUsuario()->getUsuario()->getId()) {
            $respuesta = $serializer->serialize(
                $suscripcion,
                "json",
                ["groups" => ["suscripcion"]]
            );
            return new Response($respuesta);
        }

        return new JsonResponse(["msg" => "Usuario y/o suscripcion no encontrado"], 404);
    }
}
