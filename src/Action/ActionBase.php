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
}