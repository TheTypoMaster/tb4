<?php
/**
* PushMessage object
* @author geoff
*
*/
class PushMessage
{
	const STANDARD_MESSAGE = 1,
	SKIP_MESSAGE = 2,
	ERROR_MESSAGE = 3;

	private $element = null;
	private $child_list = null;
	private $sub_child = null;
	private $current_index = -1;
	private $skip_message = false;
	private $error_message = false;
	/** 
	 * static function for custom error message
	 * @param string $error
	 * 
	 */
	static public function customError($error_message)
	{
		$message = new SimpleXMLElement("<message />");
		
		$error = $message->addChild('error');
		$error->addAttribute('code','999');
		$error->addAttribute('message', $error_message);
		
		$xml = $message->xpath('error');
						
		return new PushMessage($xml[0], self::ERROR_MESSAGE);
	}
	/**
	 * _construct
	 * @param SimpleXMLElement $message
	 * @param integer $message_category
	 */
	public function __construct(SimpleXMLElement $message, $message_category = self::STANDARD_MESSAGE)
	{
		$this->message = $message;

		$this->skip_message = $message_category == self::SKIP_MESSAGE ? true : false;
		$this->error_message = $message_category == self::ERROR_MESSAGE ? true : false;
	}
	/**
	 * getName
	 * return name of message
	 * @return string
	 */
	public function getName()
	{
		return (string) $this->message->getName();
	}

	/**
	 * getXML
	 * return the message xml
	 * @return string
	 */
	public function getXml()
	{
		return $this->message->asXML();
	}
	/**
	 * getAttribute
	 * Return selected attribute
	 * @param string $attribute
	 * @return string
	 */
	public function getAttribute($attribute)
	{
		$attribute_result =  $this->message->xpath('@'.$attribute);
		if (empty($attribute_result)){
			return;
		}
		return (string) $attribute_result[0];
	}
	/**
	 * fetchChild
	 * return child message
	 * @return mixed
	 */
	public function fetchChild()
	{
		$this->current_index++;
		$this->child_list = $this->message->children();
		$count = count($this->child_list);

		if ($this->current_index >= $count){
			return false;
		}

		return new PushMessage($this->child_list[$this->current_index]);
	}
	/**
	 * isSkipMessage
	 * @return boolean
	 */
	public function isSkipMessage()
	{
		return ($this->skip_message);
	}
	/**
	 * isErrorMessage
	 * @return boolean
	 */
	public function isErrorMessage()
	{
		return ($this->error_message);
	}
}