<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenClass;
use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

final class SirenClassInterceptor implements MethodInterceptor
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
     * @param Reader $reader
     */
    public function __construct(ResourceInterface $resource, Reader $reader)
    {
        $this->resource = $resource;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        /** @var $resourceObject ResourceObject */
        $resourceObject = $invocation->getThis();
        $method = $invocation->getMethod();
        $annotations = $this->reader->getMethodAnnotations($method);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof SirenClass) {
                $resourceObject->body['siren']['class'] = $annotation->name;
            }
        }

        $result = $invocation->proceed();

        return $result;
    }
}
