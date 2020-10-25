<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 25.10.2020
 * Time: 15:29
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

class UnableToRemoveDirectoryException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Unable to remove directory "%s"';

    /**
     * UnableToRemoveDirectoryException constructor.
     * @param string $path
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($path = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $path), $code, $previous);
    }
}