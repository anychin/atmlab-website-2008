<?php
require_once ('services/catalog/CatalogCategoryService.php');
require_once ('services/catalog/CatalogDescriptionService.php');
require_once ('services/image/ImageService.php');

class CatalogPart extends Part
{
	private $service;
	
	private $categoryService;
	
	private $descriptionService;
	
	private $imageService;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->categoryService = DAOService::instance('CatalogCategoryService');
		$this->descriptionService = DAOService::instance('CatalogDescriptionService');
		$this->service = DAOService::instance('CatalogService');
		$this->imageService = DAOService::instance('ImageService');
		$this->title = Dictionary::get('catalog.title');
	}
	
	public function doAll()
	{
		if($this->url[0] == 'upload' && $this->url[1] == 'image')
		{
			$this->output = false;
			$this->uploadImage();
			return;
		}
		if($this->url[0] == 'image' && $this->url[1] == 'delete')
		{
			$this->output = false;
			$product = $this->service->get($this->url[2]);
			$this->imageService->deleteImage($product->image);
			return;
		}
		if($this->url[0] == 'categories')
		{
			Template::add('categoryPart', true);
			$this->url = ArrayUtil::splice($this->url, 1);
			$this->parseCategoryUrl();
			$this->pageTitle = Dictionary::get('catalog.category.pageTitle');
		}
		else
		{
			$this->parseItemUrl();
			$this->pageTitle = Dictionary::get('catalog.pageTitle');
		}
		parent::doAll();
	}
	
	private function uploadImage()
	{
		$slices = Config::get('imageSlices');
		$image = $this->imageService->upload($slices[catalogitems_image]);
		$item = $this->service->get($_POST['id']);
		$this->service->save(array(id=>$item->id, image=>$image->id));
		$image = new Image($image);
		echo $image->serialize();
	}
	
	protected function parseItemUrl()
	{
		if(count($this->url) == 0)
		{
			$this->getCategoryMenu();
			return;
		}
		$category = $this->categoryService->getByUrl($this->url[0], true);
		$this->url = ArrayUtil::splice($this->url, 1);
		if($this->url[0] == 'new')
		{
			$item = $this->service->create($category->id);
			$this->navigate('/catalog/' . $category->url . '/edit/' . $item->id);
			$this->output = false;
			return;
		}
		if($this->url[0] == 'delete')
		{
			$this->service->delete($this->url[1]);
			$this->output = false;
			return;
		}
		if($this->url[0] == 'edit')
		{
			$this->getCategoryMenu();
			$item = $this->service->get($this->url[1]);
			Template::add('category', $category);
			Template::add('item', new Product($item));
			$this->registerButton(new Button('save', '', Dictionary::get('save')));
			Template::add('form', true);
			return;
		}
		
		if($this->url[0] == 'publish')
		{
			$item = $this->service->get($this->url[1], true);
			$this->service->publish($item->id, 1 - $item->published);
			$this->output = false;
			return;
		}
		$this->getCategoryMenu();
		Template::add('category', $category);
		Template::add('items', $this->service->getInCategory($category->id,  - 1,  - 1, null, null, true));
		$this->registerButton(new Button('new', '/admin/catalog/' . $category->url . '/new', Dictionary::get('catalog.new')));
	}
	
	protected function parseCategoryUrl()
	{
		if(count($this->url) == 0)
		{
			$this->getCategoryMenu();
			return;
		}
		$operation = $this->url[0];
		Template::add('operation', $operation);
		if($operation == 'new')
		{
			$parent = 0;
			if(count($this->url) > 1)
				$parent = $this->url[1];
			$category = $this->categoryService->create($parent);
			$this->navigate('/catalog/categories/edit/' . $category->id);
			$this->output = false;
			return;
		}
		if($operation == 'edit')
		{
			$this->registerButton(new Button('save', '', Dictionary::get('save')));
			Template::add('category', $this->categoryService->getCategory($this->url[1], true));
			$this->getCategoryMenu();
			return;
		}
		if($operation == 'delete')
		{
			$this->categoryService->delete($this->url[1]);
			$this->output = false;
			return;
		}
		if($operation == 'publish')
		{
			$category = $this->categoryService->getCategory($this->url[1], true);
			$this->categoryService->publish($category->id, 1 - $category->published);
			return;
		}
		if($operation == 'ord')
		{
			$this->categoryService->update(array(id=>$this->url[1], ord=>$this->url[2]));
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
	
	protected function saveCategory($data)
	{
		$category = $this->categoryService->getByUrl($data[url], true);
		if($category != null && $category->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
			$this->categoryService->save($data);
	}
	
	protected function saveItem($data)
	{
		$item = $this->service->getByUrl($data[url], true);
		if($data[category] != '')
		{
			$cat = intval($data[category]);
			if($cat == 0)
			{
				FormController::addError('category', 'not.exists');
				return;
			}
			$category = $this->categoryService->get($cat);
			if( ! $category)
			{
				FormController::addError('category', 'not.exists');
				return;
			}
		}
		else
			unset($data[category]);
		if($item != null && $item->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
		{
			$advanced = array(id=>$item->advanced, count=>$data[count], articul=>addslashes($data[articul]), price2=>$data[price2]);
			$this->descriptionService->update($advanced);
			unset($data[count]);
			unset($data[articul]);
			unset($data[price2]);
			$this->service->save($data);
		}
	}
	
	public static function registerInMenu()
	{
		return new MenuItem('Каталог товаров', '', 5, array(new MenuItem('Категории товаров', '/catalog/categories'), new MenuItem('Товары', '/catalog')));
	}
	
	private function getCategoryMenu()
	{
		$categories = $this->categoryService->getNodesWithTree(array(Expression::eq('parentid', 0)), true, array('id', 'ord', 'name', 'published', 'parentid', 'url'));
		$cats = array();
		for($i = 0; $i < count($categories); $i++)
		{
			if( ! isset($cats[$categories[$i]->parentid]))
				$cats[$categories[$i]->parentid] = array($categories[$i]);
			else
				$cats[$categories[$i]->parentid][] = $categories[$i];
		}
		unset($categories);
		Template::add('categories', $cats);
	}
}

?>