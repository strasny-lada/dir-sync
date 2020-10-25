<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 25.10.2020
 * Time: 20:17
 */

namespace StrasnyLada\DirSync\Exception;

use Throwable;

final class InvalidActionParametersException extends \Exception implements ExceptionInterface
{
    /** @var string */
    protected $message = 'Invalid action parameters "%s"';

    /**
     * InvalidActionParametersException constructor.
     * @param array $parameters
     * @param string $submessage
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($parameters = [], $submessage = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf($this->message, json_encode($parameters ?: []));
        if ($submessage) {
            $message .= sprintf(' (%s)', $submessage);
        }

        parent::__construct($message, $code, $previous);
    }

}