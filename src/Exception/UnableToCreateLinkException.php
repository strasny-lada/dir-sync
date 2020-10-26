<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 26.10.2020
 * Time: 08:30
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

class UnableToCreateLinkException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Unable to create link "%s"';

    /**
     * UnableToCreateLinkException constructor.
     * @param string $path
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($path = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $path), $code, $previous);
    }
}