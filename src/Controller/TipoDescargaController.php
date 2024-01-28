<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\TipoDescarga;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class TipoDescargaController extends AbstractController
{
    public function tiposDescarga(SerializerInterface $serializer, Request $request)
    {
        if ($request->isMethod("GET")) {
            $tiposDescarga = $this->getDoctrine()
                ->getRepository(TipoDescarga::class)
                ->findAll();

            $tiposDescarga = $serializer->serialize(
                $tiposDescarga,
                    "json",
                    ["groups" => ["tipoDescarga"]]
                );

            return new Response($tiposDescarga);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
