<?php
class ImageResizeService extends DAOService
{
	
	public function __construct()
	{
		parent::__construct('imageresize');
	}
	
	public function getResizes($imageId)
	{
		return $this->getList(Expression::eq('image', $imageId), -1, -1, array(Order::desc('name')));
	}
	
	public function create($imageId)
	{
		return $this->insert(array('image'=>$imageId));
	}
	
	public function getResize($imageId, $name)
	{
		return $this->get(0, array(Expression::eq('image', $imageId), Expression::eq('name', $name)));
	}
	
	public function deleteResizes($imageId)
	{
		$resizes = $this->getResizes($imageId);
		for($i = 0; $i < count($resizes); $i++)
			$this->deleteResize($resizes->id);
	}
	
	public function deleteResize($id)
	{
		//$resize = $this->get($id);
		$this->delete($id);
		//@unlink($resize->path);
	}
}
?>