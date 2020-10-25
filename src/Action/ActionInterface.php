<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 25.10.2020
 * Time: 11:41
 */

namespace StrasnyLada\DirSync\Action;

interface ActionInterface
{
    /**
     * Process the action
     *
     * @return ActionInterface
     */
    public function run();

    /**
     * Action path in the structure
     *
     * @param string $path
     * @return ActionInterface
     */
    public function setPath(string $path);

    /**
     * Action parameters
     *
     * @param array $parameters
     * @return ActionInterface
     */
    public function setParameters(array $parameters);
}