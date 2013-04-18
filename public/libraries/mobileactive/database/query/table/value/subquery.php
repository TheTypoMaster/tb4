<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.query.table.value');

class DatabaseQueryTableValueSubquery extends DatabaseQueryTableValue
{
	protected $_escape = false;

	public function getSQL($type = null)
	{
		$query = new DatabaseQuery($this->getValue());
		return sprintf('(%s)', $query->getSelect());
	}
}