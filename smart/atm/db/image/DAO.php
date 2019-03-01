<?php
class Db_Image_DAO extends Dao_Dao
{
	
	public $model = 'Db_Image_Image';
	
	private $resizer;
	
	public $folder = '/images/uploaded/';
	
	public $srcFolder = '/images/uploaded/original/';
	
	public $file = 'flashfile';
	
	private $originalResize;
	
	public function __construct()
	{
		parent::__construct('image');
		$this->folder = SITE_DIR . $this->folder;
		$this->srcFolder = SITE_DIR . $this->srcFolder;
		$this->resizer = new Image_Resizer();
		$this->originalResize = new Image_Resize(0, 0, Image_Format::ENCODE);
	}
	
	public function upload($slice)
	{
		if(!$slice)
			throw new Exception('slice not found');
		$image = $this->insert();
		$info = $this->resizer->resize($this->originalResize, $image->id, 'jpg', $this->file, $this->srcFolder);
		if($info == null)
			$this->deleteImage($image->id);
		else
			$image = $this->update(array('id'=>$image->id, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		if($image == null)
			return null;
		foreach($slice as $name=>$format)
		{
			$resize = Db_Image_Resize_DAO::instance()->create($image->id);
			$info = $this->resizer->resize($format, $name.$image->id, 'jpg', $this->file, $this->folder);
			if($info == null)
				Db_Image_Resize_DAO::instance()->deleteResize($resize->id);
			else
				Db_Image_Resize_DAO::instance()->update(array('id'=>$resize->id, 'name'=>$name, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		}
		$this->resizer->clean();
		return $image;
	}
	
	public function uploadFile($slice, $file, $use_original = true)
	{
		if(!$slice)
			throw new Exception('slice not found');
		$image = $this->insert();
		if($use_original){
			$info = $this->resizer->resize($this->originalResize, $image->id, 'jpg', $file, $this->srcFolder);
			if($info == null)
				$this->deleteImage($image->id);
			else
				$image = $this->update(array('id'=>$image->id, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		}
		if($image == null)
			return null;
		foreach($slice as $name=>$format)
		{
			$resize = Db_Image_Resize_DAO::instance()->create($image->id);
			$info = $this->resizer->resize($format, $name.$image->id, 'jpg', $file, $this->folder);
			if($info == null)
				Db_Image_Resize_DAO::instance()->deleteResize($resize->id);
			else
				Db_Image_Resize_DAO::instance()->update(array('id'=>$resize->id, 'name'=>$name, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		}
		$this->resizer->clean();
		return $image;
	}
	
	public function uploadResize($image, $name)
	{
		$resize = Db_Image_Resize_DAO::instance()->getResize($image, $name);
		if($resize == null)
			return null;
		$newResize = Db_Image_Resize_DAO::instance()->create($image);
		$info = $this->resizer->resize($this->originalResize, $name.$image->id, 'jpg', $this->file, $this->folder);
		$newResize = Db_Image_Resize_DAO::instance()->update(array('id'=>$newResize->id, 'name'=>$name, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		Db_Image_Resize_DAO::instance()->delete($resize->id);
		$this->resizer->clean();
		return $newResize;
	}
	
	public function deleteImage($id)
	{
		Db_Image_Resize_DAO::instance()->deleteResizes($id);
		$this->delete($id);
	}
	
	public function getResizes($imageId)
	{
		return Db_Image_Resize_DAO::instance()->getResizes($imageId);
	}
	
	public static function instance()
	{
		return parent::instance(__CLASS__);
	}
}
?>