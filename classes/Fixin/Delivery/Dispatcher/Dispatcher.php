<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Dispatcher;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\ResourceManager\Resource;
use Fixin\ResourceManager\ResourceManagerInterface;
use Fixin\Delivery\Facility\FacilityInterface;

class Dispatcher extends Resource implements DispatcherInterface {

    const FACILITIES_KEY = 'facilities';

    /**
     * @var FacilityInterface[]
     */
    protected $facilities = [];

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $container, array $options = []) {
        parent::__construct($container, $options);

        // Facilities
        if (isset($config[static::FACILITIES_KEY])) {
            $this->setupFacilities($options[static::FACILITIES_KEY]);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Dispatcher\DispatcherInterface::dispatch()
     */
    public function dispatch(CargoInterface $cargo) {
        $cargo->setDelivered(false);
        $plan = $this->facilities;

        while ($plan) {
            $cargo = array_shift($plan)->dispatch($cargo);

            if ($cargo->isDelivered()) {
                break;
            }
        }

        return $cargo;
    }

    /**
     * Setup facilities
     *
     * @param array $facilities
     * @throws InvalidParameterException
     */
    protected function setupFacilities(array $facilities) {
        foreach ($facilities as $key => $facility) {
            $facility = $this->container->get($facility);

            if (!$facility instanceof FacilityInterface) {
                throw new InvalidParameterException("Invalid facility resource '$key'");
            }

            $this->facilities[] = $this->container->get($facility);
        }
    }
}