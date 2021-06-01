<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomParticipant;
use App\Entity\User;
use App\Form\RoomCreateType;
use App\Repository\RoomParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chat")
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

            return $this->redirectToRoute('chat_room', ['room' => $room->getId()]);
        }

        return $this->render('chat/new_room.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-room/{room}", name="delete_room")
     * @param Room $room
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteRoom(Room $room, EntityManagerInterface $em, Request $request)
    {
        if (count($room->getRoomParticipants()) == 1) {
            foreach ($room->getRoomParticipants() as $participant) {
                $em->remove($participant);
                $em->flush();
            }

            $em->remove($room);
            $em->flush();
            $this->addFlash('success', 'Room successfully deleted');
            return $this->redirect($request->headers->get('referer'));
        } else {
            $this->addFlash('danger', 'You cannot delete a room while there are other members in it!');
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/room/status/toggle/{room}", name="room_status_toggle")
     * @param Room $room
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     */
    public function roomStatusToggle(Room $room, EntityManagerInterface $em, Request $request)
    {
        $room->setStatus(!$room->getStatus());
        $em->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/room/{room}/add-admin/{participant}", name="add_admin")
     * @param Room $room
     * @param RoomParticipant $participant
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     */
    public function addAdmin(Room $room, RoomParticipant $participant, EntityManagerInterface $em, Request $request)
    {
        $room->removeRoomParticipants($participant);
        $participant->setRole(RoomParticipant::ROLE_ADMIN);
        $room->addRoomParticipants($participant);
        $em->persist($participant);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/room/{room}/remove-participant/{participant}", name="remove_participant_room")
     * @param Room $room
     * @param RoomParticipant $participant
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeParticipantRoom(Room $room, RoomParticipant $participant,
                                          EntityManagerInterface $em, Request $request)
    {
        $room->removeRoomParticipants($participant);
        $em->remove($participant);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/room/{room}/make-owner/{participant}", name="make_owner_room")
     * @param Room $room
     * @param RoomParticipant $participant
     * @param RoomParticipantRepository $roomParticipantRepository
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function makeOwnerRoom(Room $room, RoomParticipant $participant,
                                  RoomParticipantRepository $roomParticipantRepository, EntityManagerInterface $em,
                                  Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var RoomParticipant $prevOwnerParticipant */
        $prevOwnerParticipant = $roomParticipantRepository->findOneByUserId($user->getId(), $room->getId());

        // Set new owner of Room
        $room->setOwner($participant->getUser());
        $em->persist($room);

        // Set role Admin in room to new owner
        $participant->setRole(RoomParticipant::ROLE_ADMIN);
        $em->persist($participant);

        // Set role User for previous owner
        $prevOwnerParticipant->setRole(RoomParticipant::ROLE_USER);
        $em->persist($prevOwnerParticipant);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}