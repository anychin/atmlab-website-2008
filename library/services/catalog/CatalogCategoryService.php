<?php
require_once ('CatalogService.php');
require_once ('services/tree/TreeService.php');

class CatalogCategoryService extends TreeService
{
	private $catalogService;
	
	public function __construct()
	{
		parent::__construct('catalogcategory');
		$this->catalogService = DAOService::instance('CatalogService');
	}
	
	protected function createFilter($values)
	{
		$values = parent::createFilter($values);
		$values[description] = '';
		return $values;
	}
	
	public function getCategory($id, $admin = false, $fields = array())
	{
		return parent::getNode($id, $admin, $fields);
	}
	
	public function delete($id)
	{
		parent::delete($id);
		$this->catalogService->deleteAllInCategory($id);
	}
	
	function getSubCategories($parent, $admin = false, $fields = array())
	{
		return parent::getSubNodes($parent, $admin, $fields);
	}
	
	public function getRootCategories($admin = false, $fields = array())
	{
		return parent::getRootNodes($admin, $fields);
	}
}
?>