<?php
class TreeService extends DAOService 
{
	public function save($values)
	{
		$values['temp'] = 0;
		return $this->update($values);
	}
	
	public function getByUrl($url, $admin = false)
	{
		$expressions = array(Expression::eq('url', $url));
		if (!$admin)
		{
			$expressions[] = Expression::eq('temp', 0);
			$expressions[] = Expression::eq('published', 1);
		}
		return parent::get(0, $expressions);
	}
	
	public function create($parent)
	{
		$node = $this->insert(array(parentid => $parent));
		$level = 0;
		$code = $this->buildCode($node->id);
		if($parent != 0)
		{
			$parentNode = $this->get($parent);
			$parentNode = $this->update(array(id=>$parentNode->id, childindex=>$parentNode->childindex + 1));
			$code = $parentNode->code.'.'.$this->buildCode($parentNode->childindex);
			$level = $parentNode->level + 1;
		}
		return $this->update(array(id=>$node->id, code=>$code, level=>$level));
	}
	
	private function buildCode($index){
		$l = mb_strlen($index.'');
		$res = $index;
		for($i = $l; $i < 4; $i++)
			$res = '0'.$res;
		return $res;
	}
	
	
	public function delete($id)
	{
		$sub = $this->getSubNodes($id, true);
		if($sub != null)
			for($i = 0; $i < count($sub); $i ++)
				$this->delete($sub[$i]->id);
		parent::delete($id);
	}
	
	public function getNode($id, $admin = false, $fields = array())
	{
		if (!$admin)
			$criteria[] = array(Expression::eq('temp', 0), Expression::eq('published', 1));
		return parent::get($id, $criteria, $fields);
	}
	
	public function getParentByCode($code)
	{
		$atoms = explode('.', $code);
		return $this->get(0, array(Expression::eq('code', $atoms[0])));
	}
	
	public function getNodesWithTree($criterias = array(), $admin = false, $fields = array())
	{
		$expressions = array(Expression::eq('temp', 0));
		if (!$admin)
			$expressions[] = Expression::eq('published', 1);
		for($i = 0; $i < count($criterias); $i++)
			$expressions[] = $criterias[$i];
		if(count($fields) > 0){
			$find = false;
			for($i = 0; $i < count($fields); $i++)
				if($fields[$i] == 'code'){
					$find = true;
					break;
				}
			if(!$find)
				$fields[] = 'code';
		}
		$nodes = $this->getList($expressions, -1, -1, array(Order::asc('ord')), $fields);
		$dis = array();
		$expressions = array(Expression::eq('temp', 0));
		if (!$admin)
			$expressions[] = Expression::eq('published', 1);
		for($i = 0; $i < count($nodes); $i++)
			$dis[] = Expression::likeLeft('code', $nodes[$i]->code.'.');
		$expressions[] = Expression::disunction($dis);
		$sub = $this->getList($expressions, -1, -1, array(Order::asc('level'), Order::asc('parentid'), Order::asc('ord')), $fields);
		if($sub == null)
			$sub = array();
		if($nodes == null)
			$nodes = array();
		return array_merge($nodes, $sub);
	}
	
	public function getTree($code, $criterias = array(), $admin = false, $fields = array())
	{
		$expressions = array(Expression::eq('temp', 0));
		if (!$admin)
			$expressions[] = Expression::eq('published', 1);
		$expressions[] = Expression::likeLeft('code', $code.'.');
		for($i = 0; $i < count($criterias); $i++)
			$expressions[] = $criterias[$i];
		return $this->getList($expressions, -1, -1, array(Order::asc('level'), Order::asc('parentid'), Order::asc('ord')), $fields);
	}
	
	public function getRootNodes($admin = false, $fields = array())
	{
		return $this->getSubNodes(0, $admin, $fields);
	}
	
	public function getSubNodes($parent, $admin = false, $fields = array())
	{
		$expressions = array(Expression::eq('temp', 0), Expression::eq('parentid', $parent));
		if (!$admin)
			$expressions[] = Expression::eq('published', 1);
		return $this->getList($expressions, -1, -1, array(Order::asc('ord')), $fields);
	}
	
	public function publish($id, $val)
	{
		$this->update(array(published => $val , id => $id));
	}
	
	public function getTreeByNode($node, $maxLevel = -1, $fields = null, $admin = false)
	{
		$parts = explode('.', $node->code);
		$expressions = array(Expression::eq('temp', 0));
		if(!$admin)
			$expressions[] = Expression::eq('published', 1);
		$c = $parts[0];
		for($i = 0; $i < count($parts); $i++){
			$expressions1[] = Expression::conjunction(array(Expression::likeLeft('code', $c.'.'), Expression::lt('level', $i + 2)));
			$expressions1[] = Expression::eq('code', $c);
			$c .= '.'.$parts[$i + 1];
		}
		$expressions[] = Expression::disunction($expressions1);
		if($maxLevel > -1)
			$expressions[] = Expression::lt('level', $maxLevel + 1);
		return $this->getList(Expression::disunction(array(Expression::conjunction($expressions), Expression::eq('parentid', 0))), -1, -1, array(Order::asc('code')), $fields);
	}
	
	public function getPath($node, $fields = null)
	{
		$result = array();
		$paths = explode('.', $node->code);
		$code = $paths[0];
		$expressions = array();
		for($i = 0; $i < count($paths) - 1; $i++)
		{
			if($i > 0)
				$code .= '.'.$paths[$i];
			$expressions[] = Expression::eq('code', $code);
		}
		if(count($expressions) > 0)
			$result = $this->getList(Expression::disunction($expressions), -1, -1, array(Order::asc('level')), $fields);
		$result[] = $node;
		return $result;
	}
}

?>
