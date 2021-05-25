<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @var RoomParticipant[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="RoomParticipant", mappedBy="room")
     */
    private $roomParticipants;

    /**
     * @var Message[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Message", mappedBy="room")
     */
    private $messages;

    /**
     * Room constructor.
     */
    public function __construct() {
        $this->roomParticipants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $ownerId): self
    {
        $this->owner = $ownerId;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRoomParticipants()
    {
        return $this->roomParticipants;
    }

    /**
     * @param ArrayCollection $roomParticipants
     */
    public function setRoomParticipants($roomParticipants)
    {
        $this->roomParticipants = $roomParticipants;
    }

    /**
     * @param RoomParticipant $roomParticipant
     * @return $this
     */
    public function addRoomParticipants($roomParticipant): self
    {
        if (!$this->roomParticipants->contains($roomParticipant)) {
            $this->roomParticipants[] = $roomParticipant;
            $roomParticipant->setRoom($this);
        }

        return $this;
    }

    /**
     * @param RoomParticipant $roomParticipant
     * @return $this
     */
    public function removeRoomParticipants($roomParticipant): self
    {
        if ($this->roomParticipants->removeElement($roomParticipant)) {
            if ($roomParticipant->getRoom() === $this) {
                $roomParticipant->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param ArrayCollection $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param Message $message
     * @return $this
     */
    public function addMessage($message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setRoom($this);
        }

        return $this;
    }

    /**
     * @param Room $message
     * @return $this
     */
    public function removeMessage($message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getRoom() === $this) {
                $message->setRoom(null);
            }
        }

        return $this;
    }
}
