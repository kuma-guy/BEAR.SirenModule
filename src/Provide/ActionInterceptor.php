<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Provide;

use BEAR\Resource\Exception\BadRequestException;
use BEAR\Resource\FactoryInterface;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenRenderer\Annotation\Action;
use BEAR\SirenRenderer\Annotation\Field;
use BEAR\SirenRenderer\Annotation\Name;
use BEAR\SirenRenderer\Annotation\Title;
use Doctrine\Common\Annotations\Reader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

final class ActionInterceptor implements MethodInterceptor
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
        $actions = $this->reader->getMethodAnnotations($method);

        $this->addActions($actions, $resourceObject, $query);
        // request (method can modify embedded resource)
        $result = $invocation->proceed();
        return $result;
    }

    /**
     * @param Action[]        $actions
     * @param ResourceObject $resourceObject
     * @param array          $query
     */
    private function addActions(array $actions, ResourceObject $resourceObject, array $query)
    {
        foreach ($actions as $action) {
            /* @var $action Action */
            if (! $action instanceof Action) {
                continue;
            }
            try {
                // Get Uri for related resource
                $templateUri = $this->getFullUri($action->src, $resourceObject);
                $uri = uri_template($templateUri, $query);

                // Get Method from action
                $requestMethod = $action->method;
                $requestMethod = 'on' . ucfirst($requestMethod);

                // Get resource object for action
                $actionResource = $this->factory->newInstance($uri);
                $ref = new \ReflectionMethod($actionResource, $requestMethod);
                $annotations = $this->reader->getMethodAnnotations($ref);

                $data = [];

                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Name) {
                        $data['name'] = $annotation->value;
                    }
                    if ($annotation instanceof Title) {
                        $data['title'] = $annotation->value;
                    }
                    if ($annotation instanceof Field) {
                        $field = [];
                        $field['name'] = $annotation->name;
                        $field['type'] = $annotation->type;
                        $field['value'] = $annotation->value;
                        $data['fields'][] = $field;
                    }
                }

                $resourceObject->body['siren']['actions'][] = $data;

            } catch (BadRequestException $e) {
                // wrap ResourceNotFound or Uri exception
                //throw new ActionException($action->src, 500, $e);
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