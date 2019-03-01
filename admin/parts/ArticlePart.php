<?
require_once ('services/article/ArticleService.php');
require_once ('services/article/link/LinkService.php');
require_once ("PhotoUploadPart.php");

class ArticlePart extends PhotoUploadPart 
{
	private $service;
	
	private $linkService;
	
	function __construct($name)
	{
		parent::__construct($name);
		$this->service = DAOService::instance('ArticleService');
		$this->linkService = DAOService::instance('LinkService');
		$this->pageTitle = Dictionary::get('article.pageTitle');
		$this->slices = array('image'=>'news_image', 'photo'=>'photo_image');
	}

	public function doAll()
	{
		$this->title = Dictionary::get('article.title');
		$this->handle();
		parent::doAll();
	}
	
	function handle()
	{
		if (count($this->url) == 0)
		{
			$this->getArticleMenu();
			return;
		}
		$operation = $this->url[0];
		Template::add('operation', $operation);
		if ($operation == 'new')
		{
			$parent = 0;
			if(count($this->url) > 1)
				$parent = $this->url[1];
			$article = $this->service->create($parent);
			$this->navigate('/article/edit/' . $article->id);
			$this->output = false;
			return;
		}
		if ($operation == 'edit')
		{
			$article = $this->service->getByUrl($this->url[1], true);
			if($article == null)
				$article = $this->service->get($this->url[1]);
			$this->registerButton(new Button('save', '', Dictionary::get('save')));
			Template::add('article', $article);
			$links = $this->linkService->getLinks($article->id);
			$onLinks = $this->linkService->getOnLinks($article->id);
			Template::add('links', $links);
			Template::add('onlinks', $onLinks);
			$ids = array();
			for($i = 0; $i < count($links); $i++)
				$ids[] = $links[$i]->link;
			for($i = 0; $i < count($onLinks); $i++)
				$ids[] = $onLinks[$i]->article;
			Template::add('linkArticles', $this->service->getList(Expression::in('id', $ids), -1, -1, null, array('id', 'name')));
			$this->getArticleMenu();
			return;
		}
		if ($operation == 'delete')
		{
			$this->service->delete($this->url[1]);
			$this->output = false;
			return;
		}
		if ($operation == 'publish')
		{
			$article = $this->service->getArticle($this->url[1], true);
			$this->service->publish($article->id, 1 - $article->published);
			return;
		}
		if ($operation == 'ord')
		{
			$this->service->update(array(id=>$this->url[1], ord=>$this->url[2]));
			$this->output = false;
			return;
		}
	}

	public function formMethod()
	{
		$data = parent::formMethod();
		$articleByUrl = $this->service->getByUrl($data[url], true);
		if ($articleByUrl != null && $articleByUrl->id != $data[id])
			FormController::addError('url', 'not.unique');
		else
			$this->service->save($data);
		FormController::sendData();
	}
	
	public static  function registerInMenu()
	{
		return new MenuItem(Dictionary::get('article.menu'), '/article');
	}
	
	private function getArticleMenu()
	{
		$articles = $this->service->getNodesWithTree(array(Expression::eq('parentid', 0)), true, array('id', 'ord', 'name', 'url', 'published', 'parentid'));
		$arts = array();
		for($i = 0; $i < count($articles); $i++){
			if(!isset($arts[$articles[$i]->parentid]))
				$arts[$articles[$i]->parentid] = array($articles[$i]);
			else
				$arts[$articles[$i]->parentid][] = $articles[$i];
		}
		unset($articles);
		Template::add('articles', $arts);
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