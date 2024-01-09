<?php

namespace App\Controller;

use App\Entity\Capitulo;
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
                ["groups" => ["Capitulo"]]
            );
            return new Response($capitulo);
    }
}
