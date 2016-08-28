<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

return;

//namespace Fixin\Model\Repository;

use Fixin\Model\Repository\Where\WhereCompare;
use Fixin\Model\Repository\Where\WhereIn;
use Fixin\Model\Repository\Where\WhereInterface;
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
            $this->tags[] = $this->container->clonePrototype(static::WHERE_REQUEST_PROTOTYPE, [
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
     * @see \Fixin\Model\Repository\RequestInterface::or($where)
     */
    public function or($where): self {
        $this->addWhere(WhereInterface::JOIN_OR, $where);

        return $this;
    }


    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\RequestInterface::where($where)
     */
    public function where($where): self {
        $this->addWhere(WhereInterface::JOIN_AND, $where);

        return $this;
    }

}