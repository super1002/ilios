<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\MeshDescriptorManager;
use App\Entity\Manager\UserManager;
use App\Message\CourseIndexRequest;
use App\Message\UserIndexRequest;
use App\Service\Index;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Populates the search index with documents
 *
 * Class PopulateIndexCommand
 */
class PopulateIndexCommand extends Command
{
    /**
     * @var Index
     */
    protected $index;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var CourseManager
     */
    protected $courseManager;

    /**
     * @var MeshDescriptorManager
     */
    protected $descriptorManager;

    /**
     * @var MessageBusInterface
     */
    protected $bus;

    public function __construct(
        Index $index,
        UserManager $userManager,
        CourseManager $courseManager,
        MeshDescriptorManager $descriptorManager,
        MessageBusInterface $bus
    ) {
        parent::__construct();

        $this->index = $index;
        $this->userManager = $userManager;
        $this->courseManager = $courseManager;
        $this->descriptorManager = $descriptorManager;
        $this->bus = $bus;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:populate-index')
            ->setDescription('Populate the search index with documents.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Clearing the index and preparing to insert data.</info>");
        $this->index->clear();
        $output->writeln("<info>Ok.</info>");
        ProgressBar::setFormatDefinition(
            'normal',
            "<info>%message%</info>\n%current%/%max% [%bar%]"
        );
        $this->populateUsers($output);
        $this->populateCourses($output);
        $this->populateMesh($output);
        $output->writeln("");
        $output->writeln("Index Populated!");
    }

    protected function populateUsers(OutputInterface $output)
    {
        $allIds = $this->userManager->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, UserIndexRequest::MAX_USERS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new UserIndexRequest($ids));
        }
        $output->writeln("<info>${count} users have been queued for indexing.</info>");
    }

    protected function populateCourses(OutputInterface $output)
    {
        $allIds = $this->courseManager->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }
        $output->writeln("<info>${count} courses have been queued for indexing.</info>");
    }

    protected function populateMesh(OutputInterface $output)
    {
        $allIds = $this->descriptorManager->getIds();
        $progressBar = new ProgressBar($output, count($allIds));
        $progressBar->setMessage('Adding MeSH...');
        $progressBar->start();
        $chunks = array_chunk($allIds, 500);
        foreach ($chunks as $ids) {
            $descriptors = $this->descriptorManager->getIliosMeshDescriptorsById($ids);
            $this->index->indexMeshDescriptors($descriptors);
            $progressBar->advance(count($ids));
        }
        $progressBar->setMessage(count($allIds) . " Descriptors Added!");
        $progressBar->finish();
    }
}
