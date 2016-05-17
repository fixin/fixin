<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Facility;

interface FacilityInterface {

    /**
     * Handle cargo
     *
     * @param CargoInterface $cargo
     * @return CargoInterface
     */
    public function handle(CargoInterface $cargo);
}