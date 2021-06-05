<?php

namespace App\Controller\Rest;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageRestController extends AbstractController
{
    /**
     * @Route("/rest/put/message", name="put_rest_message", methods={"PUT"}, defaults={"_format": "json"}, options={"expose"=true})
     * @param Request $request
     * @param RoomRepository $roomRepository
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function putMessage(Request $request, RoomRepository $roomRepository, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());

        /** @var User $user */
        $user = $this->getUser();

        /** @var Room|null $room */
        $room = $roomRepository->findOneById($data->room);

        $message = new Message();
        $message->setUser($user);
        $message->setRoom($room);
        $message->setContent($data->message);
        $message->setTime(new DateTime());

        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            $errorMessages = [];

            foreach ($errors as $error) {
                /** @var ConstraintViolationInterface $error */
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_NOT_ACCEPTABLE);
        }

        $em->persist($message);
        $em->flush();

        return new JsonResponse([
            'status' => 'OK'
        ]);
    }


    /**
     * @Route("/rest/get/new-messages", name="get_rest_new_messages", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param MessageRepository $messageRepository
     * @return Response
     * @throws Exception
     */
    public function getNewMessages(Request $request, MessageRepository $messageRepository)
    {
        $room = $request->query->get('room');
        $time = (new DateTime($request->query->get('time')))->modify('-1 second');

        return new JsonResponse(array_map(function (Message $message) {
            return $message->toArray();
        }, $messageRepository->findByRoomIdAndTime($room, $time)));
    }
}