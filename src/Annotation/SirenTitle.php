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
final class SirenTitle
{
    /**
     * title
     *
     * @var string
     */
    public $value = '';
}
