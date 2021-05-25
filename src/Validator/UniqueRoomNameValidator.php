<?php

namespace App\Validator;

use App\Repository\RoomRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueRoomNameValidator extends ConstraintValidator
{
    /** @var RoomRepository */
    private $roomRepository;

    /**
     * UniqueRoomNameValidator constructor.
     * @param RoomRepository $roomRepository
     */
    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws NonUniqueResultException
     */
    public function validate($value, Constraint $constraint)
    {
        $existingRoomName = $this->roomRepository->findOneByName($value);

        if (!$existingRoomName) {
            return;
        }

        /**
         * @var $constraint UniqueRoomName
         * @psalm-suppress UndefinedMagicPropertyFetch
         */
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}