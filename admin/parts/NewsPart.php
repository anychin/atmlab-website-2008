<?
require_once ('news/NewsService.php');
require_once ('image/ImageService.php');
require_once ("PhotoUploadPart.php");

class NewsPart extends PhotoUploadPart 
{
	private $service;
	
	private $categoryService;
	
	private $itemsPerPage = 20;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->service = DAOService::instance('NewsService');
		$this->categoryService = DAOService::instance('NewsCategoryService');
		$this->slices = array('image'=>'news_image');
	}
	
	protected function doAll()
	{
		$this->title = Dictionary::get('news.title');
		if($this->url[0] == 'categories')
			$this->parseCategoryUrl();
		else
			$this->parseItemUrl();
		parent::doAll();
	}
	
	private function parseItemUrl()
	{
		if(count($this->url) == 0)
			$category = $this->categoryService->getByUrl();
		else
			$category = $this->categoryService->getByUrl($this->url[0]);
		Template::add('category', $category);
		if(count($this->url) < 2 || $this->url[1] == 'page')
		{
			$page = 0;
			if($this->url[1] == 'page')
				$page = intval($this->url[2]) - 1;
			Template::add('pages', ceil($this->service->count($category->id, true) / $this->itemsPerPage));
			if($page > Template::get('pages') - 1)
				$page = 0;
			Template::add('page', $page + 1);
			Template::add('categories', $this->categoryService->getCategoriesList());
			Template::add('list', true);
			Template::add('news', $this->service->getInCategory($category->id, $page, $this->itemsPerPage, array('id', 'title', 'date', 'published'), true));
			$this->registerButton(new Button('new', '/admin/news/' . $category->url . '/new', Dictionary::get('news.new')));
			return;
		}
		if($this->url[1] == 'new')
		{
			$this->output = false;
			$news = $this->service->create($category->id);
			Part::navigate('/news/' . $category->url . '/edit/' . $news->id);
			return;
		}
		if($this->url[1] == 'edit')
		{
			Template::add('form', true);
			Template::add('news', $this->service->get($this->url[2]));
			Template::add('categories', $this->categoryService->getCategoriesList());
			$this->registerButton(new Button('save', '', Dictionary::get('news.save')));
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
	
	private function parseCategoryUrl()
	{
		$this->pageTitle = Dictionary::get('news.categories.pageTitle');
		Template::add('categoriespage', true);
		Template::add('categories', $this->categoryService->getCategoriesList());
		if(count($this->url) == 1)
			return;
		if($this->url[1] == 'edit')
		{
			Template::add('category', $this->categoryService->get($this->url[2]));
			$this->registerButton(new Button('save', '', Dictionary::get('news.categories.save')));
			return;
		}
		if($this->url[1] == 'new')
		{
			$category = $this->categoryService->create();
			$this->output = false;
			Part::navigate('/news/categories/edit/' . $category->id);
			return;
		}
		if($this->url[1] == 'delete')
		{
			$category = $this->categoryService->delete($this->url[2]);
			$this->output = false;
			return;
		}
		if($this->url[2] == 'publish')
		{
			$this->service->publish($this->url[3]);
			$this->output = false;
			return;
		}
	}
	
	public function formMethod()
	{
		$data = parent::formMethod();
		if(count($this->url) == 0)
			$this->saveNews($data);
		else
			$this->saveCategory($data);
		FormController::sendData();
	}
	
	protected function saveCategory($data)
	{
		$cat = $this->categoryService->getByUrl($data[url]);
		if($cat != null && $cat->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
			$this->categoryService->save($data);
	}
	
	protected function saveNews($data)
	{
		$this->service->update($data);
	}
	
	public static function registerInMenu()
	{
		return new MenuItem('Новости сайта', null, 1, array(new MenuItem('Новости', '/news'), new MenuItem('Категории новостей', '/news/categories')));
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