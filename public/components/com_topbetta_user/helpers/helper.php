<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.mail.mail');

class UserMAIL extends JMAIL
{
	/**
	 * Set the E-Mail body
	 *
	 * @access public
	 * @param string $content Body of the e-mail and append disclaimer text
	 * @return void
	 * @since 1.5
	 */
	public function setBody($content, $embed_disclaimer = true, $ishtml= false)
	{
		if ($embed_disclaimer) {
			$disclaimer = $this->_getDisclaimer();
			
			$this->Body = $ishtml ? ($content . "<br /><br />" . $disclaimer) : JMailHelper::cleanText($content . "\n\n" . $disclaimer);
		} else {
			$this->Body = $content;
		}
	}
	
	private function _getDisclaimer()
	{
		$config =& JComponentHelper::getParams( 'com_topbetta_user' );
		return $config->get('disclaimerText');
	}


	public function sendUserEmail($email_name, $email_params, $email_replacements, $embed_disclaimer=true)
	{
		$config =& JComponentHelper::getParams('com_topbetta_user');

		$mailfrom	= isset($email_params['mailfrom']) ? $email_params['mailfrom'] : $config->get('mailFrom');
		$fromname	= isset($email_params['fromname']) ? $email_params['fromname'] : $config->get('fromName');
		$mailto		= $email_params['mailto'];
		$subject	= $email_params['subject'];
		$ishtml		= isset($email_params['ishtml']) ? $email_params['ishtml'] : false;

		$email_subject_field	= $email_name . 'Subject';
		$email_body_field		= $email_name . 'Text';
		
		$text = $config->get($email_body_field);
		
		$config_email_subject = $config->get($email_subject_field);
		if (!empty($config_email_subject)) {
			$subject = $config_email_subject;
		}

		foreach($email_replacements as $token => $replace) {
			$subject	= str_replace('[' . $token . ']', $replace, $subject);
			$text		= str_replace('[' . $token . ']', $replace, $text);
		}
		
		$disclaimer_embeded = strpos($text, '[disclaimer]') !== false;
		if ($disclaimer_embeded) {
			$text = str_replace('[disclaimer]', $ishtml ? $this->_getDisclaimer() : JMailHelper::cleanText($this->_getDisclaimer()), $text);
			
			$embed_disclaimer = false;
		}
		
		$nl2br = strpos($text, '<html') === false;
		if ($ishtml && $nl2br) {
			$text = nl2br($text);
		}
		
		$this->ClearAddresses();
		$this->setSender(array($mailfrom, $fromname));
		$this->addReplyTo(array($mailfrom, $fromname));
		$this->addRecipient($mailto);
		$this->setSubject($subject);
		$this->setBody($text, $embed_disclaimer, $ishtml);
		$this->IsHTML($ishtml);

		return($this->Send());
	}

	public function sendUserToptippaEmail($email_name, $email_params, $email_replacements, $embed_disclaimer=true)
	{
		$config =& JComponentHelper::getParams('com_topbetta_user');

		$mailfrom	= isset($email_params['mailfrom']) ? $email_params['mailfrom'] : $config->get('mailFrom');
		$fromname	= isset($email_params['fromname']) ? $email_params['fromname'] : $config->get('fromName');
		$mailto		= $email_params['mailto'];
		$subject	= $email_params['subject'];
		$ishtml		= isset($email_params['ishtml']) ? $email_params['ishtml'] : false;

		$email_subject_field	= $email_name . 'Subject';
		$email_body_field		= $email_name . 'Text';
		
		//$text = $config->get($email_body_field);
		$text = isset($email_params['body']) ? $email_params['body'] : $config->get($email_body_field);
		
		$config_email_subject = $config->get($email_subject_field);
		if (!empty($config_email_subject)) {
			$subject = $config_email_subject;
		}

		foreach($email_replacements as $token => $replace) {
			$subject	= str_replace('[' . $token . ']', $replace, $subject);
			$text		= str_replace('[' . $token . ']', $replace, $text);
		}
		
		$disclaimer_embeded = strpos($text, '[disclaimer]') !== false;
		if ($disclaimer_embeded) {
			$text = str_replace('[disclaimer]', $ishtml ? $this->_getDisclaimer() : JMailHelper::cleanText($this->_getDisclaimer()), $text);
			
			$embed_disclaimer = false;
		}
		
		$nl2br = strpos($text, '<html') === false;
		if ($ishtml && $nl2br) {
			$text = nl2br($text);
		}
		
		$this->ClearAddresses();
		$this->setSender(array($mailfrom, $fromname));
		$this->addReplyTo(array($mailfrom, $fromname));
		$this->addRecipient($mailto);
		$this->setSubject($subject);
		$this->setBody($text, $embed_disclaimer, $ishtml);
		$this->IsHTML($ishtml);
		

		return($this->Send());
	}
}
