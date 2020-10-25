<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 12:48
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class DirectoryDoesNotExistException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Directory %s does not exist';

    /**
     * DirectoryDoesNotExistException constructor.
     * @param string $dirPath
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($dirPath = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $dirPath), $code, $previous);
    }
}