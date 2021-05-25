<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomParticipant;
use App\Entity\User;
use App\Form\RoomCreateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room")
 * Class RoomController
 * @package App\Controller
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/new-room", name="new_room")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createRoom(Request $request, EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(RoomCreateType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Room $room */
            $room = $form->getData();
            $room->setOwner($user);
            $em->persist($room);
            $em->flush();
            $roomParticipant = new RoomParticipant();
            $roomParticipant->setUser($user);
            $roomParticipant->setRoom($room);
            $roomParticipant->setRole(RoomParticipant::ROLE_ADMIN);
            $em->persist($roomParticipant);
            $em->flush();

            //TODO: redirect to room
        }

        return $this->render('chat/new_room.html.twig', [
            'form' => $form->createView()
        ]);
    }
}