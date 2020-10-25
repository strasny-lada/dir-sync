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