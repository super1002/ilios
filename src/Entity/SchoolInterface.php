<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\AlertableEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\SessionTypesEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\ProgramsEntityInterface;

/**
 * Interface SchoolInterface
 */
interface SchoolInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    IndexableCoursesEntityInterface,
    ProgramsEntityInterface,
    LoggableEntityInterface,
    AlertableEntityInterface,
    SessionTypesEntityInterface,
    InstructorGroupsEntityInterface,
    CompetenciesEntityInterface,
    DirectorsEntityInterface,
    AdministratorsEntityInterface
{
    /**
     * @param string $templatePrefix
     */
    public function setTemplatePrefix($templatePrefix);

    /**
     * @return string
     */
    public function getTemplatePrefix(): string;

    /**
     * @param string $iliosAdministratorEmail
     */
    public function setIliosAdministratorEmail($iliosAdministratorEmail);

    /**
     * @return string
     */
    public function getIliosAdministratorEmail(): string;

    /**
     * @param string $changeAlertRecipients
     */
    public function setChangeAlertRecipients($changeAlertRecipients);

    /**
     * @return string
     */
    public function getChangeAlertRecipients(): string;

    public function setVocabularies(Collection $vocabularies);

    public function addVocabulary(VocabularyInterface $vocabulary);

    public function removeVocabulary(VocabularyInterface $vocabulary);

    /**
     * @return ArrayCollection|VocabularyInterface[]
     */
    public function getVocabularies(): Collection;

    public function addConfiguration(SchoolConfigInterface $config);

    public function removeConfiguration(SchoolConfigInterface $config);

    public function setConfigurations(Collection $configs);

    /**
     * @return ArrayCollection|SchoolConfigInterface[]
     */
    public function getConfigurations(): Collection;
}
