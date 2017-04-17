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
use DateTimeInterface;
use Fixin\Base\Sentence\SentenceInterface;
use Fixin\Model\Entity\EntityIdInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Request\ExpressionInterface;
use Fixin\Model\Request\RequestInterface;
use Fixin\Model\Request\Where\Tag\BetweenTag;
use Fixin\Model\Request\Where\Tag\CompareTag;
use Fixin\Model\Request\Where\Tag\ExistsTag;
use Fixin\Model\Request\Where\Tag\InTag;
use Fixin\Model\Request\Where\Tag\NullTag;
use Fixin\Model\Request\Where\Tag\WhereTag;
use Fixin\Model\Request\Where\WhereInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Numbers;

abstract class Grammar extends Resource implements GrammarInterface
{
    protected const
        ALIAS_MASK = '%s AS %s',
        ARRAY_MASK = '(%s)',
        ASCENDING_ORDER = 'ASC',
        BETWEEN_MASK = 'BETWEEN %s AND %s',
        DATETIME_FORMAT = 'Y-m-d H:i:s',
        DESCENDING_ORDER = 'DESC',
        EXISTS_MASK = 'EXISTS(%s)',
        EXPRESSION_TERMINALS = "\n\r\t '\"`()[]+-*/<>!=&|^,?@",
        IDENTIFIER_CLOSE_QUOTE = "`",
        IDENTIFIER_OPEN_QUOTE = "`",
        IN_MASK = 'IN %s',
        IS_NULL_WHERE_TAG = [false => 'IS NOT NULL', true => 'IS NULL'],
        LIMIT_MASK = 'LIMIT %s' . PHP_EOL,
        LIST_SEPARATOR = ', ',
        MULTI_LINE_LIST_SEPARATOR = ',' . PHP_EOL . "\t",
        MULTI_LINE_NESTED_MASK = '(' . PHP_EOL . "\t%s)" . PHP_EOL,
        NEGATE_WHERE_TAG = [false => '', true => 'NOT '],
        NESTED_MASK = "(%s)",
        OFFSET_MASK = 'OFFSET %s' . PHP_EOL,
        ORDER_BY_MASK = 'ORDER BY %s' . PHP_EOL,
        ORDER_BY_ITEM_MASK = '%s %s',
        PLACEHOLDER = '?',
        WHERE_TAG_METHOD = 'whereTag',
        WHERE_TAG_SEPARATOR = PHP_EOL . "\t %s ";

    protected function expressionArrayToString(array $expression, SentenceInterface $sentence): string
    {
        $result = [];
        foreach ($expression as $item) {
            $result[] = $this->expressionToString($item, $sentence);
        }

        return sprintf(static::ARRAY_MASK, implode(static::LIST_SEPARATOR, $result));
    }

    protected function expressionToString($expression, SentenceInterface $sentence): string
    {
        if (is_array($expression)) {
            return $this->expressionArrayToString($expression, $sentence);
        }

        if ($expression instanceof ExpressionInterface) {
            $sentence->addParameters($expression->getParameters());

            return $this->quoteExpression($expression->getExpression());
        }

        if ($expression instanceof DateTimeInterface) {
            $expression = $expression->format(static::DATETIME_FORMAT);
        }
        elseif ($expression instanceof RequestInterface) {
            return sprintf(static::NESTED_MASK, $this->requestToString($expression, $sentence));
        }
        elseif ($expression instanceof EntityIdInterface) {
            $expression = $expression->getArrayCopy();
            if (count($expression) > 1) {
                return $this->expressionArrayToString($expression, $sentence);
            }

            $expression = reset($expression);
        }

        $sentence->addParameter($expression);

        return static::PLACEHOLDER;
    }

    public function insert(RepositoryInterface $repository, array $set): SentenceInterface
    {
        return $this->insertMultiple($repository, [$set]);
    }

    /**
     * @param number|string|array|ExpressionInterface|RequestInterface $identifier
     */
    protected function identifierToString($identifier, SentenceInterface $sentence): string
    {
        // Expression
        if ($identifier instanceof ExpressionInterface) {
            $sentence->addParameters($identifier->getParameters());

            $identifier = $identifier->getExpression();
        }
        // Request
        elseif ($identifier instanceof RequestInterface) {
            return sprintf(static::NESTED_MASK, $this->requestToString($identifier, $sentence));
        }
        elseif (is_array($identifier)) {
            return $this->quoteArrayIdentifier($identifier);
        }

        return $this->quoteIdentifier($identifier);
    }

    /**
     * Limit and offset string
     */
    protected function limitsToString(int $offset, ?int $limit): string
    {
        $result = '';

        if ($offset) {
            $result .= sprintf(static::OFFSET_MASK, $offset);
        }

        if ($limit) {
            $result .= sprintf(static::LIMIT_MASK, $limit);
        }

        return $result;
    }

    /**
     * Name (with alias)
     */
    protected function nameToString(string $name, string $alias = null): string
    {
        if ($name !== $alias && !is_null($alias)) {
            return sprintf(static::ALIAS_MASK, $this->quoteIdentifier($name), $this->quoteIdentifier($alias));
        }

        return $this->quoteIdentifier($name);
    }

