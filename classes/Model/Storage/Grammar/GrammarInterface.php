<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Grammar;

use DateTimeImmutable;
use Fixin\Base\Sentence\SentenceInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface GrammarInterface extends ResourceInterface
{
    public const
        IDENTIFIER_QUOTE = '`',
        IDENTIFIER_SEPARATOR = '.',
        STRING_QUOTE = "'";

    /**
     * Delete
     *
     * @param RequestInterface $request
     * @return SentenceInterface
     */
    public function delete(RequestInterface $request): SentenceInterface;

    /**
     * Insert
     *
     * @param RepositoryInterface $repository
     * @param array $set
     * @return SentenceInterface
     */
    public function insert(RepositoryInterface $repository, array $set): SentenceInterface;

    /**
     * Insert into
     *
     * @param RepositoryInterface $repository
     * @param RequestInterface $request
     * @return SentenceInterface
     */
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): SentenceInterface;

    /**
     * Insert multiple
     *
     * @param RepositoryInterface $repository
     * @param array $rows
     * @return SentenceInterface
     */
    public function insertMultiple(RepositoryInterface $repository, array $rows): SentenceInterface;

    /**
     * Select
     *
     * @param RequestInterface $request
     * @return SentenceInterface
     */
    public function select(RequestInterface $request): SentenceInterface;

    /**
     * Select exists value
     *
     * @param RequestInterface $request
     * @return SentenceInterface
     */
    public function selectExistsValue(RequestInterface $request): SentenceInterface;

    /**
     * Convert value to DateTime
     *
     * @param $value
     * @return DateTimeImmutable|null
     */
    public function toDateTime($value): ?DateTimeImmutable;

    /**
     * Update
     *
     * @param array $set
     * @param RequestInterface $request
     * @return SentenceInterface
     */
    public function update(array $set, RequestInterface $request): SentenceInterface;
}
