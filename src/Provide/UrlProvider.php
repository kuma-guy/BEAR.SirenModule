<?php
/**
 * This file is part of the BEAR.SirenRenderer package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenRenderer\Provide;

class UrlProvider implements UrlProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $scheme = "http://";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $scheme = "https://";
        }
        return isset($_SERVER['HTTP_HOST']) ?  $scheme . $_SERVER['HTTP_HOST'] : $scheme . "localhost";
    }
}