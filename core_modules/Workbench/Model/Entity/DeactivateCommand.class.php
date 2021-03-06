<?php
/**
 * Command to deactivate components
 * @author Michael Ritter <michael.ritter@comvation.com>
 */

namespace Cx\Core_Modules\Workbench\Model\Entity;

/**
 * Command to deactivate components
 * @author Michael Ritter <michael.ritter@comvation.com>
 */
class DeactivateCommand extends Command {
    
    /**
     * Command name
     * @var string
     */
    protected $name = 'deactivate';
    
    /**
     * Command description
     * @var string
     */
    protected $description = 'Deactivates a component (core, core_module, lib, module)';
    
    /**
     * Command synopsis
     * @var string
     */
    protected $synopsis = 'workbench(.bat) deactivate [core|core_module|lib|module] {component_name}';
    
    /**
     * Command help text
     * @var string
     */
    protected $help = 'Deactivates a component of the specified type named {component_name}.';
    
    /**
     * Execute this command
     * @param array $arguments Array of commandline arguments
     */
    public function execute(array $arguments) {
        
        $component = new \Cx\Core\Core\Model\Entity\ReflectionComponent($arguments[3], $arguments[2]);
        $component->deactivate();
        
        $this->interface->show('Done');
    }
}
