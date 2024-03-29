<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Configuracion;
use App\Entity\Free;
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

        if ($request->isMethod("POST")) {
            $bodyData = $request->getContent();
            $usuario = $serializer->deserialize(
                $bodyData,
                Usuario::class,
                "json"
            );

            $usuarioFree = new Free();
            $usuarioFree->setTiempoPublicidad(0);
            $usuarioFree->setUsuario($usuario);

            $configuracionesUsuario = new Configuracion();
            $configuracionesUsuario->setAutoplay(false);
            $configuracionesUsuario->setAjuste(false);
            $configuracionesUsuario->setNormalizacion(false);
            $configuracionesUsuario->setUsuario($usuario);

            $this->getDoctrine()->getManager()->persist($usuario);
            $this->getDoctrine()->getManager()->flush();

            $this->getDoctrine()->getManager()->persist($usuarioFree);
            $this->getDoctrine()->getManager()->flush();

            $this->getDoctrine()->getManager()->persist($configuracionesUsuario);
            $this->getDoctrine()->getManager()->flush();

            $usuario = $serializer->serialize(
                $usuario, 
                "json", 
                ["groups" => ["usuario"]]
            );
            
            return new Response($usuario);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }

    public function usuario(SerializerInterface $serializer, Request $request)
    {
        $id = $request->get("id");

        $usuario = $this->getDoctrine()
            ->getRepository(Usuario::class)
            ->findOneBy(["id" => $id]);

        if ($request->isMethod("GET")) {

            $usuario = $serializer->serialize(
                    $usuario,
                    "json",
                    ["groups" => ["usuario"]]
                );

            return new Response($usuario);
        }

        if ($request->isMethod("PUT")) {
            if (!empty($usuario)) {
                $bodyData = $request->getContent();
                $usuario = $serializer->deserialize(
                    $bodyData,
                    Usuario::class,
                    "json",
                    ["object_to_populate" => $usuario]
                );
                
                $this->getDoctrine()->getManager()->persist($usuario);
                $this->getDoctrine()->getManager()->flush();

                $usuario = $serializer->serialize(
                    $usuario,
                    "json",
                    ["groups" => ["usuario"]]
                );

            return new Response($usuario);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        if ($request->isMethod("DELETE")) {
            $deletedUsuario = clone $usuario;
            $this->getDoctrine()->getManager()->remove($usuario);
            $this->getDoctrine()->getManager()->flush();
            
            $deletedUsuario = $serializer->serialize(
                $deletedUsuario, 
                "json", 
                ["groups" => ["usuario"]]
            );

            return new Response($deletedUsuario);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
