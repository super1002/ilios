<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ConceptsEntityInterface;
use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Traits\TimestampableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshTermInterface
 */
interface MeshTermInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    ConceptsEntityInterface
{

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid);

    /**
     * @return string
     */
    public function getMeshTermUid();

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag);

    /**
     * @return string
     */
    public function getLexicalTag();

    /**
     * @param boolean $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred);

    /**
     * @return boolean
     */
    public function isConceptPreferred();

    /**
     * @param boolean $recordPreferred
     */
    public function setRecordPreferred($recordPreferred);

    /**
     * @return boolean
     */
    public function isRecordPreferred();

    /**
     * @param boolean $permuted
     */
    public function setPermuted($permuted);

    /**
     * @return boolean
     */
    public function isPermuted();
}
