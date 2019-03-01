<?php
class Db_Tree_DAO extends Dao_Dao 
{
	public function save($values)
	{
		$values['temp'] = 0;
		return $this->update($values);
	}
	
	public function getByUrl($url, $admin = false)
	{
		$expressions = array(Dao_Exp::eq('url', $url));
		if (!$admin)
		{
			$expressions[] = Dao_Exp::eq('temp', 0);
			$expressions[] = Dao_Exp::eq('published', 1);
		}
		return parent::get(0, $expressions);
	}
	
	public function create($parent)
	{
		$node = $this->insert(array('parentid' => $parent));
		$level = 0;
		$code = $this->buildCode($node->id);
		if($parent != 0)
		{
			$parentNode = $this->get($parent);
			$parentNode = $this->update(array('id'=>$parentNode->id, 'childindex'=>$parentNode->childindex + 1));
			$code = $parentNode->code.'.'.$this->buildCode($parentNode->childindex);
			$level = $parentNode->level + 1;
		}
		return $this->update(array('id'=>$node->id, 'code'=>$code, 'level'=>$level));
	}
	
	private function buildCode($index){
		$l = strlen($index.'');
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
	
	public function oldDelete($id, $criteria = "")
	{
		parent::delete($id, $criteria);
	}
	
	public function getNode($id, $admin = false, $fields = array())
	{
		$criteria = array();
		if (!$admin)
			$criteria[] = array(Dao_Exp::eq('temp', 0), Dao_Exp::eq('published', 1));
		return parent::get($id, $criteria, $fields);
	}
	
	public function getParentByCode($code)
	{
		$atoms = explode('.', $code);
		return $this->get(0, array(Dao_Exp::eq('code', $atoms[0])));
	}
	
	public function getNodesWithTree($criterias = array(), $admin = false, $fields = array())
	{
		$expressions = array(Dao_Exp::eq('temp', 0));
		if (!$admin)
			$expressions[] = Dao_Exp::eq('published', 1);
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
		$nodes = $this->getList($expressions, -1, -1, array(Dao_Order::asc('ord')), $fields);
		$dis = array();
		$expressions = array(Dao_Exp::eq('temp', 0));
		if (!$admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		for($i = 0; $i < count($nodes); $i++)
			$dis[] = Dao_Exp::likeLeft('code', $nodes[$i]->code.'.');
		$expressions[] = Dao_Exp::disunction($dis);
		$sub = $this->getList($expressions, -1, -1, array(Dao_Order::asc('level'), Dao_Order::asc('parentid'), Dao_Order::asc('ord')), $fields);
		if($sub == null)
			$sub = array();
		if($nodes == null)
			$nodes = array();
		return array_merge($nodes, $sub);
	}
	
	public function getTree($code, $criterias = array(), $admin = false, $fields = array())
	{
		$expressions = array(Dao_Exp::eq('temp', 0));
		if (!$admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		$expressions[] = Dao_Exp::likeLeft('code', $code.'.');
		for($i = 0; $i < count($criterias); $i++)
			$expressions[] = $criterias[$i];
		return $this->getList($expressions, -1, -1, array(Dao_Order::asc('level'), Dao_Order::asc('parentid'), Dao_Order::asc('ord')), $fields);
	}
	
	public function getRootNodes($admin = false, $fields = array())
	{
		return $this->getSubNodes(0, $admin, $fields);
	}
	
	public function getSubNodes($parent, $admin = false, $fields = array())
	{
		$expressions = array(Dao_Exp::eq('temp', 0), Dao_Exp::eq('parentid', $parent));
		if (!$admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		return $this->getList($expressions, -1, -1, array(Dao_Order::asc('ord')), $fields);
	}
	
	public function publish($id, $val)
	{
		$this->update(array('published' => $val , 'id' => $id));
	}
	
	public function getTreeByNode($node, $maxLevel = -1, $fields = null, $admin = false)
	{
		$parts = explode('.', $node->code);
		$expressions = array(Dao_Exp::eq('temp', 0));
		if(!$admin)
			$expressions[] = Dao_Exp::eq('published', 1);
		$c = $parts[0];
		$n = count($parts);
		for($i = 0; $i < $n; $i++){
			$expressions1[] = Dao_Exp::conjunction(array(Dao_Exp::likeLeft('code', $c.'.'), Dao_Exp::lt('level', $i + 2)));
			$expressions1[] = Dao_Exp::eq('code', $c);
			$c .= '.';
			if($i < $n - 1)
				$c .= $parts[$i + 1];
		}
		$expressions[] = Dao_Exp::disunction($expressions1);
		if($maxLevel > -1)
			$expressions[] = Dao_Exp::lt('level', $maxLevel + 1);
		$parent = array(Dao_Exp::eq('parentid', 0), Dao_Exp::eq('temp', 0));
		if(!$admin)
			$parent[] = Dao_Exp::eq('published', 1);
		return $this->getList(Dao_Exp::disunction(array(Dao_Exp::conjunction($expressions), Dao_Exp::conjunction($parent))), -1, -1, array(Dao_Order::asc('code')), $fields);
	}
	
	
	public function getTreeByNodeWithCriteria($node, $crs, $maxLevel = -1, $fields = null)
	{
		$parts = explode('.', $node->code);
		$expressions = array();
		$c = $parts[0];
		for($i = 0; $i < count($parts); $i++){
			$expressions1[] = Dao_Exp::conjunction(array(Dao_Exp::likeLeft('code', $c.'.'), Dao_Exp::lt('level', $i + 2)));
			$expressions1[] = Dao_Exp::eq('code', $c);
			$c .= '.'.$parts[$i + 1];
		}
		$expressions[] = Dao_Exp::disunction($expressions1);
		if($maxLevel > -1)
			$expressions[] = Dao_Exp::lt('level', $maxLevel + 1);
		$resultCr = array(Dao_Exp::disunction(array(Dao_Exp::conjunction($expressions), Dao_Exp::eq('parentid', 0))));
		for($i = 0; $i < count($crs); $i++)
			$resultCr[] = $crs[$i];
		$resultCr[] = Dao_Exp::eq('temp', 0);
		$resultCr[] = Dao_Exp::eq('published', 1);
		return $this->getList($resultCr, -1, -1, array(Dao_Order::asc('code')), $fields);
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
			$expressions[] = Dao_Exp::eq('code', $code);
		}
		if(count($expressions) > 0)
			$result = $this->getList(Dao_Exp::disunction($expressions), -1, -1, array(Dao_Order::asc('level')), $fields);
		$result[] = $node;
		return $result;
	}
}

?>
