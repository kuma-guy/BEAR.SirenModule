<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Link
{
    /**
     * rel
     *
     * @var string
     */
    public $rel = '';

    /**
     * uri to get href
     *
     * @var string
     */
    public $uri = '';
}
