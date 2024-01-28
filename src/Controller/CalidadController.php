<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Calidad;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class CalidadController extends AbstractController
{
    public function calidades(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $calidades = $this->getDoctrine()
                ->getRepository(Calidad::class)
                ->findAll();

            $calidades = $serializer->serialize(
                $calidades,
                    "json",
                    ["groups" => ["calidad"]]
                );

            return new Response($calidades);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
