<?php
/**
 * Class ComponentController
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_order
 */

namespace Cx\Modules\Order\Controller;

/**
 * Class OrderException
 */
class OrderException extends \Exception {}

/**
 * Class ComponentController
 *
 * The main Order component
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_order
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    
    const ORDER_ACCESS_ID        = 184;
    const SUBSCRIPTION_ACCESS_ID  = 185;
    const INVOICE_ACCESS_ID       = 186;
    const PAYMENT_ACCESS_ID      = 187;

    /*
     * Constructor
     */
    public function __construct(\Cx\Core\Core\Model\Entity\SystemComponent $systemComponent, \Cx\Core\Core\Controller\Cx $cx) {
        parent::__construct($systemComponent, $cx);
    }
    
    public function getControllerClasses() {
        return array('Backend', 'Default', 'Invoice', 'Payment', 'Subscription');
    }
    
    public function postResolve(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        $evm = \Env::get('cx')->getEvents();
        
        $crmCrmContactEventListener = new \Cx\Modules\Order\Model\Event\CrmCrmContactEventListener();
        $evm->addModelListener(\Doctrine\ORM\Events::preRemove, 'Cx\\Modules\\Crm\\Model\\Entity\\CrmContact', $crmCrmContactEventListener);
    }
    
    /**
     * get command mode
     * 
     * @return type array
     */
    public function getCommandsForCommandMode() 
    {
        return array('Order');
    }
    
    /**
     * execute the command
     * 
     * @param type $command   command name
     * @param type $arguments argument name
     * 
     * @return type null
     */
    public function executeCommand($command, $arguments) 
    {
        $subcommand = null;
        if (!empty($arguments[0])) {
            $subcommand = $arguments[0];
        }
        switch ($command) {
            case 'Order':
                switch ($subcommand) {
                    case 'Cron':
                        $this->executeCommandCron();
                        break;
                    default:
                        break;
                }
                break;
            default :
                break;
        }
    }
    
    /**
     * Api Cron command
     * 
     * @return type null
     */
    public function executeCommandCron()
    {
        // Let the associated product entities know that their associated subscription
        // has expired by triggering the model event model/expired on the subscription
        $this->touchProductEntitiesOfExpiredSubscriptions();

        //  Terminate the active and expired subscription.
        $this->terminateExpiredSubscriptions();
    }
    
    /**
     * Inform the product entities that their associated Subscription has expired
     *
     * Let the associated product entities know that their associated Subscription
     * has expired (Subscription::$expirationDate < now) by triggering the model
     * event model/expired on the Subscription
     */
    public function touchProductEntitiesOfExpiredSubscriptions()
    {
        $subscriptionRepo = \Env::get('em')->getRepository('Cx\Modules\Order\Model\Entity\Subscription');
        $subscriptions    = $subscriptionRepo->getExpiredSubscriptions();
        
        if (\FWValidator::isEmpty($subscriptions)) {
            return;
        }

        foreach ($subscriptions as $subscription) {
            //Trigger the model event model/expired on the Subscription's product entity.
            \Env::get('cx')->getEvents()->triggerEvent('model/expired', array(new \Doctrine\ORM\Event\LifecycleEventArgs($subscription, \Env::get('em'))));
        }
        \Env::get('em')->flush();
    }

    /**
     * Terminate expired Subscriptions
     *
     * This method does call the method Subscription::terminate() on all Subscriptions
     * that are expired (Subscription::$expirationDate < now), but are still
     * active (Subscription::$state = active) or have been cancelled (Subscription::$state = cancelled).
     * Expired Subscriptions that are inactive (Subscription::$state = inactive) are not
     * terminated as long as they are inactive. This allows a Subscription to be re-activated
     * and resetting a new expiration date without having the Subscription automatically
     * being terminated.
     */
    public function terminateExpiredSubscriptions()
    {
        $subscriptionRepo = \Env::get('em')->getRepository('Cx\Modules\Order\Model\Entity\Subscription');
        $subscriptions    = $subscriptionRepo->getExpiredSubscriptions(array(
                                \Cx\Modules\Order\Model\Entity\Subscription::STATE_ACTIVE,
                                \Cx\Modules\Order\Model\Entity\Subscription::STATE_CANCELLED));
        
        if (\FWValidator::isEmpty($subscriptions)) {
            return;
        }
        
        foreach ($subscriptions as $subscription) {
            $subscription->terminate();
        }
        \Env::get('em')->flush();
    }
    
    /**
     * Register model events related to Order component
     */
    public function registerEvents() {
        $this->cx->getEvents()->addEvent('model/expired');
        $this->cx->getEvents()->addEvent('model/terminated');
        $this->cx->getEvents()->addEvent('model/payComplete');
    }
}
