<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Configuracion;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ConfiguracionController extends AbstractController
{
    public function configuracionUsuario(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $id]);

        $configuracion = $this->getDoctrine()
            ->getRepository(Configuracion::class)
            ->findOneBy(["usuario" => $usuario]);

        if ($request->isMethod("GET")) {

            $configuracion = $serializer->serialize(
                    $configuracion,
                    "json",
                    ["groups" => ["configuracion", "calidad", "idioma", "tipoDescarga"]]
                );

            return new Response($configuracion);
        }

        if ($request->isMethod("PUT")) {
            if (!empty($configuracion)) {
                $bodyData = $request->getContent();
                $configuracion = $serializer->deserialize(
                    $bodyData,
                    Configuracion::class,
                    "json",
                    ["object_to_populate" => $configuracion]
                );
                
                $this->getDoctrine()->getManager()->persist($configuracion);
                $this->getDoctrine()->getManager()->flush();

                $configuracion = $serializer->serialize(
                    $configuracion,
                    "json",
                    ["groups" => ["configuracion", "calidad", "idioma", "tipoDescarga"]]
                );

            return new Response($configuracion);
            }

            return new JsonResponse(["msg" => "Configuración no encontrada"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
