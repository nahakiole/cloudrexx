<?PHP

/**
 * Payment manager.
 * @package     contrexx
 * @copyright   CONTREXX CMS - COMVATION AG
 * @subpackage  module_shop
 * @todo        Edit PHP DocBlocks!
 */

/**
 * Payment manager.
 * @package     contrexx
 * @copyright   CONTREXX CMS - COMVATION AG
 * @subpackage  module_shop
 * @todo        Edit PHP DocBlocks!
 */

class Payment
{
    /**
     * Array of available payment methods
     * @var     array
     * @access  public
     */
    var $arrPaymentObject = array();


    /**
     * Constructor (PHP4)
     * @access  public
     */
    function Payment()
    {
        $this->__construct();
    }

    /**
     * Constructor (PHP5)
     *
     * Sets up the array of payment objects
     */
    function __construct()
    {
        global $objDatabase;
        $query = "
            SELECT id, name, processor_id, costs, costs_free_sum,
                   sort_order, status
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_payment
          ORDER BY id
        ";
        $objResult = $objDatabase->Execute($query);
        while($objResult && !$objResult->EOF) {
            $this->arrPaymentObject[$objResult->fields['id']] = array(
                'id'             => $objResult->fields['id'],
                'name'           => $objResult->fields['name'],
                'processor_id'   => $objResult->fields['processor_id'],
                'costs'          => $objResult->fields['costs'],
                'costs_free_sum' => $objResult->fields['costs_free_sum'],
                'sort_order'     => $objResult->fields['sort_order'],
                'status'         => $objResult->fields['status']
            );
            $objResult->MoveNext();
        }
    }


    /**
     * Returns the countries related payment ID array.
     *
     * @global   ADONewConnection
     * @param    integer $countryId         The country ID
     * @param    array   $arrCurrencies     The currencies array
     * @return   array   $arrPaymentId      Array of payment IDs, like:
     *                                      array( index => paymentId )
     */
    function getCountriesRelatedPaymentIdArray($countryId, $arrCurrencies)
    {
        global $objDatabase;

        require_once ASCMS_MODULE_PATH.'/shop/payments/paypal/Paypal.class.php';
        $objPayPal = new PayPal();

        $arrAcceptedCurrencyCodes = array();

        foreach ($arrCurrencies as $arrCurrency) {
            if (   $arrCurrency['status']
                && in_array($arrCurrency['code'],
                            $objPayPal->arrAcceptedCurrencyCodes)
            ) {
                array_push($arrAcceptedCurrencyCodes, $arrCurrency['code']);
            }
        }

        $arrPaymentId = array();
        $query = "
            SELECT p.payment_id as payment_id
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_rel_countries AS c,
                   ".DBPREFIX."module_shop".MODULE_INDEX."_zones AS z,
                   ".DBPREFIX."module_shop".MODULE_INDEX."_rel_payment AS p
             WHERE c.countries_id=".intval($countryId)."
               AND z.activation_status=1
               AND (z.zones_id=c.zones_id
               AND z.zones_id=p.zones_id)
        ";
        $objResult = $objDatabase->Execute($query);
        while ($objResult && !$objResult->EOF) {
            if (   isset($this->arrPaymentObject[$objResult->fields['payment_id']])
                && $this->arrPaymentObject[$objResult->fields['payment_id']]['status'] == 1
                && (   $this->arrPaymentObject[$objResult->fields['payment_id']]['processor_id'] != 2
                    || count($arrAcceptedCurrencyCodes) > 0)
            ) {
                $arrPaymentId[] = $objResult->fields['payment_id'];
            }
            $objResult->MoveNext();
        }
        return $arrPaymentId;
    }



    /**
     * Return HTML code for the shipment dropdown menu
     * @param   string  $selectedId     Optional pre-selected shipment ID
     * @param   string  $onchange       Optional onchange function
     * @param   integer $countryId      Country ID
     * @param   array   $arrCurrencies  Currencies array
     * @return  string                  HTML code for the dropdown menu
     * @global  array
     */
    function getPaymentMenu($selectedId=0, $onchange='', $countryId=0, $arrCurrencies='')
    {
        global $_ARRAYLANG;

        $arrPaymentId = $this->getCountriesRelatedPaymentIdArray($countryId, $arrCurrencies);
        $onchange = !empty($onchange) ? 'onchange="'.$onchange.'"' : '';
        $menu = "\n<select name='paymentId' $onchange>\n".
            (intval($selectedId) == 0
                ?   "<option value='0' selected='selected'>".
                    $_ARRAYLANG['TXT_SHOP_PLEASE_SELECT'].
                    "</option>\n"
                :   ''
            );

        foreach($arrPaymentId as $id) {
            $selected = ($id==intval($selectedId) ? "selected='selected'" : '');
            $menu .=
                "<option value='$id' $selected>".
                $this->arrPaymentObject[$id]['name'].
                "</option>\n";
        }
        $menu .= "</select>\n";
        return $menu;
    }


    /**
     * Get the payment name for the ID given
     * @static
     * @global  ADONewConnection
     * @param   integer   $paymentId      The payment ID
     * @return  mixed                     The payment name on success,
     *                                    false otherwise
     * @since   1.2.1
     */
    function getNameById($paymentId)
    {
        global $objDatabase;

        $objResult = $objDatabase->Execute("
            SELECT name
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_payment
             WHERE id=$paymentId
        ");
        if ($objResult && !$objResult->EOF) {
            return $objResult->fields['name'];
        }
        return false;
    }


    /**
     * Returns the name of the payment processor with the given ID,
     * or '' if it couldn't be found, or if an error was encountered.
     * @return  string                  The name of the payment processor
     * @global  ADONewConnection
     * @todo    This method belongs to the PaymentProcessing class.  It's
     *          still here because the backend only uses this class, and not
     *          PaymentProcessing.
     */
    function getPaymentProcessorName($processorId)
    {
        global $objDatabase;
        $query = "
            SELECT name
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_payment_processors
             WHERE id=$processorId
        ";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            return '';
        }
        return $objResult->fields['name'];
    }


    /**
     * Returns the ID of the payment processor for the given payment ID
     * @static
     * @param   integer   $paymentId    The payment ID
     * @return  integer                 The payment processor ID on success,
     *                                  false otherwise
     * @global  ADONewConnection
     */
    //static
    function getPaymentProcessorId($paymentId)
    {
        global $objDatabase;

        $query = "
            SELECT processor_id
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_payment
             WHERE id=$paymentId
        ";
        $objResult = $objDatabase->Execute($query);
        if ($objResult) {
            return $objResult->fields['processor_id'];
        }
        return false;
    }
}

?>
