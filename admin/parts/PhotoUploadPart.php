<?php
require_once 'services/photo/PhotoGalleryService.php';

class PhotoUploadPart extends Part 
{
	protected $photoUrls = array('photo');
	
	protected $imageUrls = array('image');
	
	protected $photoService;
	
	protected $photoGalleryService;
	
	protected $imageService;
	
	protected $slices = array();
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->photoGalleryService = DAOService::instance('PhotoGalleryService');
		$this->photoService = DAOService::instance('PhotoService');
		$this->imageService = DAOService::instance('ImageService');
	}
	
	protected function doAll()
	{
		$this->parseUrl();
		parent::doAll();
	}
	
	protected function parseUrl()
	{
		if(ArrayUtil::inArray($this->url[0], $this->photoUrls))
		{
			if($this->url[1] == 'upload')
				$this->uploadPhoto();
			if($this->url[1] == 'edit')
				$this->editPhoto();
			if($this->url[1] == 'delete')
				$this->deletePhoto();
			return;
		}
		if(ArrayUtil::inArray($this->url[0], $this->imageUrls))
		{
			if($this->url[1] == 'upload')
				$this->uploadImage();
			if($this->url[1] == 'delete')
				$this->deleteImage();
			if($this->url[1] == 'resize')
				$this->uploadResize();
			if($this->url[1] == 'edit')
				$this->editImage();
			return;
		}
	}
	
	protected function handleImageDelete($id)
	{
		
	}
	
	protected function handleImageUpload($id, $image)
	{
		
	}
	
	protected function uploadImage()
	{
		$this->output = false;
		self::setXMLHeaders();
		$image = new Image($this->imageService->upload($this->getImageSlice()));
		$this->handleImageUpload($_POST['id'], $image);
		echo $image->serialize();
	}
	
	protected function getImageSlice()
	{
		$slices = Config::get('imageSlices');
		return $slices[$this->slices[$this->url[0]]];
	}
	
	protected function getPhotoSlice()
	{
		$slices = Config::get('imageSlices');
		return $slices[$this->slices[$this->url[0]]];
	}
	
	protected function uploadResize()
	{
		$this->output = false;
		self::setXMLHeaders();
		$resize = new RealImageResize($this->imageService->uploadResize($_POST['id'], $this->url[2]));
		echo $resize->serialize();
		
	}
	
	protected function uploadPhoto()
	{
		$this->output = false;
		self::setXMLHeaders();
		$photo = $this->photoService->create($_POST['id']);
		$photo = new Photo($this->photoService->upload($photo->id, $this->getPhotoSlice()));
		echo $photo->serialize();
	}
	
	protected function deleteImage()
	{
		$this->output = false;
		$image = $this->handleImageDelete($this->url[2]);
		$this->imageService->deleteImage($image);
	}
	
	protected function deletePhoto()
	{
		$this->output = false;
		$this->photoService->delete($this->url[2]);
	}
	
	protected function editImage()
	{
		$this->pageTitle = 'Редактирование изображения';
		$this->registerButton(new Button('save', '', 'Сохранить'));
		Template::add('image', new Image($this->imageService->get($_GET['id'])));
	}
	
	protected function editPhoto()
	{
		$this->pageTitle = 'Редактирование фотографии';
		$photo = new Photo($this->photoService->get($this->url[2]));
		$this->registerButton(new Button('save', '', 'Сохранить'));
		Template::add('photo', $photo);
	}
	
	protected function savePhoto($data)
	{
		$this->photoService->update($data);
	}
	
	public function formMethod()
	{
		$data = parent::formMethod();
		if($this->url[0] == 'photo')
			$this->savePhoto($data);
		if($this->url[0] == 'image')
		{
			
		}
		return $data;
	}
}
?>