<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 13:22
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class InvalidJsonInputException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Invalid JSON "%s"';

    /**
     * InvalidJsonInputException constructor.
     * @param string $jsonInput
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($jsonInput = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, (string)$jsonInput), $code, $previous);
    }
}