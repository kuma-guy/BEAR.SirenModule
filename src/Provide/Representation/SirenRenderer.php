<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Provide\Representation;

use BEAR\Resource\RenderInterface;
use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;
use BEAR\SirenRenderer\Provide\UrlProvider;
use Doctrine\Common\Annotations\Reader;
use Siren\Components\Entity;
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

        $body = $ro->body;
        $annotations = [];

        /* @var $annotations Link[] */
        $siren = $this->getSiren($ro->uri, $body, $annotations);

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

    private function getSiren(Uri $uri, array $body, array $annotation)
    {
        $domain = $this->url->get();

        $query = $uri->query ? '?' . http_build_query($uri->query) : '';
        $path = $uri->path . $query;
        $selfLink = $this->getReverseMatchedLink($path);
        $selfLink = $domain . $selfLink;


        $rootEntity = new Entity();
        $self = new Link;
        $self->addRel('self')
            ->setHref($selfLink);

        $rootEntity->addLink($self);

        return $rootEntity;
    }
}