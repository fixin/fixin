<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
            self::OPTION_CONTENT_NAME => self::TYPE_STRING,
            self::OPTION_TEMPLATE => self::TYPE_STRING
        ];

    public const
        OPTION_CONTENT_NAME = 'contentName',
        OPTION_TEMPLATE = 'template';

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

            $view = $this->container->clone('View\View');
            $view->setTemplate($this->template);
            $cargo->setContent($view);

            if ($content instanceof ViewInterface) {
                $view->setChild($this->contentName, $content);

                return $cargo;
            }

            $view->setVariable($this->contentName, $content);
        }

        return $cargo;
    }

    protected function setContentName(string $contentName): void
    {
        $this->contentName = $contentName;
    }

    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
