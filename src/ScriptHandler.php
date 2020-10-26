<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 26.10.2020
 * Time: 09:18
 */

namespace StrasnyLada\DirSync;

use Composer\Script\Event;
use StrasnyLada\DirSync\Exception\ExceptionInterface;

class ScriptHandler
{
    const HANDLER_VERBOSE = 'verbose';
    const HANDLER_ROOT_DIR = 'root-dir';
    const HANDLER_CONFIG_FILE = 'config-file';
    const HANDLER_CONFIG_JSON = 'config-json';
    const HANDLER_OPTIONS = 'options';

    /**
     * @param Event $event
     */
    public static function sync(Event $event)
    {
        $parameters = self::prepareParameters($event);
        $dirSync = new DirSync();

        try {
            // set root directory
            if ($parameters[self::HANDLER_ROOT_DIR] ?? null) {
                $dirSync->setRootDir($parameters[self::HANDLER_ROOT_DIR]);
            }

            // set JSON input
            if ($parameters[self::HANDLER_CONFIG_FILE] ?? null) {
                $dirSync->fromFile($parameters[self::HANDLER_CONFIG_FILE]);
            } elseif ($parameters[self::HANDLER_CONFIG_JSON] ?? null) {
                $dirSync->setJsonInput($parameters[self::HANDLER_CONFIG_JSON]);
            }
        } catch (ExceptionInterface $e) {
            $event->getIO()->write($e->getMessage());
        }

        // run synchronization
        $dirSync->sync($parameters[self::HANDLER_OPTIONS] ?? null);

        // render log messages
        if ($parameters[self::HANDLER_VERBOSE]) {
            $event->getIO()->write('DirSync messages:');
            if ($dirSync->getMessages()) {
                foreach ($dirSync->getMessages() as $message) {
                    $event->getIO()->write($message);
                }
            } else {
                $event->getIO()->write('Nothing to do');
            }
        }
    }

    /**
     * @param Event $event
     * @return array
     */
    private static function prepareParameters(Event $event)
    {
        $parameters = [];

        if ($event->getArguments()) {
            foreach($event->getArguments() as $argument) {
                // argument is in the "key=value" shape
                list($name, $value) = explode('=', $argument);

                switch($name) {
                    // process options separated by comma
                    case self::HANDLER_OPTIONS:
                        $value = array_map(function ($item){
                            return trim($item);
                        }, explode(',', $value));
                        break;
                    // verbose is boolean parameter
                    case self::HANDLER_VERBOSE:
                        $value = (bool)$value;
                        break;
                }

                $parameters[$name] = $value;
            }
        }

        return $parameters;
    }
}