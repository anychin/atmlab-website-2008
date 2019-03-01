<?php
require_once 'projects/ProjectsService.php';
require_once 'projects/ProjectsCategoryService.php';
require_once ("PhotoUploadPart.php");

class ProjectsPart extends PhotoUploadPart
{
	private $service;
	
	private $categoryService;
	
	private $itemsPerPage = 20;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->service = DAOService::instance('ProjectsService');
		$this->categoryService = DAOService::instance('ProjectsCategoryService');
		$this->slices = array('image'=>'projects_image', 'photo'=>'projects_photo');
	}
	
	protected function getImageSlice()
	{
		$project = $this->service->get($_POST['project']);
		$category = $this->categoryService->get($project->category);
		$slices = Config::get('imageSlices');
		$slice = $slices[$this->slices[$this->url[0]]];
		$slice['big'] = new ImageResize($category->imagewidth, $category->imagewidth, ResizeFormat::DESCRIBE_W);
		return $slice;
	}
	
	protected function getPhotoSlice()
	{
		$project = $this->service->get($_POST['project']);
		$category = $this->categoryService->get($project->category);
		$slices = Config::get('imageSlices');
		$slice = $slices[$this->slices[$this->url[0]]];
		$slice['big'] = new ImageResize($category->imagewidth, $category->imagewidth, ResizeFormat::DESCRIBE_W);
		return $slice;
	}
	
	protected function doAll()
	{
		$this->title = 'проекты';
		$this->handle();
		parent::doAll();
	}
	
	private function handle()
	{
		if(count($this->url) > 0 && $this->url[0] == 'categories')
		{
			$this->url = ArrayUtil::splice($this->url, 1);
			$this->handleCategory();
			Template::add('inCategory', true);
		}
		else
			$this->handleProject();
	}
	
	private function handleCategory()
	{
		$this->pageTitle = 'Категории проектов';
		if(count($this->url) == 0)
		{
			Template::add('categories', $this->categoryService->getCategoriesList());
			$this->registerButton(new Button('new', '/admin/projects/categories/new', 'Создать'));
			return;
		}
		if($this->url[0] == 'new')
		{
			$this->output = false;
			$category = $this->categoryService->create();
			Part::navigate('/projects/categories/edit/' . $category->id);
			return;
		
		}
		if($this->url[0] == 'edit')
		{
			Template::add('categories', $this->categoryService->getCategoriesList());
			Template::add('category', $this->categoryService->get($this->url[1]));
			$this->registerButton(new Button('save', '', 'Сохранить'));
			return;
		}
		if($this->url[0] == 'delete')
		{
			$category = $this->categoryService->delete($this->url[1]);
			$this->output = false;
			return;
		}
	}
	
	private function getCategories()
	{
		$categories = $this->categoryService->getCategoriesList();
		Template::add('categories', $categories);
		$counts = array();
		for($i = 0; $i < count($categories); $i++)
			$counts[$categories[$i]->id] = $this->service->count($categories[$i]->id, true);
		Template::add('counts', $counts);
	}
	
	private function handleProject()
	{
		$this->pageTitle = 'Проекты';
		if(count($this->url) == 0)
		{
			$this->getCategories();
			return;
		}
		$category = $this->categoryService->getByUrl($this->url[0]);
		Template::add('category', $category);
		$page = 0;
		if(count($this->url) < 2 || $this->url[1] == 'page')
		{
			if($this->url[1] == 'page')
				$page = intval($this->url[2]) - 1;
			$this->getList($category, $page);
			$this->registerButton(new Button('new', '/admin/projects/' . $category->url . '/new', 'Создать'));
			$this->getCategories();
			return;
		}
		if($this->url[1] == 'new')
		{
			$this->output = false;
			$project = $this->service->insert(array('category'=>$category->id));
			Part::navigate('/projects/' . $category->url . '/edit/' . $project->id);
			return;
		}
		if($this->url[1] == 'edit')
		{
			Template::add('project', $this->service->get($this->url[2]));
			$this->getCategories();
			$this->registerButton(new Button('save', '', 'Сохранить'));
			return;
		}
		
		if($this->url[0] == 'delete')
		{
			$this->service->delete($this->url[1]);
			$this->output = false;
			return;
		}
		if($this->url[0] == 'publish')
		{
			$this->service->publish($this->url[1]);
			$this->output = false;
			return;
		}
	}
	
	public function formMethod()
	{
		$data = parent::formMethod();
		if(count($this->url) == 0)
			$this->saveItem($data);
		else
			$this->saveCategory($data);
		FormController::sendData();
	}
	
	private function getList($category, $page)
	{
		$count = $this->service->count($category->id, true);
		Template::add('pages', ceil($count / $this->itemsPerPage));
		Template::add('projects', $this->service->getInCategory($category->id, true, $page, $this->itemsPerPage));
		Template::add('currentPage', $page);
		Template::add('itemsPerPage', $this->itemsPerPage);
	}
	
	private function saveItem($data)
	{
		$item = $this->service->getByUrl($data[url]);
		if($item != null && $item->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
			$this->service->save($data);
	}
	
	private function saveCategory($data)
	{
		$cat = $this->categoryService->getByUrl($data[url]);
		if($cat != null && $cat->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
			$this->categoryService->save($data);
	}
	
	public static function registerInMenu()
	{
		return new MenuItem('Проекты', '', 5, array(new MenuItem('Категории проектов', '/projects/categories'), new MenuItem('Проекты', '/projects')));
	}
	
	protected function handleImageDelete($id)
	{
		$this->service->update(array('id'=>$id, 'image'=>'NULL'));
	}
	
	protected function handleImageUpload($id, $image)
	{
		$this->service->save(array('id'=>$id, 'image'=>$image->id));
	}
}

?>