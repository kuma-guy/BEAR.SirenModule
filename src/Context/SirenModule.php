<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Context;

use BEAR\Resource\RenderInterface;
use BEAR\SirenRenderer\Annotation\Action;
use BEAR\SirenRenderer\Annotation\EmbedResource;
use BEAR\SirenRenderer\Provide\ActionInterceptor;
use BEAR\SirenRenderer\Provide\EmbedResourceInterceptor;
use BEAR\SirenRenderer\Provide\Representation\SirenRenderer;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class SirenModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(RenderInterface::class)->to(SirenRenderer::class)->in(Scope::SINGLETON);
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Action::class),
            [ActionInterceptor::class]
        );

        $this->bind(Reader::class)->to(AnnotationReader::class);
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(EmbedResource::class),
            [EmbedResourceInterceptor::class]
        );
    }
}