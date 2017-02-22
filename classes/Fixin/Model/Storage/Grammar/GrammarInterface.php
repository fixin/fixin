<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Grammar;

use DateTime;
use Fixin\Base\Query\QueryInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\ResourceInterface;

interface GrammarInterface extends ResourceInterface
{
    protected const
        IDENTIFIER_QUOTE = '`',
        IDENTIFIER_SEPARATOR = '.',
        STRING_QUOTE = "'";

    public function delete(RequestInterface $request): QueryInterface;
    public function exists(RequestInterface $request): QueryInterface;
    public function getValueAsDateTime($value): ?DateTime;
    public function insert(RepositoryInterface $repository, array $set): QueryInterface;
    public function insertInto(RepositoryInterface $repository, RequestInterface $request): QueryInterface;
    public function insertMultiple(RepositoryInterface $repository, array $rows): QueryInterface;
    public function select(RequestInterface $request): QueryInterface;
    public function update(array $set, RequestInterface $request): QueryInterface;
}
