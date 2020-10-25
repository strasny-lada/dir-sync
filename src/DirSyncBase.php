<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 23.10.2020
 * Time: 15:10
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Exception\DirectoryDoesNotExistException;
use StrasnyLada\DirSync\Exception\FileDoesNotExistException;
use StrasnyLada\DirSync\Exception\InvalidJsonInputException;

abstract class DirSyncBase implements DirSyncInterface
{
    const SYNC_CREATE_ONLY = 'create';
    const SYNC_REMOVE_ONLY = 'remove';
    const SYNC_ACTIONS_ONLY = 'actions';

    const PREFIX_ACTION = '#';

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $jsonInput;

    /**
     * DirSyncBase constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $path
     * @return DirSyncBase
     * @throws DirectoryDoesNotExistException
     */
    public function setRootDir($path)
    {
        if (!file_exists($path)) {
            throw new DirectoryDoesNotExistException($path);
        }

        $this->rootDir = rtrim($path, DIRECTORY_SEPARATOR);

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
     * @return DirSyncBase
     * @throws FileDoesNotExistException
     * @throws InvalidJsonInputException
     */
    public function fromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileDoesNotExistException($filePath);
        }

        $this->setJsonInput(file_get_contents($filePath));

        return $this;
    }

    /**
     * @param string $JSON
     * @return DirSyncBase
     * @throws InvalidJsonInputException
     */
    public function setJsonInput($JSON)
    {
        if (
            !($structure = json_decode($JSON))
            ||
            !is_object($structure)
        ) {
            throw new InvalidJsonInputException($JSON);
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
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    protected static function hasArrayKey(array $data, string $key)
    {
        $values = self::getArrayValues($data, $key);
        return !empty($values);
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    protected static function getArrayValue(array $data, string $key)
    {
        $values = self::getArrayValues($data, $key);
        return $values ? $values[key($values)] : null;
    }

    /**
     * @param array $data
     * @param string $key
     * @return array
     */
    private static function getArrayValues(array $data, string $key)
    {
        $keyChunks = explode(DIRECTORY_SEPARATOR, $key);
        return array_filter($data, function ($itemValue, $itemKey) use ($keyChunks){
            $itemKeyChunks = explode(DIRECTORY_SEPARATOR, $itemKey);
            return $itemKeyChunks == $keyChunks;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param string $dirPath Absolute directory path
     * @param int $permissions Numeric directory permissions
     * @return bool
     */
    protected function mkdir(string $dirPath, $permissions = 0755)
    {
        $previousUmask = umask(0);
        $ret = mkdir($dirPath, $permissions, true);
        umask($previousUmask);

        return $ret;
    }

    /**
     * Clear directory
     *
     * @param string $dirPath Absolute directory path
     * @param bool $self Remove pasted directory itself
     * @return bool
     */
    public function mrProper(string $dirPath, $self = false)
    {
        // scan content of the directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dirPath,
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        // clear directory content
        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->mrProper($item->getPathname(), true);
            } else {
                unlink($item->getPathname());
            }
        }

        return $self ? rmdir($dirPath) : true;
    }
}