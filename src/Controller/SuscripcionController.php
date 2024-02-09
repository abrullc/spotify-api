<?php

namespace App\Controller;

# Fichero de Adrián
# Fichero completo

use App\Entity\Pago;
use App\Entity\Paypal;
use App\Entity\Suscripcion;
use App\Entity\TarjetaCredito;
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
                    ["groups" => ["suscripcion"]]
                );
                return new Response($suscripciones);
            }

            return new JsonResponse(["msg" => "Usuario no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
    
    public function suscripcionUsuario(Request $request, SerializerInterface $serializer)
    {
        if ($request->isMethod("GET"))
        {
            $usuarioId = $request->get("usuarioId");
            $suscripcionId = $request->get("suscripcionId");
            
            $usuario = $this->getDoctrine()
                ->getRepository(Usuario::class)
                ->findOneBy(["id" => $usuarioId]);

            $suscripcion = $this->getDoctrine()
                ->getRepository(Suscripcion::class)
                ->findOneBy(["id" => $suscripcionId]);

            $pago = $this->getDoctrine()
                ->getRepository(Pago::class)
                ->findOneBy(["suscripcion" => $suscripcion]);

            // Problemas de conversión de datos debido a que Doctrine no admite tipo YEAR
            /*$tarjetaCredito = $this->getDoctrine()
                ->getRepository(TarjetaCredito::class)
                ->findOneBy(["formaPago" => $pago->getFormaPago()]);
            
            $paypal = $this->getDoctrine()
                ->getRepository(Paypal::class)
                ->findOneBy(["formaPago" => $pago->getFormaPago()]);*/

            if ($usuario->getId() == $suscripcion->getPremiumUsuario()->getUsuario()->getId()) {
                $respuesta = $serializer->serialize(
                    $suscripcion,
                    "json",
                    ["groups" => ["suscripcionUsuario"]]
                );

                return new Response($respuesta);
            }

            return new JsonResponse(["msg" => "Usuario y/o suscripcion no encontrado"], 404);
        }

        return new JsonResponse(["msg" => $request->getMethod() . " no permitido"]);
    }
}
