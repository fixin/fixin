<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\PrototypeInterface;

interface CargoInterface extends PrototypeInterface
{
    public const
        OPTION_CONTENT = 'content',
        OPTION_CONTENT_TYPE = 'contentType',
        OPTION_DELIVERED = 'delivered';

    public function getContent();
    public function getContentType(): string;
    public function isDelivered(): bool;
    public function setContent($content): CargoInterface;
    public function setContentType(string $contentType): CargoInterface;
    public function setDelivered(bool $delivered): CargoInterface;
    public function unpack(): CargoInterface;
}
