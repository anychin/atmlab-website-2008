<?php
class Db_Photo_DAO extends Dao_Dao
{
	public $model = 'Db_Photo_Photo';
	
	public function __construct()
	{
		parent::__construct('photo');
	}
	
	public function getByGallery($galleryId)
	{
		return $this->getList(Dao_Exp::eq('gallery', $galleryId),  - 1,  - 1, array(Dao_Order::asc('ord')));
	}
	
	public function create($galleryId)
	{
		return $this->insert(array('gallery'=>$galleryId, 'date'=>date('Y-m-d')));
	}
	
	public function save($data)
	{
		$data['temp'] = 0;
		return $this->update($data);
	}
	
	public function deleteInGallery($galleryId)
	{
		$this->delete(0, Dao_Exp::eq('gallery', $galleryId));
	}
	
	public function upload($photoId, $slice = null)
	{
		$slices = Config::getInstance()->slices;
		if($slice == null)
			$slice = $slices['photo_image'];
		$image = Db_Image_DAO::instance()->upload($slice);
		if($image)
			return $this->save(array('id'=>$photoId, 'image'=>$image->id, 'published'=>1));
		return $this->get($photoId);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>