<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 * @package Ilios\CliBUndle\Command
 */
class SyncAllUsersCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    public function __construct(
        UserManagerInterface $userManager,
        AuthenticationManagerInterface $authenticationManager,
        Directory $directory,
        EntityManager $em
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->directory = $directory;
        $this->em = $em;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:sync-users')
            ->setDescription('Sync all users against the directory by their campus id.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userManager->resetExaminedFlagForAllUsers();
        $campusIds = $this->userManager->getAllCampusIds(false, false)->toArray();
        $allUserRecoreds = $this->directory->findByCampusIds($campusIds);
        
        if (!$allUserRecoreds) {
            $output->writeln('<error>Unable to find any users in the directory');
            return;
        }
        $totalRecords = count($allUserRecoreds);
        $updated = 0;
        $chunks = array_chunk($allUserRecoreds, 500);
        foreach ($chunks as $userRecords) {
            foreach ($userRecords as $recordArray) {
                $users = $this->userManager->findUsersBy([
                    'campusId' => $recordArray['campusId'],
                    'enabled' => true,
                    'userSyncIgnore' => false
                ]);
                if ($users->count() == 0) {
                    //this shouldn't happen unless the user gets updated between
                    //listing all the IDs and getting results back from
                    //the directory
                    $output->writeln(
                        '<error>Unable to find an active sync user with ' .
                        'Campus ID ' . $recordArray['campusId'] . '</error>'
                    );
                    continue;
                }
                if ($users->count() > 1) {
                    $output->writeln(
                        '<error>Multiple accounts exist for the same ' .
                        'Campus ID (' . $recordArray['campusId'] . ').  ' .
                        'None of them will be updated.</error>'
                    );
                    foreach ($users as $user) {
                        $user->setExamined(true);
                        $this->userManager->updateUser($user, false);
                    }
                    continue;
                }
                $user = $users->first();

                $update = false;
                $output->writeln(
                    '<info>Comparing User #' . $user->getId() . ' ' .
                    $user->getFirstAndLastName() . ' (' . $user->getEmail() . ') ' .
                    'to directory user by Campus ID ' . $user->getCampusId() . '</info>'
                );
                if (!$this->validateDirectoryRecord($recordArray, $output)) {
                    $user->setExamined(true);
                    $this->userManager->updateUser($user, false);
                    //don't do anything else with invalid directory data
                    continue;
                }
                if ($user->getFirstName() != $recordArray['firstName']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating first name from "' . $user->getFirstName() .
                        '" to "' . $recordArray['firstName'] . '"</comment>'
                    );
                    $user->setFirstName($recordArray['firstName']);
                }
                if ($user->getLastName() != $recordArray['lastName']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating last name from "' . $user->getLastName() .
                        '" to "' . $recordArray['lastName'] . '"</comment>'
                    );
                    $user->setLastName($recordArray['lastName']);
                }
                if ($user->getPhone() != $recordArray['telephoneNumber']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating phone number from "' . $user->getPhone() .
                        '" to "' . $recordArray['telephoneNumber'] . '"</comment>'
                    );
                    $user->setPhone($recordArray['telephoneNumber']);
                }
                
                $authentication = $user->getAuthentication();
                if (!$authentication) {
                    $output->writeln(
                        '<comment>User had no Authentication data, creating it now.</comment>'
                    );
                    $authentication = $this->authenticationManager->createAuthentication();
                    $authentication->setUser($user);
                }
                if ($authentication->getUsername() != $recordArray['username']) {
                    $update = true;
                    $output->writeln(
                        '<comment>Updating username from "' . $authentication->getUsername() .
                        '" to "' . $recordArray['username'] . '"</comment>'
                    );
                    $authentication->setUsername($recordArray['username']);
                    $this->authenticationManager->updateAuthentication($authentication, false);
                }
                
                if ($update) {
                    $updated++;
                }
                $user->setExamined(true);
                $this->userManager->updateUser($user, false);
            }
            $this->em->flush();
            $this->em->clear();
        }
        $output->writeln(
            "<info>Completed Sync Process {$totalRecords} users found in the directory; " .
            "{$updated} users updated.</info>"
        );
        
    }
    
    protected function validateDirectoryRecord(array $record, OutputInterface $output)
    {
        $valid = true;
        $requiredFields = ['firstName', 'lastName', 'email', 'username'];
        foreach ($requiredFields as $key) {
            if (empty($record[$key])) {
                $valid = false;
                $output->writeln(
                    "<error> {$key} is required and it is missing from record with " .
                    'Campus ID (' . $record['campusId'] . ').  ' .
                    'User will not be updated.</error>'
                );
            }
        }
        
        return $valid;
    }
}
