<?php
require_once ('services/image/ImageResizeService.php');
require_once ('image/ImageResizer.php');
require_once ('Image.php');
require_once ('RealImageResize.php');

class ImageService extends DAOService
{
	private $imageResizer;
	
	private $imageResizeService;
	
	public $folder = '/images/uploaded/';
	
	public $srcFolder = '/images/uploaded/original/';
	
	public $file = 'flashfile';
	
	private $originalResize;
	
	public function __construct()
	{
		parent::__construct('image');
		$this->folder = SITE_DIR . $this->folder;
		$this->srcFolder = SITE_DIR . $this->srcFolder;
		$this->imageResizeService = DAOService::instance('ImageResizeService');
		$this->imageResizer = new ImageResizer();
		$this->originalResize = new ImageResize(0, 0, ResizeFormat::ENCODE);
	}
	
	public function upload($slice)
	{
		$image = $this->insert();
		$info = $this->imageResizer->resize($this->originalResize, $image->id, 'jpg', $this->file, $this->srcFolder);
		if($info == null)
			$this->deleteImage($image->id);
		else
			$image = $this->update(array('id'=>$image->id, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		if($image == null)
			return null;
		foreach($slice as $name=>$format)
		{
			$resize = $this->imageResizeService->create($image->id);
			$info = $this->imageResizer->resize($format, $resize->id, 'jpg', $this->file, $this->folder);
			if($info == null)
				$this->imageResizeService->deleteResize($resize->id);
			else
				$resize = $this->imageResizeService->update(array('id'=>$resize->id, 'name'=>$name, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		}
		$this->imageResizer->clean();
		return $image;
	}
	
	public function uploadResize($image, $name)
	{
		$resize = $this->imageResizeService->get(0, array(Expression::eq('image', $image), Expression::eq('name', $name)));
		if($resize == null)
			return null;
		$newResize = $this->imageResizeService->create($image);
		$info = $this->imageResizer->resize($this->originalResize, $newResize->id, 'jpg', $this->file, $this->folder);
		$newResize = $this->imageResizeService->update(array('id'=>$newResize->id, 'name'=>$name, 'width'=>$info['width'], 'height'=>$info['height'], 'size'=>$info['size'], 'mime'=>$info['mime']));
		$this->imageResizeService->delete($resize->id);
		$this->imageResizer->clean();
		return $newResize;
	}
	
	public function deleteImage($id)
	{
		$this->imageResizeService->deleteResizes($id);
		//$image = $this->get($id);
		//@unlink($image->path);
		parent::delete($id);
	}
	
	public function getResizes($imageId)
	{
		return $this->imageResizeService->getResizes($imageId);
	}
}
?>