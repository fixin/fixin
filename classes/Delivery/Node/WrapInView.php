<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Types;
use Fixin\View\ViewInterface;

class WrapInView extends Resource implements NodeInterface
{
    public const
        CARGO_NAME = 'cargoName',
        CONTENT_NAME = 'contentName',
        TEMPLATE = 'template';

    protected const
        ALLOWED_TYPES = ['text/html'],
        THIS_SETS = [
            self::CARGO_NAME => Types::STRING,
            self::CONTENT_NAME => Types::STRING,
            self::TEMPLATE => Types::STRING
        ];

    /**
     * @var string
     */
    protected $cargoName = 'cargo';

    /**
     * @var string
     */
    protected $contentName = 'content';

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @inheritDoc
     */
    public function handle(CargoInterface $cargo): CargoInterface
    {
        if (in_array($cargo->getContentType(), static::ALLOWED_TYPES)) {
            $content = $cargo->getContent();

            /** @var ViewInterface $view */
            $view = $this->resourceManager->clone('*\View\View', ViewInterface::class, [
                ViewInterface::TEMPLATE => $this->template
            ]);
            $cargo->setContent($view);

            if ($content instanceof ViewInterface) {
                $view->setChild($this->contentName, $content);

                return $cargo;
            }

            if (is_array($content)) {
                $view->setMultipleVariables($content);
            }
            else {
                $view->setVariable($this->contentName, $content);
            }

            $view->setVariable($this->cargoName, $cargo);
        }

        return $cargo;
    }
}
