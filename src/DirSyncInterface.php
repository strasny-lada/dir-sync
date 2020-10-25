<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 23.10.2020
 * Time: 14:51
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Exception\ExceptionInterface;

interface DirSyncInterface
{
    public function __construct();

    /**
     * Will set the root directory in which the directory
     * sync will be applied.
     * If the root directory is not set the Instance should look for
     * constant "__root__"; if the constant is not provided
     * then the root is the system root.
     * @param string $path A valid path to a existing directory
     * @return self
     */
    public function setRootDir($path);

    /**
     * Will read the JSON string directly from a file path;
     *
     * @param string $filePath A valid json file path
     * @return self
     * @throws ExceptionInterface
     */
    public function fromFile($filePath);

    /**
     * Will provide the library with the JSON input
     *
     * @param string $JSON A raw string JSON
     * @return self
     * @throws ExceptionInterface
     */
    public function setJsonInput($JSON);

    /**
     * Simply return the previously given JSON data.
     * @return string Return a string JSON data.
     * @throws ExceptionInterface
     */
    public function getJsonInput();


    /**
     * Will begin the process of the synchronization.
     * The process can have the following options:
     *
     *  \StrasnyLada\DirSync\DirSync::SYNC_CREATE_ONLY - creating directories only;<br>
     *  \StrasnyLada\DirSync\DirSync::SYNC_REMOVE_ONLY - only removing directories;<br>
     *  \StrasnyLada\DirSync\DirSync::SYNC_ACTIONS_ONLY - just run the action but do
     *  not change the directory tree in any way;<br>
     *
     * @param array [optional] Additional options for the directory sync process
     * @return self|array
     * @throws ExceptionInterface
     */
    public function sync($options=[]);
}