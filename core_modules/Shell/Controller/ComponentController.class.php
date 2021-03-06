<?php

namespace Cx\Core_Modules\Shell\Controller;

class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    protected $commandRunning = false;
    protected $userWantsExit = false;
    protected $prompt = '> ';

    public function getCommandsForCommandMode() {
        if ($this->commandRunning) {
            return array('exit', 'prompt', 'sh');
        }
        return array('shell', 'sh');
    }

    public function getCommandDescription($command, $short = false) {
        switch ($command) {
            case 'sh':
                if ($this->commandRunning) {
                    return 'Execute command in system shell and show output';
                    break;
                }
            case 'shell':
                return 'Interactive shell for Contrexx command mode';
                break;
            case 'prompt':
                return 'Change interactive shell command prompt';
                break;
            case 'exit':
                return 'Leave the interactive shell';
                break;
        }
    }

    public function executeCommand($command, $arguments)
    {
        //\DBG::activate(DBG_PHP);
        if ($command == 'exit') {
            $this->userWantsExit = true;
            return;
        } else if ($command == 'prompt') {
            if (!count($arguments)) {
                echo 'This command expects one argument
';
                return;
            }
            $this->prompt = preg_replace('/"(.*)"/', '$1', implode(' ', $arguments));
            return;
        } else if ($this->commandRunning && $command == 'sh') {
            if (!count($arguments)) {
                echo 'This command expects one argument
';
                return;
            }
            echo passthru(implode(' ', $arguments) . ' > `tty`');
            return;
        }
        
        $this->commandRunning = true;
        echo 'This is Contrexx Command mode shell v3.2
Please type `help` to find available commands
';
        $componentRepo = $this->cx->getDb()->getEntityManager()->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $commands = array();
        foreach ($componentRepo->findAll() as $component) {
            foreach ($component->getCommandsForCommandMode() as $command) {
                if (isset($commands[$command])) {
                    throw new \Exception('Command \'' . $command . '\' is already in index');
                }
                $commands[$command] = $component;
            }
        }

        while (!$this->userWantsExit) {
            echo $this->prompt;
            flush();
            if (ob_get_level() != 0) {
                ob_flush();
            }
            // read from command line
            $params = explode(' ', trim(fgets(STDIN)));

            // execute command if possible
            $command = current($params);
            $params = array_slice($params, 1);
            if (!isset($commands[$command])) {
                echo 'Unknown command \'' . $command . '\'
';
                continue;
            }
            try {
                $commands[$command]->executeCommand($command, $params);
            } catch (\Exception $e) {
                echo 'Command died with an ' . get_class($e) . ' with message . ' . $e->getMessage();
            }
        }
        echo "Bye!\r\n";
        $this->commandRunning = false;
    }

    protected function findCommand($name) {
        $componentRepo = $this->cx->getDb()->getEntityManager()->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $component = $componentRepo->findOneBy(array('name'=>$name));
        return $component;
    }
    
    public function getControllerClasses() {
        return array();
    }
}

