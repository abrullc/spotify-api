<?php

namespace App\Controller;
# Fichero de Julio

use App\Entity\Capitulo;
use App\Entity\Podcast;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class CapituloController extends AbstractController
{
    public function capitulosPodcast(Request $request, SerializerInterface $serializer)
    {        
        $id = $request->get("id");
        $capitulo = $this->getDoctrine()
            ->getRepository(Capitulo::class)
            ->findBy(["podcast" => $id]);
        $capitulo = $serializer->serialize(
            $capitulo,
            "json",
            ["groups" => ["capitulo"]]
        );
        return new Response($capitulo);
    }
    public function capituloPodcast(Request $request, Request $request2, SerializerInterface $serializer)
    {
        $id = $request->get("podcastId");
        $id2 = $request->get("capituloId");
        
        $podcast = $this->getDoctrine()
            ->getRepository(Podcast::class)
            ->findOneBy(["id" => $id]);

        $capitulo = $this->getDoctrine()
            ->getRepository(Capitulo::class)
            ->findOneBy(["id" => $id2]);

        if ($podcast->getId() == $capitulo->getPodcast()->getId()) {
                
            $respuesta = $serializer->serialize(
                $capitulo,
                "json",
                ["groups" => ["capitulo"]]
            );
            return new Response($respuesta);
        }
        
    }
}
