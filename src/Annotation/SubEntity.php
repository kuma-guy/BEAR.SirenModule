<?php

/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class SubEntity
{
    /**
     * uri
     *
     * @var string
     */
    public $class = '';

    /**
     * uri
     *
     * @var string
     */
    public $rel = '';

    /**
     * uri
     *
     * @var string
     */
    public $uri = '';
}
