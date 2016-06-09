<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\NodeInterface;
use Fixin\Resource\Resource;
use Fixin\View\ViewInterface;

class WrapInView extends Resource implements NodeInterface {

    const ALLOWED_TYPES = ['text/html'];
    const EXCEPTION_NO_TEMPLATE_SET = 'No template set';

    /**
     * @var string
     */
    protected $contentName = 'content';

    /**
     * @var string
     */
    protected $template = '';

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (empty($this->template)) {
            throw new RuntimeException(static::EXCEPTION_NO_TEMPLATE_SET);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (in_array($cargo->getContentType(), static::ALLOWED_TYPES)) {
            $content = $cargo->getContent();

            $view = $this->container->clonePrototype('View\View');
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

    /**
     * Set content name
     *
     * @param string $contentName
     */
    protected function setContentName(string $contentName) {
        $this->contentName = $contentName;
    }

    /**
     * Set template
     *
     * @param string $template
     */
    protected function setTemplate(string $template) {
        $this->template = $template;
    }
}