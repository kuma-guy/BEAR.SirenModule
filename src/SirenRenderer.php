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
use BEAR\SirenModule\Annotation\EmbedLink;
use BEAR\SirenModule\Annotation\EmbedResource;
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

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
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
        $response = json_encode($response);

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

        // Class
        $className = $this->getClass($ref);
        $rootEntity->addClass($className);

        // Add Self Link
        $rootEntity->addLink($self);

        // Actions
        if (isset($body['siren']['actions'])) {
            foreach ($body['siren']['actions'] as $data) {
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

        // Sub Entity
        foreach ($annotations as $annotation) {
            if ($annotation instanceof EmbedResource) {
                $this->embedResource($body, $annotation, $rootEntity);
            }
            if ($annotation instanceof EmbedLink) {
                $entity = new Entity();
                $replacedSrc = $this->replaceQueryParameter($annotation->rel, $annotation->src, $body);
                $href = $this->getHref(new Uri($replacedSrc));

                $entity->addRel($annotation->rel)->setHref($href);
                /* @var $entity Entity */
                $rootEntity->addEntity($entity);
                unset($body[$annotation->rel]);
            }
        }

        // Properties
        unset($body['siren']);
        $rootEntity->setProperties($body);

        // TODO: Related Link

        return $rootEntity;
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
     * Get Class Name
     *
     * @param ReflectionClass $ref
     *
     * @return string
     */
    private function getClass(ReflectionClass $ref)
    {
        return lcfirst($ref->getParentClass()->getShortName());
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
    private function embedResource(array &$body, $annotation, $rootEntity)
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
}
