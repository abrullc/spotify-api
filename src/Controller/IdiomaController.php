<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Idioma;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class IdiomaController extends AbstractController
{
    public function idiomas(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $idiomas = $this->getDoctrine()
                ->getRepository(Idioma::class)
                ->findAll();

            $idiomas = $serializer->serialize(
                $idiomas,
                    "json",
                    ["groups" => ["idioma"]]
                );

            return new Response($idiomas);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
