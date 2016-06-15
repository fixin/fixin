<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Entity\EntityInterface;
use Fixin\Model\Entity\EntitySetInterface;
use Fixin\Resource\ResourceInterface;

interface RepositoryInterface extends ResourceInterface {

    const OPTION_ENTITY_ID_PROTOTYPE = 'entityIdPrototype';
    const OPTION_ENTITY_PROTOTYPE = 'entityPrototype';
    const OPTION_NAME = 'name';
    const OPTION_PRIMARY_KEY = 'primaryKey';
    const OPTION_STORAGE = 'storage';

//     public function executeRequest(): EntitySetInterface;
//     public function getEntityWithId(EntityIdInterface $entityId);
//     public function getRegisteredEntities(): EntitySet;
//     public function getRegisteredEntityWithId(EntityIdInterface $entityId);



    /**
     * Create entity for the repository
     *
     * @return EntityInterface
     */
    public function createEntity(): EntityInterface;

    /**
     * Create entity ID
     *
     * @param array|int|string ...$entityId
     * @return EntityIdInterface
     */
    public function createEntityId(...$entityId): EntityIdInterface;

    /**
     * Get entity of ID
     *
     * @param EntityIdInterface $entityId
     * @return EntityInterface|null
     */
    public function getEntityWithId(EntityIdInterface $entityId);

    /**
     * Get name of the repository
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Save entity
     *
     * @param EntityInterface $entity
     * @return self
     */
    public function saveEntity(EntityInterface $entity): self;
}