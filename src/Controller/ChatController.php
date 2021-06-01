<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomParticipant;
use App\Entity\User;
use App\Repository\RoomParticipantRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chat")
 * Class ChatController
 * @package App\Controller
 */
class ChatController extends AbstractController
{
    /**
     * @Route("/", name="chat_index")
     * @return Response
     */
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('chat/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/all-rooms", name="all_rooms")
     * @param RoomRepository $roomRepository
     * @return Response
     */
    public function allRooms(RoomRepository $roomRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $rooms = $roomRepository->findAll();

        return $this->render('chat/all_rooms.html.twig', [
            'rooms' => $rooms,
            'user' => $user
        ]);
    }

    /**
     * @Route("/chat-room/{room}", name="chat_room")
     * @param Room $room
     * @param EntityManagerInterface $em
     * @param RoomParticipantRepository $participantRepository
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function chatRoom(Room $room, EntityManagerInterface $em, RoomParticipantRepository $participantRepository, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($room->getStatus() == Room::STATUS_PRIVATE && !$participantRepository->findOneByUserId($user->getId(), $room->getId())) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!$participantRepository->findOneByUserId($user->getId(), $room->getId())) {
            $participant = new RoomParticipant();
            $participant->setRoom($room);
            $participant->setUser($user);
            $participant->setRole(RoomParticipant::ROLE_USER);
            $em->persist($participant);
            $em->flush();
            $room->addRoomParticipants($participant);
        }

        if (!isset($participant)) {
            /** @var RoomParticipant $participant */
            $participant = $participantRepository->findOneByUserId($user->getId(), $room->getId());
        }

        $role = $participant->getRole();

        return $this->render('chat/room.html.twig', [
            'room' => $room,
            'user' => $user,
            'role' => $role
        ]);
    }

    /**
     * @Route("/leave-room/{room}", name="leave_chat_room")
     * @param Room $room
     * @param RoomParticipantRepository $roomParticipantRepository
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function leaveChatRoom(Room $room, RoomParticipantRepository $roomParticipantRepository, EntityManagerInterface $em, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var RoomParticipant $roomParticipant */
        $roomParticipant = $roomParticipantRepository->findOneByUserId($user->getId(), $room->getId());

        if (count($room->getRoomParticipants()) == 1) {
            $this->addFlash('warning', 'The room was removed because there were no more members in it.');

            return $this->redirectToRoute('delete_room', ['room' => $room->getId()]);
        } else {
            if ($room->getOwner()->getId() == $user->getId()) {
                foreach ($room->getRoomParticipants() as $participant) {
                    if ($participant->getUser()->getId() != $user->getId()) {
                        $room->setOwner($participant->getUser());
                        $em->persist($room);
                        break;
                    }
                }
            }
            $room->removeRoomParticipants($roomParticipant);
            $em->persist($room);
            $em->remove($roomParticipant);
            $em->flush();
            $this->addFlash('success', 'You have successfully left the room.');
            return $this->redirect($request->headers->get('referer'));
        }
    }
}