<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlaceRepository;
use App\Entity\Place;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Repository\PersonRepository;
class PlaceController extends AbstractController
{
    /**
     * @Route("/api/place", name="api_place" , methods="GET")
     * @Groups("place:read")
     */
    public function index(PlaceRepository $placeRepository, NormalizerInterface $normalizer): Response
    {
        $places = $placeRepository->findAll();
        $normalized = $normalizer->normalize($places,null,['groups'=>'place:read']);
        $json = json_encode($normalized);
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
        ]);
        return $reponse;
    }
    /**
     * @Route("/api/place/{id}", name="api_place_avec_id" , methods="GET")
     * @Groups("place:read")
     */
    public function findById(PlaceRepository $placeRepository, $id, NormalizerInterface $normalizer): Response 
    {
        $place = $placeRepository->find($id);
        $normalized = $normalizer->normalize($place,null,['groups'=>'place:read']);
        $json = json_encode($normalized);
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
        ]);
        return $reponse;
    }
    /**
* @Route("/api/place/", name="api_place_add",methods="POST")
*/
public function add(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ValidatorInterface $validator) {
    $contenu = $request->getContent();
   try
   {
   $place = $serializer->deserialize($contenu, Place::class, 'json');
   $errors = $validator->validate($place);
   if (count($errors) > 0)
   {
   return $this->json($errors, 400);
   }
   $entityManager->persist($place);
   $entityManager->flush();
   return $this->json($place, 201, [],
   ['groups' => 'place:read']);
   }
   catch (NotEncodableValueException $e)
   {
   return $this->json(['status' => 400,'message' => $e->getMessage()]);
   }
   }
    /**
* @Route("/api/place/{numPlace}/liked/{numPerson}", name="api_place_add",methods="POST")
*/

   public function like(EntityManagerInterface $entityManager, Request $request, PlaceReposiroty $placeRepository, PersonRepository $pPersonRepository, $numPlace,$numPerson){
        $place = $placeRepository->find($numPlace);
        $personne = $pPersonRepository->find($numPerson);
        $place->addLikeBy($personne);
        $entityManager->flush();
        return $this->json($place, 201, [],['groups' => 'place:read']);
    }

}

