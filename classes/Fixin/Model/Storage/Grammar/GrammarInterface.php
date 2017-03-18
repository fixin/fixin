<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage\Grammar;

use DateTime;
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

    public function delete(RequestInterface $request): SentenceInterface;
    public function getValueAsDateTime($value): ?DateTime;
    public function insert(RepositoryInterface $repository, array $set): SentenceInterface;
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): SentenceInterface;
    public function insertMultiple(RepositoryInterface $repository, array $rows): SentenceInterface;
    public function select(RequestInterface $request): SentenceInterface;
    public function selectExistsValue(RequestInterface $request): SentenceInterface;
    public function update(array $set, RequestInterface $request): SentenceInterface;
}
