<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Context;

use BEAR\Resource\RenderInterface;
use BEAR\SirenRenderer\Annotation\Action;
use BEAR\SirenRenderer\Provide\ActionInterceptor;
use BEAR\SirenRenderer\Provide\Representation\SirenRenderer;
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
    }
}