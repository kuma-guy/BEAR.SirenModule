<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Provide\Representation;

use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;
use BEAR\SirenRenderer\Annotation\Action as SirenAction;
use BEAR\SirenRenderer\Annotation\Name;
use BEAR\SirenRenderer\Annotation\Title;
use BEAR\SirenRenderer\Provide\UrlProvider;
use Doctrine\Common\Annotations\Reader;
use JsonSchema\RefResolver;
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

    /**
     * @var UrlProviderInterface
     */
    private $url;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->url = new UrlProvider;
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
            if ($annotation instanceof Embed) {
                if (isset($body[$annotation->rel])) {
                    $entity = new Entity();
                    $entity->setProperties($body[$annotation->rel])
                        ->addRel($annotation->rel)
                        ->setHref($this->replaceQueryParameter($annotation->src, $body[$annotation->rel]));
                }
                /** @var $entity Entity */
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

    private function replaceQueryParameter($query, $properties)
    {
        foreach ($properties as $key => $value) {
            if (strstr($query, $key)) {
                return str_replace('{?' . $key . '}', '?' . $key . '=' . $value, $query);
            }
        }

        return $query;
    }

    private function getClass(ReflectionClass $ref)
    {
        return lcfirst($ref->getParentClass()->getShortName());
    }

    private function getHref(Uri $uri)
    {
        $siteUrl = $this->url->get();
        $query = $uri->query ? '?' . http_build_query($uri->query) : '';
        $path = $uri->path . $query;
        $link = $this->getReverseMatchedLink($path);
        $link = $siteUrl . $link;

        return $link;
    }

    private function getActions($currentMethod, ReflectionClass $ref)
    {
        $actions = [];

        $currentMethodName = 'on' . ucfirst($currentMethod);
        $methods = $ref->getMethods();

        foreach ($methods as $method) {
            // Don't build action if method does not start with "on".
            if (strpos($method->name, 'on') !== 0) {
                continue;
            }
            // Don't build action if current method.
            if ($currentMethodName == $method->name) {
                continue;
            }

            // Build action
            $action = new Action();
            $annotations = $this->reader->getMethodAnnotations($method);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Name) {
                    $action->setName($annotation->value);
                } else {
                    // Name is required.
                }
                if ($annotation instanceof Title) {
                    $action->setTitle($annotation->value);
                }
            }

            // TODO:
            $action->setHref("http://api.x.io/orders/42/items");

            switch ($method->name) {
                case 'onGet':
                    $action->setMethod('GET');
                    break;
                case 'onPost':
                    $action->setMethod('POST');
                    break;
                case 'onPut':
                    $action->setMethod('PUT');
                    break;
                case 'onDelete':
                    $action->setMethod('DELETE');
                    break;
                default:
                    continue;
            }

            $actions[] = $action;
        }

        return $actions;
    }
}