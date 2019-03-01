<?
class Part_Part extends Part_BasePart
{	
	protected $articleDao;
	
	public function __construct()
	{
		parent::__construct();
		$this->layout = 'site';
		$this->articleDao = Db_Article_DAO::instance();
	}
	
	public function before()
	{
		parent::before();
		$this->resolveSite();
		$this->view->presets = Db_Presets_DAO::instance()->getPresets();
		$this->view->path = array();
	}
	
	public function after(){
		$this->title = $this->site->title;
		$this->keywords =$this->site->metakeys;
		$this->description = $this->site->descr;
		parent::after();
	}
}
?>