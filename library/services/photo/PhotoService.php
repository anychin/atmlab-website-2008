<?php
require_once ('services/image/ImageService.php');
require_once ('Photo.php');

class PhotoService extends DAOService
{
	private $imageService;
	
	public function __construct()
	{
		parent::__construct('photo');
		$this->imageService = DAOService::instance('ImageService');
	}
	
	public function getByGallery($galleryId)
	{
		return $this->getList(Expression::eq('gallery', $galleryId),  - 1,  - 1, array(Order::asc('ord')));
	}
	
	public function create($galleryId)
	{
		return $this->insert(array('gallery'=>$galleryId, 'date'=>date('Y-m-d')));
	}
	
	public function save($data)
	{
		$data[temp] = 0;
		return $this->update($data);
	}
	
	public function upload($photoId, $slice = null)
	{
		$slices = Config::get('imageSlices');
		if($slice == null)
			$slice = $slices['photo_image'];
		$image = $this->imageService->upload($slice);
		return $this->save(array('id'=>$photoId, 'image'=>$image->id));
	}
}
?>