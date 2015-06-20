<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;

use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\SirenEmbedLink;
use BEAR\SirenModule\Annotation\SirenEmbedResource;
use BEAR\SirenModule\Annotation\SirenLink;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Siren\Components\Action;
use Siren\Components\Entity;
use Siren\Components\Field;
use Siren\Components\Link;
use Siren\Encoders\Encoder;

final class SirenRenderer implements RenderInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader, PaginationParamHolderInterface $paginater)
    {
        $this->reader = $reader;
        $this->paginater = $paginater;
    }

    /**
     * {@inheritdoc}
     */
    public function render(ResourceObject $ro)
    {
        if (!isset($ro->headers['content-type'])) {
            $ro->headers['content-type'] = 'application/vnd.siren+json';
        }

        $method = 'on' . ucfirst($ro->uri->method);
        $annotations = $this->reader->getMethodAnnotations(new \ReflectionMethod($ro, $method));

        $siren = $this->getSiren($ro, $annotations);

        $response = (new Encoder)->encode($siren);
        $response = json_encode($response, JSON_PRETTY_PRINT);

        $ro->view = $response;

        return $ro->view;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected function getReverseMatchedLink($uri)
    {
        return $uri;
    }

    /**
     * @param ResourceObject $ro
     * @param array          $annotations
     *
     * @return Entity
     */
    private function getSiren(ResourceObject $ro, array $annotations)
    {
        // Get Reflection Class For Resource
        $ref = new ReflectionClass($ro);

        // Self Link
        $self = new Link;
        $self->addRel('self')->setHref($this->getHref($ro->uri));

        // Resource Body
        $body = $ro->jsonSerialize();

        // Siren Root Entity
        $rootEntity = new Entity();

        // Add Self Link
        $rootEntity->addLink($self);

        // Actions
        if (isset($body['siren']['actions'])) {
            foreach ($body['siren']['actions'] as $data) {
                $this->addAction($data, $rootEntity);
            }
        }

        // Build Entity
        foreach ($annotations as $annotation) {
            if ($annotation instanceof SirenClass) {
                $this->addClass($annotation, $rootEntity);
            }
            if ($annotation instanceof SirenEmbedResource) {
                $this->embedResource($body, $annotation, $rootEntity);
            }
            if ($annotation instanceof SirenEmbedLink) {
                $this->embedLink($body, $annotation, $rootEntity);
            }
            if ($annotation instanceof SirenLink) {
                $this->addSirenLink($ro, $body, $annotation, $rootEntity);
            }
        }

        // Properties
        unset($body['siren']);
        $rootEntity->setProperties($body);

        return $rootEntity;
    }

    /**
     * @param $annotation
     * @param $rootEntity
     */
    private function addClass($annotation, Entity $rootEntity)
    {
        // Class
        $class = $annotation->name;
        $rootEntity->addClass($class);
    }

    /**
     * Replace parameter holder with actual value.
     *
     * @param $query
     * @param $properties
     *
     * @return mixed
     */
    private function replaceQueryParameter($rel, $query, $body)
    {
        if (isset($body[$rel])) {
            foreach ($body[$rel] as $key => $value) {
                if (strstr($query, $key)) {
                    return str_replace('{?' . $key . '}', '?' . $key . '=' . $value, $query);
                }
            }
        }

        foreach ($body as $key => $value) {
            if (strstr($query, $key)) {
                return str_replace('{?' . $key . '}', '?' . $key . '=' . $value, $query);
            }
        }

        return $query;
    }

    /**
     * Get Href
     *
     * @param Uri $uri
     *
     * @return string
     */
    private function getHref(Uri $uri)
    {
        $query = $uri->query ? '?' . http_build_query($uri->query) : '';
        $path = $uri->path . $query;
        $link = $this->getReverseMatchedLink($path);

        return $link;
    }

    /**
     * @param array  $body
     * @param object $annotation
     * @param Entity $rootEntity
     */
    private function embedResource(array &$body, $annotation, Entity $rootEntity)
    {
        if (isset($body[$annotation->rel])) {
            $entity = new Entity();
            $replacedSrc = $this->replaceQueryParameter($annotation->rel, $annotation->src, $body);
            $href = $this->getHref(new Uri($replacedSrc));

            $entity->setProperties($body[$annotation->rel])
                ->addRel($annotation->rel)
                ->setHref($href);
        }
        /* @var $entity Entity */
        $rootEntity->addEntity($entity);
        unset($body[$annotation->rel]);
    }

    /**
     * @param array  $body
     * @param object $annotation
     * @param Entity $rootEntity
     */
    private function embedLink(array &$body, $annotation, Entity $rootEntity)
    {
        $entity = new Entity();
        $replacedSrc = $this->replaceQueryParameter($annotation->rel, $annotation->src, $body);
        $href = $this->getHref(new Uri($replacedSrc));

        $entity->addRel($annotation->rel)->setHref($href);
        /* @var $entity Entity */
        $rootEntity->addEntity($entity);
        unset($body[$annotation->rel]);
    }

    /**
     * @param ResourceObject $ro
     * @param array $body
     * @param $annotation
     * @param Entity $rootEntity
     */
    private function addSirenLink(ResourceObject $ro, array &$body, $annotation, Entity $rootEntity)
    {
        // Parameters for pagination.
        $parameters = [
            $annotation->param => $body[$annotation->param]
        ];

        $pagingQuery = [];

        if ($annotation->rel == 'previous') {
            $pagingQuery = $this->paginater->getPreviousParameter($parameters);
        }
        if ($annotation->rel == 'next') {
            $pagingQuery = $this->paginater->getNextParameter($parameters);
        }

        $ro->uri->query = array_merge($ro->uri->query, $pagingQuery);

        $link = new Link;
        $link->addRel($annotation->rel)->setHref($this->getHref($ro->uri));

        $rootEntity->addLink($link);
    }

    /**
     * @param array  $data
     * @param Entity $rootEntity
     */
    private function addAction(array $data, Entity $rootEntity)
    {
        $action = new Action();
        $action->setName($data['name']);
        $action->setTitle($data['title']);
        $action->setHref($this->getHref(new Uri($data['href'])));
        $action->setMethod($data['method']);

        foreach ($data['fields'] as $row) {
            $field = new Field();
            $field->setName($row['name']);
            $field->setType($row['type']);
            $field->setValue($row['value']);
            $action->addField($field);
        }
        $rootEntity->addAction($action);
    }
}
