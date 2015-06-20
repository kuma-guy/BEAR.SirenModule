<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule;


final class SirenPaginationParamHolder implements PaginationParamHolderInterface
{
    /**
     * @param $params
     * @return mixed
     */
    public function getPreviousParameter($params)
    {
        foreach ($params as $key => &$value) {
            $value = $value - 1;
        }
        return $params;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getNextParameter($params)
    {
        foreach ($params as $key => &$value) {
            $value = $value + 1;
        }
        return $params;
    }
}