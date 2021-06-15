<?php

namespace App\Command;

use App\Entity\Room;
use App\Repository\RoomRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveNonActiveRooms extends Command
{
    const DELETE_TIME = 12;

    /** @var string */
    protected static $defaultName = 'app:remove-non-active-rooms';

    /** @var RoomRepository */
    private $roomRepository;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * RemoveNonActiveRooms constructor.
     * @param RoomRepository $roomRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(RoomRepository $roomRepository, EntityManagerInterface $em)
    {
        $this->roomRepository = $roomRepository;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removes all rooms where users have been inactive for more than 12 hours');
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
            /** @var ArrayCollection $messages */
            $messages = $room->getMessages();

            /** @var ArrayCollection $roomParticipant */
            $roomParticipants = $room->getRoomParticipants();

            if (!is_null($messages)) {
                $diff = $date->diff($messages->last()->getTime());
                $hours = $diff->h;
                $hours = $hours + ($diff->days * 24);
                if ($hours >= self::DELETE_TIME) {
                    foreach ($messages as $message) {
                        $this->em->remove($message);
                    }

                    foreach ($roomParticipants as $roomParticipant) {
                        $this->em->remove($roomParticipant);
                    }

                    $this->em->remove($room);
                    $this->em->flush();
                }
            }
        }

        return Command::SUCCESS;
    }

}