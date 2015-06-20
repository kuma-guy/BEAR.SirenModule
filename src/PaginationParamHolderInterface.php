<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;


interface PaginationParamHolderInterface
{
    /**
     * @return array
     */
    public function getPreviousParameter($params);

    /**
     * @return array
     */
    public function getNextParameter($params);
}