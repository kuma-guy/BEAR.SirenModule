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
final class Field
{
    /**
     * name
     *
     * @var string
     */
    public $name = '';

    /**
     * type
     *
     * @var string
     */
    public $type = '';

    /**
     * value
     *
     * @var string
     */
    public $value = '';
}
