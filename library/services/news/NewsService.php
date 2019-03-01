<?
require_once ('NewsCategoryService.php');
require_once ('News.php');

class NewsService extends DAOService
{
	
	private $categoryService;
	
	public function __construct()
	{
		parent::__construct('news');
		$this->categoryService = DAOService::instance('NewsCategoryService');
	}
	
	public function create($category)
	{
		return parent::insert(array(text=>'', shorttext=>'', title=>'', pagetitle=>'', keywords=>'', description=>'', category=>$category, date=>date('Y-m-d')));
	}
	
	public function save($data)
	{
		$this->update($data);
	}
	
	public function getInCategory($category, $page = -1, $itemsPerPage = -1, $fields = array(), $admin = false)
	{
		$cat = intval($category);
		if($cat == 0)
		{
			$category = $this->categoryService->getByUrl($category);
			$category = $category->id;
		}
		$expressions = array(Expression::eq('temp', 0), Expression::eq('category', $category));
		if( ! $admin)
			$expressions[] = Expression::eq('published', 1);
		return $this->getList($expressions, $page, $itemsPerPage, array(Order::desc('date')), $fields);
	}
	
	public function update($values)
	{
		$values[temp] = 0;
		parent::update($values);
	}
	
	public function count($category, $admin = false)
	{
		$cat = intval($category);
		if($cat == 0)
		{
			$category = $this->categoryService->getByUrl('');
			$cat = $category->id;
		}
		$expressions = array(Expression::eq('temp', 0), Expression::eq('category', $cat));
		if( ! $admin)
			$expressions[] = Expression::eq('published', 1);
		return parent::count($expressions);
	}
	
	public function delete($id)
	{
		parent::delete($id);
	}
	
	public function publish($id)
	{
		$news = $this->get($id);
		return $this->update(array(id=>$id, published=>(1 - $news->published)));
	}
}
?>