<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 19:58
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class ActionDoesNotExistException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Action %s does not exist';

    /**
     * ActionDoesNotExistException constructor.
     * @param string $action
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($action = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $action), $code, $previous);
    }
}