<?php
require_once ('services/photo/PhotoGalleryService.php');
require_once ('Project.php');

class ProjectsService extends DAOService
{
	private $galleryService;
	
	public function __construct()
	{
		parent::__construct('projects');
		$this->galleryService = DAOService::instance('PhotoGalleryService');
	}
	
	public function getByUrl($url)
	{
		return $this->get(0, array(Expression::eq('url', $url), Expression::eq('temp', 0)));
	}
	
	protected function createFilter($values)
	{
		$values = parent::createFilter($values);
		$gallery = $this->galleryService->create();
		$values['name'] = '';
		$values['description'] = '';
		$values['shortdescription'] = '';
		$values['gallery'] = $gallery->id;
		$values['date'] = date('Y-m-d');
		return $values;
	}
	
	public function getInCategory($category, $admin = false, $page = -1, $itemsPerPage = -1, $orders = array())
	{
		$exp = array(Expression::eq('temp', 0), Expression::eq('category', $category));
		if( ! $admin)
			$exp[] = Expression::eq('published', 1);
		return $this->getList($exp, $page, $itemsPerPage, $orders);
	}
	
	public function save($values)
	{
		$values[temp] = 0;
		return $this->update($values);
	}
	
	public function count($category, $admin = false)
	{
		$expressions = array(Expression::eq('temp', 0), Expression::eq('category', $category));
		if( ! $admin)
			$expressions[] = Expression::eq('published', 1);
		return parent::count($expressions);
	}
	
	public function publish($id)
	{
		$project = $this->get($id);
		return $this->update(array(id=>$id, published=>(1 - $project->published)));
	}
}

?>