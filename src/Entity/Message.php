<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Room
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="messages")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $content;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom(Room $room): void
    {
        $this->room = $room;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param DateTimeInterface $time
     * @return $this
     */
    public function setTime(DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function toArray(): array
    {
        return [
          'id' => $this->getId(),
          'userId' => $this->getUser()->getId(),
          'userName' => $this->getUser()->getUsername(),
          'roomId' => $this->getRoom()->getId(),
          'time' => $this->getTime()->format('H:i'),
          'content' => $this->getContent()
        ];
    }
}
