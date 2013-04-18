<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class PaymentModelEwayinvoicenumber extends JModel
{
    /**
	* Method to generate an invoice number
	*
	* @return int invoice number
	*/
    function generateInvoiceNumber()
    {
    	// Get the table
    	$db =& Jfactory::getDBO();
		
    	$table = $db->nameQuote('#__eway_invoice_number');
    	
    	$query = "LOCK TABLES $table WRITE";
		$db->setQuery($query);
		$db->query();
		
    	$query = "UPDATE $table SET id=@invoice_number:=id+1";
		$db->setQuery($query);
		$db->query();
    	
    	$query = "UNLOCK TABLES";
		$db->setQuery($query);
		$db->query();
    	
		$query = "SELECT @invoice_number";
		$db->setQuery($query);
		$db->query();
		return $db->loadResult();
    }
}
?>