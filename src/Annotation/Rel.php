<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace BEAR\SirenRenderer\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Rel
{
    /**
     * rel
     *
     * @var string
     */
    public $value = '';
}