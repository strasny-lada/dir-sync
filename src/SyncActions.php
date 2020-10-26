<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 20:55
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Action\ActionInterface;
use StrasnyLada\DirSync\Exception\ActionDoesNotExistException;
use StrasnyLada\DirSync\Exception\ExceptionInterface;
use StrasnyLada\DirSync\Exception\InvalidJsonInputException;
use StrasnyLada\DirSync\Transformer\ObjectToArrayTransformer;
use StrasnyLada\DirSync\Transformer\TreeToFlatDirectoryStructureTransformer;

final class SyncActions extends DirSyncBase
{
    /**
     * @param array $options [optional] Additional options for the directory sync process
     * @param array $allowedActionClasses [optional] Action classes allowed within sync process
     * @return DirSyncInterface
     */
    public function sync($options = [], $allowedActionClasses = [])
    {
        $options = $options ?: [
            DirSync::SYNC_ACTIONS_ONLY,
        ];

        if (in_array(DirSync::SYNC_ACTIONS_ONLY, $options)) {
            $this->runActions($allowedActionClasses);
        }

        return $this;
    }

    /**
     * @param array $allowedActionClasses
     */
    private function runActions(array $allowedActionClasses)
    {
        try {
            $actions = $this->fetchActions();
        } catch (ExceptionInterface $e) {
            error_log($e->getMessage());
            $actions = [];
        }

        // filter actions by allowed classes
        if ($allowedActionClasses) {
            $actions = array_filter($actions, function (ActionInterface  $action) use ($allowedActionClasses){
                return in_array(get_class($action), $allowedActionClasses);
            });
        }

        // run actions
        foreach($actions as $action) {
            try {
                $this->addMessage(sprintf('%s action stared', $action->getBaseClassName()));
                $action->run();
                $this->addMessages($action->getMessages());
                $this->addMessage(sprintf('%s action finished', $action->getBaseClassName()));
            } catch (ExceptionInterface $e) {
                error_log($e->getMessage());
            }
        }
    }

    /**
     * @return ActionInterface[]
     * @throws ActionDoesNotExistException
     * @throws InvalidJsonInputException
     */
    private function fetchActions()
    {
        // fetch structure
        $structure = json_decode($this->jsonInput);
        if (!$structure) {
            throw new InvalidJsonInputException($this->jsonInput);
        }

        // convert object structure to array
        $treeStructure = (new ObjectToArrayTransformer())->transform($structure);
        // get flat structure paths
        $flatStructure = (new TreeToFlatDirectoryStructureTransformer())->transform($treeStructure);

        // filter out non-action paths
        $actionPaths = array_filter($flatStructure, function ($value, $path) {
            return strpos($path, self::PREFIX_ACTION) !== false;
        }, ARRAY_FILTER_USE_BOTH);

        // initiate action providers
        return array_map(function ($parameters, $path) {
            // divide directory path and action configuration
            list($directoryPath, $actionName) = explode(self::PREFIX_ACTION, $path);
            $directoryPath = rtrim($directoryPath, DIRECTORY_SEPARATOR);

            // find action class
            $actionClass = sprintf(
                '%s\%s',
                (new \ReflectionClass(ActionInterface::class))->getNamespaceName(),
                ucfirst($actionName)
            );
            if (!class_exists($actionClass)) {
                throw new ActionDoesNotExistException($actionClass);
            }

            // create action
            $action = new $actionClass();
            $action->setPath($this->getRootDir() . DIRECTORY_SEPARATOR . $directoryPath);
            if ($parameters) {
                $action->setParameters(is_array($parameters) ? $parameters : [$parameters]);
            }

            return $action;
        }, $actionPaths, array_keys($actionPaths));
    }
}