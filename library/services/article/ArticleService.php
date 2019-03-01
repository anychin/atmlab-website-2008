<?
require_once ('services/photo/PhotoGalleryService.php');
require_once ('services/tree/TreeService.php');
require_once ('Article.php');
require_once ('link/LinkService.php');

class ArticleService extends TreeService 
{

	private $galleryService;
	
	private $linkService;
	
	public function __construct()
	{
		parent::__construct('articles');
		$this->galleryService = DAOService::instance('PhotoGalleryService');
		$this->linkService = DAOService::instance('LinkService');
	}
	
	protected function deleteFilter($id, $criteria, $table)
	{
		$this->linkService->deleteArticle($id);
		parent::deleteFilter($id, $criteria, $table);
	}

	protected function createFilter($values)
	{
		$values = parent::createFilter($values);
		$gallery = $this->galleryService->create();
		$values[otherurl] = '';
		$values[name] = '';
		$values[title] = '';
		$values[descr] = '';
		$values[text] = '';
		$values[gallery] = $gallery->id;
		return $values;
	}
	
	public function save($values)
	{
		if ($values['def'] == 1)
			$this->update(array(def => 0));
		return parent::save($values);
	}

	public function getArticle($id, $admin = false, $fields = array())
	{
		return parent::getNode($id, $admin, $fields);
	}

	public function getDefault()
	{
		return parent::get(0, array(Expression::eq('temp', 0), Expression::eq('published', 1), Expression::eq('def', 1)));
	}

	public function getRootArticles($admin = false, $fields = array())
	{
		return parent::getRootNodes($admin, $fields);
	}

	public function getSubArticles($parent, $admin = false, $fields = array())
	{
		return parent::getSubNodes($parent, $admin, $fields);
	}
}
?>