<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 25.10.2020
 * Time: 11:57
 */

namespace StrasnyLada\DirSync\Action;

abstract class ActionBase implements ActionInterface
{
    /** @var string */
    protected $path;

    /** @var array */
    protected $parameters = [];

    /**
     * @return ActionInterface
     */
    public function run()
    {
        $this->validate();
        $this->process();

        return $this;
    }

    /**
     * @param string $path
     * @return ActionInterface
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param array $parameters
     * @return ActionInterface
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Collect context path with directory path
     *
     * @param string $relativePath
     * @return string
     */
    protected function getAbsolutePath(string $relativePath)
    {
        if (!$relativePath
            || $relativePath[0] === DIRECTORY_SEPARATOR
        ) {
            return $relativePath;
        }

        // filter out slash on the path beginning
        $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
        if (strpos($relativePath, '.' . DIRECTORY_SEPARATOR) === 0) {
            $relativePath = substr($relativePath, 2);
        }

        // collect absolute path
        $path = $this->path;
        if ($relativePath) {
            $path .= DIRECTORY_SEPARATOR;
            $path .= $relativePath;
        }

        return $path;
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
}