<?php
class PresetsService extends DAOService
{
	public function __construct()
	{
		parent::__construct("common");
	}
	
	public function getPresets()
	{
		return $this->get(1);
	}
}
?>