<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 25.10.2020
 * Time: 13:28
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class UnableToCreateDirectoryException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Unable to create directory "%s"';

    /**
     * UnableToCreateDirectoryException constructor.
     * @param string $path
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($path = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $path), $code, $previous);
    }
}