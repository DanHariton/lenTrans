<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueRoomName extends Constraint
{
    /** @var string */
    public $message = 'Room with that name already exists';
}