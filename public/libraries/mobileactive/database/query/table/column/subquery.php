<?php

defined('_JEXEC') or die();

jimport('mobileactive.database.query.table.column');

class DatabaseQueryTableColumnSubquery extends DatabaseQueryTableColumn
{
	public function getSQL($type = null)
	{
		$query = new DatabaseQuery($this->getName());
		$alias = $this->_getAliasSQL($type);

		return sprintf('(%s)%s', $query->getSelect(), $alias);
	}
}