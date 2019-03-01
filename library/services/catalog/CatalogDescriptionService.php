<?php
class CatalogDescriptionService extends DAOService
{
	public function __construct()
	{
		parent::__construct('catalogadvanced');
	}
	
	public function create()
	{
		return $this->insert();
	}
}
?>