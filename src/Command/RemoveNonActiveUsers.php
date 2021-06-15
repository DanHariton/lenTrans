<?php

namespace App\Command;

use App\Entity\Room;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveNonActiveUsers extends Command
{
    const DELETE_TIME = 5;

    /** @var string */
    protected static $defaultName = 'app:remove-non-active-rooms';

    /** @var RoomRepository */
    private $roomRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var MessageRepository */
    private $messageRepository;

    /**
     * RemoveNonActiveUsers constructor.
     * @param RoomRepository $roomRepository
     * @param EntityManagerInterface $em
     * @param MessageRepository $messageRepository
     */
    public function __construct(RoomRepository $roomRepository, EntityManagerInterface $em, MessageRepository $messageRepository)
    {
        $this->roomRepository = $roomRepository;
        $this->em = $em;
        $this->messageRepository = $messageRepository;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setDescription('Removes all users from rooms that have been inactive for more than 5 hours');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Room[]|null $rooms */
        $rooms = $this->roomRepository->findAll();

        $date = new DateTime();

        foreach ($rooms as $room) {
            $roomParticipants = $room->getRoomParticipants();
            foreach ($roomParticipants as $roomParticipant) {
                $messages = $this->messageRepository->findByRoomIdAndUserId($room->getId(), $roomParticipant->getUser()->getId());
                if (!is_null($messages)) {
                    $diff = $date->diff($messages->last()->getTime());
                    $hours = $diff->h;
                    $hours = $hours + ($diff->days * 24);
                    if ($hours >= self::DELETE_TIME) {
                        if ($room->getOwner()->getId() == $roomParticipant->getUser()->getId()) {
                            foreach ($roomParticipants as $participant) {
                                if ($participant->getUser()->getId() != $roomParticipant->getUser()->getId()) {
                                    $room->setOwner($participant->getUser());
                                    $this->em->persist($room);
                                    break;
                                }
                            }
                        }
                        foreach ($messages as $message) {
                            $this->em->remove($message);
                        }

                        $this->em->remove($roomParticipant);
                        $this->em->remove($roomParticipant->getUser());
                        $this->em->flush();
                    }
                }
            }

            if (count($roomParticipants) < 1) {
                $this->em->remove($room);
                $this->em->flush();
            }
        }

        return Command::SUCCESS;
    }
}