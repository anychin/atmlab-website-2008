<?php

class NewsCategoryService extends DAOService
{

	public $defaultCategory = 'common';

	public function __construct()
	{
		parent::__construct('newscategory');
	}

	public function delete($id)
	{
		$category = $this->get($id);
		parent::delete($id);
		return $category;
	}
	
	public function save($data)
	{
		$data[temp] = 0;
		return $this->update($data);
	}
	
	public function create()
	{
		return $this->insert(array(name=>''));
	}

	public function getByUrl($url = '')
	{
		if($url == '')
			$url = $this->defaultCategory;
		return $this->get(0, array(Expression::eq('url', $url), Expression::eq('temp', 0)));
	}
	
	public function getCategoriesList($fields = array())
	{
		return $this->getList(Expression::eq('temp', 0), -1, -1, array(), $fields);
	}
}
?>