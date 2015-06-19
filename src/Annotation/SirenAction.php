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
final class SirenAction
{
    /**
     * src
     *
     * @var string
     */
    public $src = '';

    /**
     * method
     *
     * @var string
     */
    public $method = '';
}
