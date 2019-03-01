<?php
require_once 'PhotoUploadPart.php';

class PhotoPart extends PhotoUploadPart  
{
	public function __construct($name)
	{
		parent::__construct($name);
		$this->pageTitle = 'Фотогалереи сайта';
	}
	
	public function doAll()
	{
		$this->handle();
		$this->title = 'Фотогалереи сайта';
		parent::doAll();
	}
	
	private function handle()
	{
		if(count($this->url) == 0)
		{
			$this->getAllGallery();
			$this->registerButton(new Button('new', '/admin/photo/new', 'Новая'));
			return;
		}
		if($this->url[0] == 'edit')
		{
			$this->getAllGallery();
			Template::add('gallery', $this->photoGalleryService->get($this->url[1]));
			Template::add('galleryForm', true);
			$this->registerButton(new Button('save', '', 'Сохранить'));
			return;
		}
		if($this->url[0] == 'new')
		{
			$this->output = false;
			$gallery = $this->photoGalleryService->create();
			$this->navigate('/photo/edit/'.$gallery->id);
			return;
		}
		if($this->url[0] == 'delete')
		{
			$this->output = false;
			$gallery = $this->photoGalleryService->delete($this->url[1]);
			return;
		}
	}
	
	private function getAllGallery()
	{
		Template::add('galleries', $this->photoGalleryService->getList(array(Expression::eq('temp', 0)), -1, -1, null, array('id', 'title','url')));
	}
	
	public static  function registerInMenu()
	{
		return new MenuItem('Фотогалереи', '/photo/', 4);
	}
	
	public function formMethod()
	{
		$data = parent::formMethod();
		if($this->url[0] == 'gallery')
			$this->savePhotoGallery($data);
		FormController::sendData();
	}
	
	private function savePhotoGallery($data){
		$galleryByUrl = $this->photoGalleryService->getByUrl($data[url]);
		$gallery = $this->photoGalleryService->get($data[id]);
		if($galleryByUrl != null && $gallery->url != $galleryByUrl->url)
			FormController::addError('url', 'exists');
		if(FormController::isValid())
		{
			$data['temp'] = 0;
			$this->photoGalleryService->update($data);
		}
	}
}
?>