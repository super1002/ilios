<?php

declare(strict_types=1);

namespace App\Classes;

use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use App\Annotation as IS;

/**
 * Class CurrentSession
 *
 * @IS\DTO
 */
class CurrentSession
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $userId;

    /**
     * Constructor
     * @param  SessionUserInterface $user
     */
    public function __construct(SessionUserInterface $user)
    {
        $this->userId = $user->getId();
    }

    /**
     * Get the user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
