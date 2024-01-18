<?php

namespace App\Controller;

# Fichero de Adrián

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class UsuarioController extends AbstractController
{
    public function usuarios(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $usuarios = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findAll();

            $usuarios = $serializer->serialize(
                $usuarios,
                    "json",
                    ["groups" => ["usuario"]]
                );

            return new Response($usuarios);
        }

        /*if ($request->isMethod("POST")) {
            $bodyData = $request->getContent();
            $affiliation = $serializer->deserialize(
                $bodyData,
                Usuario::class,
                "json"
            );
            
            $this->getDoctrine()->getManager()->persist($affiliation);
            $this->getDoctrine()->getManager()->flush();

            $affiliation = $serializer->serialize(
                $affiliation, 
                "json", 
                ["groups" => ["affiliation"]]);
            
            return new Response($affiliation);
        }*/

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
