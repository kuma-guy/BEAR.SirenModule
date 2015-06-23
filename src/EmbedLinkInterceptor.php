<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;

use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Exception\BadRequestException;
use BEAR\Resource\FactoryInterface;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\SirenEmbedLink;
use BEAR\SirenModule\Exception\SirenEmbedLinkException;
use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

final class EmbedLinkInterceptor implements MethodInterceptor
{
    /**
     * @var \BEAR\Resource\ResourceInterface
     */
    private $resource;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ResourceInterface $resource
     * @param Reader            $reader
     */
    public function __construct(ResourceInterface $resource, Reader $reader, FactoryInterface $factory)
    {
        $this->resource = $resource;
        $this->reader = $reader;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        /** @var $resourceObject ResourceObject */
        $resourceObject = $invocation->getThis();
        $method = $invocation->getMethod();
        $query = $this->getArgsByInvocation($invocation);
        $embeds = $this->reader->getMethodAnnotations($method);
        // embedding resource
        $this->embedLink($embeds, $resourceObject, $query);
        // request (method can modify embedded resource)
        $result = $invocation->proceed();

        return $result;
    }

    /**
     * @param Embed[]        $embeds
     * @param ResourceObject $resourceObject
     * @param array          $query
     */
    private function embedLink(array $embeds, ResourceObject $resourceObject, array $query)
    {
        foreach ($embeds as $embed) {
            if (! $embed instanceof SirenEmbedLink) {
                continue;
            }
            try {
                $templateUri = $this->getFullUri($embed->src, $resourceObject);
                $uri = uri_template($templateUri, $query);

                $actionResource = $this->factory->newInstance($uri);
                $ref = new \ReflectionMethod($actionResource, 'onGet');
                $annotations = $this->reader->getMethodAnnotations($ref);

                foreach ($annotations as $annotation) {
                    if (! $annotation instanceof SirenClass) {
                        continue;
                    }
                    $classes = explode(',', $annotation->value);
                    $resourceObject->body[$embed->rel]['siren']['class'] = $classes;
                }

            } catch (BadRequestException $e) {
                throw new SirenEmbedLinkException($embed->src, 500, $e);
            }
        }
    }

    /**
     * @param string         $uri
     * @param ResourceObject $resourceObject
     *
     * @return string
     */
    private function getFullUri($uri, ResourceObject $resourceObject)
    {
        if (substr($uri, 0, 1) == '/') {
            $uri = "{$resourceObject->uri->scheme}://{$resourceObject->uri->host}" . $uri;
        }

        return $uri;
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @return array
     */
    private function getArgsByInvocation(MethodInvocation $invocation)
    {
        $args = $invocation->getArguments()->getArrayCopy();
        $params = $invocation->getMethod()->getParameters();
        $namedParameters = [];
        foreach ($params as $param) {
            $namedParameters[$param->name] = array_shift($args);
        }

        return $namedParameters;
    }
}
