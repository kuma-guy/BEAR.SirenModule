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
final class SirenLink
{
    /**
     * Relation
     *
     * @var string
     */
    public $rel;

    /**
     * Class
     *
     * @var string
     */
    public $class;

    /**
     * Href
     *
     * @var string
     */
    public $href;

    /**
     * Title
     *
     * @var string
     */
    public $title;

    /**
     * Type
     *
     * @var string
     */
    public $type;
}
