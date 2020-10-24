<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 23.10.2020
 * Time: 15:10
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Exception\DirectoryDoesNotExistExceptionInterface;
use StrasnyLada\DirSync\Exception\FileDoesNotExistExceptionInterface;
use StrasnyLada\DirSync\Exception\InvalidJsonInputExceptionInterface;

class DirSync implements DirSyncInterface
{
    const SYNC_CREATE_ONLY = 'create';
    const SYNC_REMOVE_ONLY = 'remove';
    const SYNC_ACTIONS_ONLY = 'actions';

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $jsonInput;

    /**
     * DirSync constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $path
     * @return DirSync
     * @throws DirectoryDoesNotExistExceptionInterface
     */
    public function setRootDir($path)
    {
        if (!file_exists($path)) {
            throw new DirectoryDoesNotExistExceptionInterface($path);
        }

        $this->rootDir = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        if ($this->rootDir) {
            return $this->rootDir;
        } elseif (($rootDir = constant('__root__'))) {
            return (string)$rootDir;
        } else {
            return __DIR__;
        }
    }

    /**
     * @param string $filePath
     * @return DirSync
     * @throws FileDoesNotExistExceptionInterface
     * @throws InvalidJsonInputExceptionInterface
     */
    public function fromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileDoesNotExistExceptionInterface($filePath);
        }

        $this->setJsonInput(file_get_contents($filePath));

        return $this;
    }

    /**
     * @param string $JSON
     * @return DirSync
     * @throws InvalidJsonInputExceptionInterface
     */
    public function setJsonInput($JSON)
    {
        if (
            ($structure = json_decode($JSON))
            ||
            $structure === 'NULL'
            ||
            !is_object($structure)
        ) {
            throw new InvalidJsonInputExceptionInterface($JSON);
        }

        $this->jsonInput = $JSON;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsonInput()
    {
        return $this->jsonInput;
    }

    /**
     * @param array|null $options
     * @return DirSync
     */
    public function sync($options = null)
    {
        $syncStructure = json_decode($this->jsonInput);

        // TODO

        return $this;
    }
}