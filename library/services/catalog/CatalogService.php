<?php
require_once ('CatalogDescriptionService.php');
require_once ('Product.php');

class CatalogService extends DAOService
{
	private $descriptionService;
	
	public function __construct()
	{
		parent::__construct('catalogitems');
		$this->descriptionService = DAOService::instance('CatalogDescriptionService');
	}
	
	public function deleteAllInCategory($category)
	{
		$items = $this->getList(Expression::eq('category', $category),  - 1,  - 1, array(), array('id'));
		$ids = '';
		for($i = 0; $i < count($items); $i++)
			$ids .= $items[$i]->id . ',';
		$ids = preg_replace('/,$/', '', $ids);
		if($ids != '')
			$this->descriptionService->delete(0, Expression::query('id in ($ids)'));
		$this->delete(0, Expression::eq('category', $category));
	}
	
	public function create($category)
	{
		$description = $this->descriptionService->create();
		return $this->insert(array(category=>$category, advanced=>$description->id));
	}
	
	public function publish($id, $val)
	{
		$this->update(array(published=>$val, id=>$id));
	}
	
	public function getInCategory($category, $page = -1, $itemsPerPage = -1, $orders = null, $fields = null, $admin = false)
	{
		$expressions = array(Expression::eq('temp', false), Expression::eq('category', $category));
		if( ! $admin)
			$expressions[] = Expression::eq('published', 1);
		return $this->getList($expressions, $page, $itemsPerPage, $orders, $fields);
	}
	
	public function save($data)
	{
		$data[temp] = 0;
		$this->update($data);
	}
	
	public function getByUrl($url, $admin = false)
	{
		$expressions = array(Expression::eq('url', $url));
		if( ! $admin)
		{
			$expressions[] = Expression::eq('temp', 0);
			$expressions[] = Expression::eq('published', 1);
		}
		return $this->get(0, $expressions);
	}
}
?>