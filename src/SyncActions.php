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
     * @param array $actions [optional] Action classes allowed within sync process
     * @return DirSyncInterface
     */
    public function sync($options = [], $actions = [])
    {
        $options = $options ?: [
            DirSync::SYNC_ACTIONS_ONLY,
        ];

        if (in_array(DirSync::SYNC_ACTIONS_ONLY, $options)) {
            try {
                $jsonActions = $this->getJsonActions();
            } catch (ExceptionInterface $e) {
                error_log($e->getMessage());
                $jsonActions = [];
            }

            if ($actions) {
                $jsonActions = array_filter($jsonActions, function (ActionInterface  $action) use ($actions){
                    return in_array(get_class($action), $actions);
                });
            }

            foreach($jsonActions as $action) {
                $action->run();
            }
        }

        return $this;
    }

    /**
     * @return ActionInterface[]
     * @throws ActionDoesNotExistException
     * @throws InvalidJsonInputException
     */
    private function getJsonActions()
    {
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