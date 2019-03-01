<?php
class View_View
{
	protected $layout = null;
	
	protected $template = null;
	
	protected $partial = null;
	
	protected $viewsFolder = 'views';
	
	protected $layoutsFolder = 'layouts';
	
	protected $extension = 'phtml';
	
	private static $instance;
	
	private $extraPartials = array();
	
	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new self();
		return self::$instance;
	}
	
	public function append($name, $file)
	{
		$this->extraPartials[$name] = $file;
	}
	
	public function __get($var)
	{
		if($var == 'content')
			return $this->content();
		if($var == 'template_content')
			return $this->renderPartial($this->partial);
		if(isset($this->extraPartials[$var]))
			return $this->renderPartial($this->extraPartials[$var]);
		if(!isset($this->$var))
			return null;
		return $this->$var;
	}
	
	public function __set($var, $val)
	{
		if($var == 'content')
			return;
		$this->$var = $val;
	}
	
	public function render($file = '')
	{
		if($file != '')
			$this->layout = $file;
		if($this->layout != null)
		{
			ob_start();
			include $this->viewsFolder . DIRECTORY_SEPARATOR . $this->layoutsFolder . DIRECTORY_SEPARATOR . $this->layout . '.'. $this->extension;
			return ob_get_clean();
		}
		return $this->content();
	}
	
	public function content()
	{
		if($this->template != null)
		{
			ob_start();
			include($this->viewsFolder . DIRECTORY_SEPARATOR . $this->template . '.'. $this->extension);
			return ob_get_clean();
		}
		else
			return $this->renderPartial($this->partial);
	}
	
	private function renderPartial($file)
	{
		if(!$file)
			return '';
		ob_start();
		include($this->viewsFolder . DIRECTORY_SEPARATOR . $file . '.'. $this->extension);
		return ob_get_clean();
	}
	
	public function renderFile($file)
	{
		ob_start();
		include($this->viewsFolder . DIRECTORY_SEPARATOR . $file . '.'. $this->extension);
		return ob_get_clean();
	}
}
?>