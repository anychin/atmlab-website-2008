<?
require_once ('library/services/presets/PresetsService.php');
require_once ('library/services/article/ArticleService.php');
require_once ('library/services/news/NewsService.php');
require_once ('library/services/news/NewsCategoryService.php');
require_once ('classes/services/projects/ProjectsService.php');
require_once ('classes/services/projects/ProjectsCategoryService.php');

class Part extends BasePart
{
	protected $articleService;
	
	protected $presetsService;
	
	protected $projectsService;
	
	protected $projectsCategoryService;
	
	protected $newsService;
	
	protected $newsCategoryService;
	
	protected $presets;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->articleService = DAOService::instance('ArticleService');
		$this->presetsService = DAOService::instance('PresetsService');
		$this->projectsService = DAOService::instance('ProjectsService');
		$this->projectsCategoryService = DAOService::instance('ProjectsCategoryService');
		$this->newsService = DAOService::instance('NewsService');
		$this->newsCategoryService = DAOService::instance('NewsCategoryService');
		$this->presets = $this->presetsService->getPresets();
	}
	
	protected function doAll()
	{
		if((count($this->url) == 0 && $this->partName == 'articles') || $this->partName == 'projects')
		{
			$realProjects = array();
			$categories = $this->projectsCategoryService->getList(array(Expression::eq('temp', 0)), -1, -1, array(Order::asc('ord')));
			Template::add('projectCategories', $categories);
			$baseCriteria = Expression::query('p.temp = 0 and p.published = 1 and ir.image = p.image');
			$fields = array('p.id as id', 'p.gallery gallery', 'ir.name as name', 'ir.id as image', 'ir.width as width', 'ir.height as height', 'p.description description', 'p.url url', 'p.site site', 'p.name title', 'p.date date', 'p.shortdescription tags', 'ir.image original');
			$tables = 'projects p, imageresize ir';
			$projects = array();
			$ids = array();
			for($i = 0; $i < count($categories); $i++)
			{
				$list = $this->projectsService->getList(array(Expression::eq('p.category', $categories[$i]->id), $baseCriteria), -1, -1, array(Order::desc('p.id')), $fields, $tables);
				$prs = array();
				for($j = 0; $j < count($list); $j++)
				{
					$pr = $list[$j]->id;
					if(!isset($realProjects[$pr]))
					{
						$realProjects[$pr] = $list[$j];
						$ids[] = $list[$j]->gallery;
					}
					$r = $list[$j]->name;
					if(!isset($prs[$pr]))
						$prs[$pr] = array();
					$prs[$pr][$r] = $list[$j];
				}
				foreach ($prs as $pr)
					$projects[$categories[$i]->id][] = $pr;
			}
			$galleries = array();
			Template::add('projects', $projects);
			$gallery = $this->projectsService->getList(Expression::query(Expression::in('p.gallery', $ids) . ' and ir.image = p.image'), -1, -1, array(Order::desc('p.id')), array('p.gallery as gallery', 'ir.name as name', 'ir.height height', 'ir.width width', 'ir.id as image', 'ir.image id'), 'photo p, imageresize ir');
			for($i = 0; $i < count($gallery); $i++)
			{
				if(!isset($galleries[$gallery[$i]->gallery]))
					$galleries[$gallery[$i]->gallery] = array();
				$g = &$galleries[$gallery[$i]->gallery];
				if(!isset($g[$gallery[$i]->id]))
					$g[$gallery[$i]->id] = array();
				if($gallery[$i]->name == 'small' || $gallery[$i]->name == 'big' || $gallery[$i]->name == 'mostbig')
					$g[$gallery[$i]->id][$gallery[$i]->name] = array('id'=>$gallery[$i]->image, 'height'=>$gallery[$i]->height, 'width'=>$gallery[$i]->width);
			}
			Template::add('galleries', $galleries);
			Template::add('realProjects', $realProjects);
		}
		Template::add('more', $this->articleService->getList(array(Expression::eq('temp', 0), Expression::eq('published', 1), Expression::eq('flag1', 1)),-1, -1, array(Order::asc('ord')), array('url', 'name')));
		if($this->partName != 'news')
		{
			Template::add('newsCount', $this->newsService->count(0));
			Template::add('mainNews', $this->newsService->getList(array(Expression::eq('temp', 0), Expression::eq('published', 1)), 0, 4, array(Order::desc('date')), array('id', 'shorttext', 'title')));
		}
		Template::add('contacts', $this->articleService->getByUrl("contacts"));
		Template::add('vacancy', $this->articleService->getByUrl("vacancy"));
		Template::add('company', $this->articleService->getByUrl("company"));
		Template::add('target', $this->articleService->getByUrl("target"));
		parent::doAll();
	}

}
?>