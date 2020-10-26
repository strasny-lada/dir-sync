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
     * Run the action
     *
     * @return ActionInterface
     */
    public function run();

    /**
     * Validate action (parameters, ...)
     *
     * @return void
     */
    public function validate();

    /**
     * Process the action
     *
     * @return void
     */
    public function process();

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

    /**
     * Store simple action process message
     *
     * @param string $message
     * @return ActionInterface
     */
    public function addMessage(string $message);

    /**
     * Returns verbose messages with action process describe
     *
     * @return string[]
     */
    public function getMessages();
}