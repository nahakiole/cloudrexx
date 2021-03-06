<?php

/**
 * Paymill online payment
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Däppen <thomas.daeppen@comvation.com>
 * @author      ss4u <ss4u.comvation@gmail.com>
 * @version     3.1.1
 * @package     contrexx
 * @subpackage  module_shop
 */

/**
 * PostFinance online payment
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Däppen <thomas.daeppen@comvation.com>
 * @author      ss4u <ss4u.comvation@gmail.com>
 * @version     3.1.1
 * @package     contrexx
 * @subpackage  module_shop  
 */
class PaymillHandler {
    /**
     * section name
     *
     * @access  private
     * @var     string
     */
    public static $sectionName = null;

    /**
     * Error messages
     * @access  public
     * @var     array
     */
    public static $arrError = array();
    
    /**
     * Paymill JS Bridge url
     * 
     * @var string
     */
    public static $paymillJsBridge = "https://bridge.paymill.com";

    /**
     * Warning messages
     * @access  public
     * @var     array
     */
    public static $arrWarning = array();
    
    public static function processRequest($token, $arrOrder) {
        global $_CONFIG;
        
        if (empty($token)) {
            return array(
                        'status'  => 'error',
                        'message' => 'invalid token'
                       );
        }
        
        $testMode = intval(\Cx\Core\Setting\Controller\Setting::getValue('paymill_use_test_account','Shop')) == 0;
        $apiKey   = $testMode ? \Cx\Core\Setting\Controller\Setting::getValue('paymill_test_private_key','Shop') : \Cx\Core\Setting\Controller\Setting::getValue('paymill_live_private_key','Shop');
        
        if ($token) {
            try {
                
                $request     = new Paymill\Request($apiKey);
                $transaction = new Paymill\Models\Request\Transaction();
                $transaction->setAmount($arrOrder['amount'])
                            ->setCurrency($arrOrder['currency'])
                            ->setToken($token)
                            ->setDescription($arrOrder['note'])
                            ->setSource('contrexx_'.$_CONFIG['coreCmsVersion']);
                
                DBG::log("Transactoin created with token:". $token);
                $response = $request->create($transaction);
                $paymentId = $response->getId();
                DBG::log("Payment ID".$paymentId);
                
                return array('status' => 'success', 'payment_id' => $paymentId);
            } catch(\Paymill\Services\PaymillException $e) {
                //Do something with the error informations below
                return array(
                        'status' => 'error',
                        'response_code' => $e->getResponseCode(),
                        'status_code' => $e->getStatusCode(),
                        'message'       => $e->getErrorMessage()
                       );
            }
        }
    }
    
    static function fieldset($legend = false, $selfClose = false) {
        $fieldset = self::openElement('fieldset');        
        if ($legend) {
            $fieldset .= self::getElement('legend', '', $legend);
        }
        if ($selfClose) {
            $fieldset .= self::closeElement('fieldset');
        }
        
        return $fieldset;
    }
    
    static function getElement($elm, $attrbs = '', $content ='') {        
        return "<$elm ". self::getFormattedAttrbs($attrbs) . ">". $content ."</$elm> \n";
    }
    
    static function openElement($elm , $attributes = '')
    {        
        return "<$elm ". self::getFormattedAttrbs($attributes)." > \n";
    }
    
    static function closeElement($elm) 
    {        
        return "</$elm> \n";
    }
    
    static function getFormattedAttrbs($attributes) {
        $html = '';
        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                $html .= $attribute;
            }
        } else {
            $html .= $attributes;
        }
        return $html;
    }
}
