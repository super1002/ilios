<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

#[IA\DTO('meshConcepts')]
#[IA\ExposeGraphQL]
class MeshConceptDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $preferred;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $scopeNote;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $casn1Name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $registryNumber;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshTerms')]
    #[IA\Type('array<string>')]
    public array $terms = [];

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshDescriptors')]
    #[IA\Type('array<string>')]
    public array $descriptors = [];

    public function __construct(
        string $id,
        string $name,
        bool $preferred,
        ?string $scopeNote,
        ?string $casn1Name,
        ?string $registryNumber,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->preferred = $preferred;
        $this->scopeNote = $scopeNote;
        $this->casn1Name = $casn1Name;
        $this->registryNumber = $registryNumber;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
