<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoomParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param RoomParticipantRepository $participantRepository
     * @return Response
     */
    public function index(RoomParticipantRepository $participantRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('chat/index.html.twig', [
            'user' => $user
        ]);
    }
}