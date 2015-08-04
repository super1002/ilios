<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserMadeReminderInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserMadeReminderVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserMadeReminderVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     */
    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\UserMadeReminderInterface');
    }

    /**
     * @param string $attribute
     * @param UserMadeReminderInterface $reminder
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $reminder, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // only the user who created the reminder in the first place can access it.
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return ($user->getId() === $reminder->getUser()->getId());
                break;
        }

        return false;
    }
}
