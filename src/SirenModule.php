<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;

use BEAR\Resource\RenderInterface;
use BEAR\SirenModule\Annotation\SirenAction;
use BEAR\SirenModule\Annotation\SirenEmbedResource;
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
        $this->bind(PaginationParamHolderInterface::class)->to(SirenPaginationParamHolder::class)->in(Scope::SINGLETON);
        $this->bind(Reader::class)->to(AnnotationReader::class);
        $this->bind(RenderInterface::class)->to(SirenRenderer::class)->in(Scope::SINGLETON);

        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(SirenAction::class),
            [ActionInterceptor::class]
        );
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(SirenEmbedResource::class),
            [EmbedResourceInterceptor::class]
        );
    }
}
