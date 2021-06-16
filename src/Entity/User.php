<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var RoomParticipant[]|ArrayCollection
     * Many Users have Many Groups.
     * @ORM\OneToMany(targetEntity="RoomParticipant", mappedBy="user")
     */
    private $roomParticipants;

    /**
     * User constructor.
     */
    public function __construct() {
        $this->roomParticipants = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $roomParticipant->setUser($this);
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
            // set the owning side to null (unless already changed)
            if ($roomParticipant->getUser() === $this) {
                $roomParticipant->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRooms()
    {
        $rooms = [];

        foreach ($this->roomParticipants as $roomParticipant) {
            $rooms[] = $roomParticipant->getRoom();
        }

        return $rooms;
    }

    /**
     * @return array
     */
    public function getOwnedRooms()
    {
        $rooms = [];

        foreach ($this->roomParticipants as $roomParticipant) {
            if ($roomParticipant->getRoom()->getOwner()->getId() == $this->getId()) {
                $rooms[] = $roomParticipant->getRoom();
            }
        }

        return $rooms;
    }
}
