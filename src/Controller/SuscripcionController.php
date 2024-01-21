<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Suscripcion  ;
class SuscripcionController extends AbstractController
{
    
    public function suscripcionesUsuario(Request $request, SerializerInterface $serializer)
    {
        $id = $request->get("id");
        $suscripcion = $this->getDoctrine()
            ->getRepository(Suscripcion::class)
            ->findBy(["usuario" => $id]);
        $suscripcion = $serializer->serialize(
            $suscripcion,
            "json",
            ["groups" => ["suscripcion"]]
        );
        return new Response($suscripcion);
    }
    public function suscripcionUsuario(Request $request, Request $request2, SerializerInterface $serializer)
    {
        $id = $request->get("usuarioId");
        $id2 = $request->get("suscripcionId");
        
        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $id]);

        $suscripcion = $this->getDoctrine()
            ->getRepository(Suscripcion::class)
            ->findOneBy(["id" => $id2]);

        if ($usuario->getId() == $suscripcion->getUsuario()->getId()) {
                
            $respuesta = $serializer->serialize(
                $suscripcion,
                "json",
                ["groups" => ["suscripcion"]]
            );
            return new Response($respuesta);
        }
        
    }
}
