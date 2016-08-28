<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository;

use Fixin\Model\Repository\Where\WhereBetween;
use Fixin\Model\Repository\Where\WhereCompare;
use Fixin\Model\Repository\Where\WhereExists;
use Fixin\Model\Repository\Where\WhereIn;
use Fixin\Model\Repository\Where\WhereInterface;
use Fixin\Model\Repository\Where\WhereNull;
use Fixin\Model\Repository\Where\WhereRequest;
use Fixin\Resource\Prototype;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RepositoryRequest extends Prototype implements RequestInterface {





    /**
     * Add where
     *
     * @param string $join
     * @param RequestInterface|array|\Closure $where
     */
    protected function addWhere(string $join, $where) {
        // Request, Closure
        if ($where instanceof RequestInterface || $where instanceof \Closure) {
            $this->wheres[] = $this->container->clonePrototype(static::WHERE_REQUEST_PROTOTYPE, [
                WhereRequest::OPTION_REQUEST => $where
            ]);

            return;
        }

        // Array
        if (is_array($where)) {
            $this->addWhereArray($join, $where);

            return;
        }

        // TODO: gettype?
        throw new InvalidArgumentException(static::EXCEPTION_INVALID_WHERE_TYPE);
    }

    /**
     * Add wheres defined by array
     *
     * @param string $join
     * @param array $where
     */
    protected function addWhereArray(string $join, array $where) {
        $this->addWhere($join, function(RequestInterface $request) use ($where) {
            foreach ($where as $key => $value) {
                // Simple where (no key)
                if (is_numeric($key)) {
                    $request->where($value);

                    continue;
                }

                // Key - array value
                if (is_array($value)) {
                    $request->whereIn($key, $value);

                    continue;
                }

                // Key - any value
                $request->whereCompare($key, static::OPERATOR_EQUALS, $value);
            }
        });
    }


    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhere($where)
     */
    public function orWhere($where): RequestInterface {
        $this->addWhere(WhereInterface::JOIN_OR, $where);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereBetween($identifier, $min, $max)
     */
    public function orWhereBetween(string $identifier, $min, $max): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_BETWEEN_PROTOTYPE, [
            WhereBetween::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereBetween::OPTION_IDENTIFIER => $identifier,
            WhereBetween::OPTION_MIN => $min,
            WhereBetween::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereCompare($left, $operator, $right)
     */
    public function orWhereCompare($left, string $operator, $right): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_COMPARE_PROTOTYPE, [
            WhereCompare::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereCompare::OPTION_LEFT => $left,
            WhereCompare::OPTION_OPERATOR => $operator,
            WhereCompare::OPTION_RIGHT => $right
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereExists($request)
     */
    public function orWhereExists(RequestInterface $request): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_EXISTS_PROTOTYPE, [
            WhereExists::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereExists::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereIn($identifier, $values)
     */
    public function orWhereIn(string $identifier, array $values): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_IN_PROTOTYPE, [
            WhereIn::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereIn::OPTION_IDENTIFIER => $identifier,
            WhereIn::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereNotBetween($identifier, $min, $max)
     */
    public function orWhereNotBetween(string $identifier, $min, $max): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_BETWEEN_PROTOTYPE, [
            WhereBetween::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereBetween::OPTION_NEGATED => true,
            WhereBetween::OPTION_IDENTIFIER => $identifier,
            WhereBetween::OPTION_MIN => $min,
            WhereBetween::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereNotExists($request)
     */
    public function orWhereNotExists(RequestInterface $request): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_EXISTS_PROTOTYPE, [
            WhereExists::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereExists::OPTION_NEGATED => true,
            WhereExists::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereNotIn($identifier, $values)
     */
    public function orWhereNotIn(string $identifier, array $values): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_IN_PROTOTYPE, [
            WhereIn::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereIn::OPTION_NEGATED => true,
            WhereIn::OPTION_IDENTIFIER => $identifier,
            WhereIn::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::orWhereNotNull($identifier)
     */
    public function orWhereNotNull(string $identifier): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_NULL_PROTOTYPE, [
            WhereNull::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereNull::OPTION_NEGATED => true,
            WhereNull::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNull($identifier)
     */
    public function orWhereNull(string $identifier): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_NULL_PROTOTYPE, [
            WhereNull::OPTION_JOIN => WhereInterface::JOIN_OR,
            WhereNull::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }



    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::update($set)
     */


    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::where($where)
     */
    public function where($where): RequestInterface {
        $this->addWhere(WhereInterface::JOIN_AND, $where);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereBetween($identifier, $min, $max)
     */
    public function whereBetween(string $identifier, $min, $max): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_BETWEEN_PROTOTYPE, [
            WhereBetween::OPTION_IDENTIFIER => $identifier,
            WhereBetween::OPTION_MIN => $min,
            WhereBetween::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereCompare($left, $operator, $right)
     */
    public function whereCompare($left, string $operator, $right): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_COMPARE_PROTOTYPE, [
            WhereCompare::OPTION_LEFT => $left,
            WhereCompare::OPTION_OPERATOR => $operator,
            WhereCompare::OPTION_RIGHT => $right
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereExists($request)
     */
    public function whereExists(RequestInterface $request): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_EXISTS_PROTOTYPE, [
            WhereExists::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereIn($identifier, $values)
     */
    public function whereIn(string $identifier, array $values): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_IN_PROTOTYPE, [
            WhereIn::OPTION_IDENTIFIER => $identifier,
            WhereIn::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNotBetween($identifier, $min, $max)
     */
    public function whereNotBetween(string $identifier, $min, $max): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_BETWEEN_PROTOTYPE, [
            WhereBetween::OPTION_NEGATED => true,
            WhereBetween::OPTION_IDENTIFIER => $identifier,
            WhereBetween::OPTION_MIN => $min,
            WhereBetween::OPTION_MAX => $max
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNotExists($request)
     */
    public function whereNotExists(RequestInterface $request): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_EXISTS_PROTOTYPE, [
            WhereExists::OPTION_NEGATED => true,
            WhereExists::OPTION_REQUEST => $request
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNotIn($identifier, $values)
     */
    public function whereNotIn(string $identifier, array $values): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_IN_PROTOTYPE, [
            WhereIn::OPTION_NEGATED => true,
            WhereIn::OPTION_IDENTIFIER => $identifier,
            WhereIn::OPTION_VALUES => $values
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNotNull($identifier)
     */
    public function whereNotNull(string $identifier): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_NULL_PROTOTYPE, [
            WhereNull::OPTION_NEGATED => true,
            WhereNull::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::whereNull($identifier)
     */
    public function whereNull(string $identifier): RequestInterface {
        $this->wheres[] = $this->container->clonePrototype(static::WHERE_NULL_PROTOTYPE, [
            WhereNull::OPTION_IDENTIFIER => $identifier
        ]);

        return $this;
    }
}