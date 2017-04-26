<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Base\Container\ContainerInterface;
use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Base\Header\HeadersInterface;
use Fixin\Base\Session\SessionManagerInterface;
use Fixin\Base\Upload\UploadItemInterface;
use Fixin\Base\Uri\UriInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\FactoryInterface;
use Fixin\Resource\ResourceManagerInterface;
use Fixin\Support\Http;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class HttpCargoFactory implements FactoryInterface
{
    protected const
        FORM_MIME_TYPES = ['application/x-www-form-urlencoded', 'multipart/form-data'],
        FORM_PROCESSED_TYPE = 'text/html',
        STREAM_CLASS = 'Fixin\Base\Stream\Stream';

    public function __invoke(ResourceManagerInterface $resourceManager, array $options = null, string $name = null): HttpCargoInterface
    {
        $cookies = $resourceManager->clone('Base\Cookie\CookieManager', CookieManagerInterface::class, [
            CookieManagerInterface::COOKIES => $_COOKIE,
        ]);
        $requestHeaders = $this->getRequestHeaders();

        return $resourceManager->clone('Delivery\Cargo\HttpCargo', HttpCargoInterface::class, [
            HttpCargoInterface::COOKIES => $cookies,
            HttpCargoInterface::ENVIRONMENT => $resourceManager->get('Support\Factory\EnvironmentInfoFactory', ContainerInterface::class),
            HttpCargoInterface::METHOD => $_SERVER['REQUEST_METHOD'],
            HttpCargoInterface::PARAMETERS => $resourceManager->clone('Base\Container\VariableContainer', VariableContainerInterface::class)->replace($_GET),
            HttpCargoInterface::PROTOCOL_VERSION => $this->getRequestProtocolVersion(),
            HttpCargoInterface::REQUEST_HEADERS => $resourceManager->clone('Base\Header\Headers', HeadersInterface::class, [
                HeadersInterface::VALUES => $requestHeaders
            ]),
            HttpCargoInterface::RESPONSE_HEADERS => $resourceManager->clone('Base\Header\Headers', HeadersInterface::class),
            HttpCargoInterface::SERVER => $resourceManager->get('Support\Factory\ServerInfoFactory', ContainerInterface::class),
            HttpCargoInterface::SESSION => $resourceManager->clone('Base\Session\SessionManager', SessionManagerInterface::class, [
                SessionManagerInterface::COOKIE_MANAGER => $cookies
            ]),
            HttpCargoInterface::URI => $resourceManager->clone('Base\Uri\Factory\EnvironmentUriFactory', UriInterface::class),
        ] + $this->getContentOptions($resourceManager, $requestHeaders[Http::CONTENT_TYPE_HEADER] ?? 'text/html'));
    }

    protected function getContentOptions(ResourceManagerInterface $resourceManager, string $contentType): array
    {
        $contentType = strstr($contentType, ';', true);

        if (in_array($contentType, static::FORM_MIME_TYPES)) {
            $post = $_POST;

            // Files
            if ($_FILES) {
                foreach ($_FILES as $key => $file) {
                    if ($processed = $this->processFiles($resourceManager, $file['name'], $file['type'], $file['tmp_name'], $file['error'], $file['size'])) {
                        $post[$key] = $processed;
                    }
                }
            }

            return [
                HttpCargoInterface::CONTENT => $post,
                HttpCargoInterface::CONTENT_TYPE => self::FORM_PROCESSED_TYPE
            ];
        }

        $streamClass = static::STREAM_CLASS;

        return [
            HttpCargoInterface::CONTENT => new $streamClass('php://input'),
            HttpCargoInterface::CONTENT_TYPE => $contentType
        ];
    }

    protected function getRequestHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (!strncmp($name, 'HTTP_', 5)) {
                $headers[strtr(ucwords(strtolower(substr($name, 5)), '_'), '_', '-')] = $value;
            }
        }

        return $headers;
    }

    protected function getRequestProtocolVersion(): string
    {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::VERSION_1_0)
            ? Http::VERSION_1_0 : Http::VERSION_1_1;
    }

    protected function processFiles(ResourceManagerInterface $resourceManager, $name, $type, $tempFilename, $error, $size)
    {
        // Items
        if (is_array($name)) {
            $items = [];

            foreach ($name as $key => $value) {
                if (null !== $item = $this->processFiles($resourceManager, $value, $type[$key], $tempFilename[$key], $error[$key], $size[$key])) {
                    $items[$key] = $item;
                }
            }

            return $items ?: null;
        }

        if (is_uploaded_file($tempFilename)) {
            return $resourceManager->clone('Base\Upload\UploadItem', UploadItemInterface::class, [
                UploadItemInterface::CLIENT_FILENAME => basename($name),
                UploadItemInterface::CLIENT_MIME_TYPE => $type,
                UploadItemInterface::TEMP_FILENAME => $tempFilename,
                UploadItemInterface::ERROR => $error,
                UploadItemInterface::SIZE => $size
            ]);
        }

        return null;
    }
}
