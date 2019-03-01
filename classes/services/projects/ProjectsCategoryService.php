<?php
class ProjectsCategoryService extends DAOService
{
	public function __construct()
	{
		parent::__construct('projectscategory');
	}
	
	public function getCategoriesList($fields = array())
	{
		return $this->getList(Expression::eq('temp', 0),  - 1,  - 1, array(), $fields);
	}
	
	public function create()
	{
		return $this->insert(array(name=>''));
	}
	
	public function save($data)
	{
		$data[temp] = 0;
		return $this->update($data);
	}
	
	public function getByUrl($url)
	{
		return $this->get(0, array(Expression::eq('url', $url), Expression::eq('temp', 0)));
	}
}
?>