    protected function orderByToString(array $orderBy, SentenceInterface $sentence): string
    {
        if ($orderBy) {
            $list = [];

            foreach ($orderBy as $key => $value) {
                if (is_numeric($key)) {
                    $list[] = $this->identifierToString($value, $sentence);

                    continue;
                }

                $list[] = sprintf(static::ORDER_BY_ITEM_MASK, $this->quoteIdentifier($key), strtoupper($value) === static::DESCENDING_ORDER ? static::DESCENDING_ORDER : static::ASCENDING_ORDER);
            }

            return sprintf(static::ORDER_BY_MASK, implode(static::LIST_SEPARATOR, $list));
        }

        return '';
    }

    protected function quoteArrayIdentifier(array $identifier): string
    {
        if (count($identifier) > 1) {
            return sprintf(static::ARRAY_MASK, implode(static::LIST_SEPARATOR, array_map([$this, 'quoteIdentifier'], $identifier)));
        }

        return $this->quoteIdentifier(reset($identifier));
    }

    /**
     * Quote expression (detects identifier-only expressions)
     */
    protected function quoteExpression(string $expression): string
    {
        $trimmed = trim($expression);

        // There is no terminal characters in the trimmed
        if (strcspn($trimmed, static::EXPRESSION_TERMINALS) === strlen($trimmed)) {
            return $this->quoteIdentifier($trimmed);
        }

        // Complex expression
        return $expression;
    }

    protected function quoteIdentifier(string $identifier): string
    {
        $trimmed = trim($identifier);

        // There is no terminal characters in the trimmed
        if (strcspn($trimmed, static::EXPRESSION_TERMINALS) === strlen($trimmed)) {
            $tags = explode(static::IDENTIFIER_SEPARATOR, $trimmed);

            foreach ($tags as &$tag) {
                if ($tag[0] !== static::IDENTIFIER_OPEN_QUOTE) {
                    $tag = static::IDENTIFIER_OPEN_QUOTE . $tag . static::IDENTIFIER_CLOSE_QUOTE;
                }
            }

            return implode(static::IDENTIFIER_SEPARATOR, $tags);
        }

        return $identifier;
    }

    /**
     * Request name string (with alias)
     */
    protected function requestNameToString(RequestInterface $request): string
    {
        return $this->nameToString($request->getRepository()->getName(), $request->getAlias());
    }

    protected function requestToString(RequestInterface $request, SentenceInterface $sentence): string
    {
        $select = $this->select($request);

        $sentence->addParameters($select->getParameters());

        return $select->getText();
    }

    public function toDateTime($value): ?DateTimeImmutable
    {
        if (Numbers::isInt($value)) {
            return new DateTimeImmutable('@' . $value);
        }

        return DateTimeImmutable::createFromFormat(static::DATETIME_FORMAT, $value) ?: null;
    }

    protected function whereTagBetween(BetweenTag $tag, SentenceInterface $sentence): string
    {
        return $this->identifierToString($tag->getIdentifier(), $sentence) . ' ' . static::NEGATE_WHERE_TAG[$tag->isNegated()] . sprintf(static::BETWEEN_MASK, $this->expressionToString($tag->getMin(), $sentence)
            , $this->expressionToString($tag->getMax(), $sentence));
    }

    protected function whereTagCompare(CompareTag $tag, SentenceInterface $sentence): string
    {
        return static::NEGATE_WHERE_TAG[$tag->isNegated()] . $this->expressionToString($tag->getLeft(), $sentence) . ' ' . $tag->getOperator() . ' ' . $this->expressionToString($tag->getRight(), $sentence);
    }

    protected function whereTagExists(ExistsTag $tag, SentenceInterface $sentence): string
    {
        return static::NEGATE_WHERE_TAG[$tag->isNegated()] . sprintf(static::EXISTS_MASK, $this->requestToString($tag->getRequest(), $sentence));
    }

    protected function whereTagIn(InTag $tag, SentenceInterface $sentence): string
    {
        $values = $tag->getValues();

        return $this->identifierToString($tag->getIdentifier(), $sentence) . ' ' . static::NEGATE_WHERE_TAG[$tag->isNegated()] . sprintf(static::IN_MASK, $this->expressionToString($values, $sentence));
    }

    protected function whereTagNull(NullTag $tag, SentenceInterface $sentence): string
    {
        return $this->identifierToString($tag->getIdentifier(), $sentence) . ' ' . static::IS_NULL_WHERE_TAG[!$tag->isNegated()];
    }

    /**
     * Where nested tag
     */
    protected function whereTagWhere(WhereTag $tag, SentenceInterface $sentence): string
    {
        return sprintf(static::MULTI_LINE_NESTED_MASK, $this->whereToString('', $tag->getWhere(), $sentence));
    }

    protected function whereToString(string $clause, WhereInterface $where, SentenceInterface $sentence): string
    {
        if ($tags = $where->getTags()) {
            $result = rtrim($clause);

            foreach ($tags as $index => $tag) {
                if ($index) {
                    $result .= sprintf(static::WHERE_TAG_SEPARATOR, strtoupper($tag->getJoin()));
                }

                $class = get_class($tag);
                $shortName = substr($class, strrpos($class, '\\') + 1, -3);

                $result .= $this->{static::WHERE_TAG_METHOD . $shortName}($tag, $sentence);
            }

            return $result . PHP_EOL;
        }

        return '';
    }
}
