<?php

/**
 * This file is part of the BEAR.SirenModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\SirenModule\Exception;

use BEAR\Resource\Exception\BadRequestException;
use BEAR\Resource\Exception\ExceptionInterface;

class SirenEmbedLinkException extends BadRequestException implements ExceptionInterface
{
}