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
use Fixin\View\ViewInterface;

class WrapInView extends Resource implements NodeInterface
{
    protected const
        ALLOWED_TYPES = ['text/html'],
        THIS_REQUIRES = [
            self::CONTENT_NAME,
            self::TEMPLATE
        ],
        THIS_SETS = [
            self::CONTENT_NAME => self::STRING_TYPE,
            self::TEMPLATE => self::STRING_TYPE
        ];

    public const
        CONTENT_NAME = 'contentName',
        TEMPLATE = 'template';

    /**
     * @var string
     */
    protected $contentName = 'content';

    /**
     * @var string
     */
    protected $template = '';

    public function handle(CargoInterface $cargo): CargoInterface
    {
        if (in_array($cargo->getContentType(), static::ALLOWED_TYPES)) {
            $content = $cargo->getContent();

            /** @var ViewInterface $view */
            $view = $this->resourceManager->clone('View\View', ViewInterface::class, [
                ViewInterface::TEMPLATE => $this->template
            ]);
            $cargo->setContent($view);

            if ($content instanceof ViewInterface) {
                $view->setChild($this->contentName, $content);

                return $cargo;
            }

            $view->setVariable($this->contentName, $content);
        }

        return $cargo;
    }
}
