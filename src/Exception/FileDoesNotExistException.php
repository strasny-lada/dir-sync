<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 12:48
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class FileDoesNotExistException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'File %s does not exist';

    /**
     * FileDoesNotExistException constructor.
     * @param string $filePath
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($filePath = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $filePath), $code, $previous);
    }
}