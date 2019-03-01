<?php
class Image_Resizer
{
	private $uploader = null;
	
	private $imagePath;
	
	public function __construct()
	{
		$this->uploader = new Utils_Image();
	}
	
	public function resize($format, $name, $type = "jpg", $file = "flashfile", $folder="images/uploaded/")
	{
		if(file_exists($file))
			$this->uploader->init($file);
		else
			$this->uploader->init($_FILES[$file]);
		if(!$this->uploader->uploaded)
			return null;
		if($name != "")
			$this->uploader->file_new_name_body = $name;
		if($format->format != Image_Format::ORIGINAL)
			$this->resolveImageType($type);
		if($format->bg)
			$this->uploader->image_background_color = $format->bg;
		$this->uploader->file_overwrite = true;
		$this->uploader->jpeg_quality = 100;
		$this->imagePath = $folder;
		$this->uploader->image_greyscale = $format->grey;
		$this->uploader->blur = $format->blur;
		if($format->format == Image_Format::INSCRIBE)
			$this->inscribe($format);
		if($format->format == Image_Format::DESCRIBE)
			$this->describe($format);
		if($format->format == Image_Format::DESCRIBE_W)
			$this->describeW($format);
		if($format->format == Image_Format::DESCRIBE_H)
			$this->describeH($format);
		$res = true;
		if(!$format->stream)
			$res = $this->uploader->process($this->imagePath);
		else
			$res = $this->uploader->process();
		if($res !== false)
			return $this->getInfo();
		return null;
		
	}
	
	private function inscribe($format)
	{
		$this->uploader->image_resize = true;
		$this->uploader->image_x = $format->width;
		$this->uploader->image_y = $format->height;
		$this->uploader->image_ratio = true;
		if($format->bg)
			$this->uploader->image_ratio_fill = true;
		if(!$format->enlarge)
			$this->uploader->image_ratio_no_zoom_in = true;
	}
	
	private function describe($format)
	{
		
		$xScale = $this->uploader->image_src_x / $format->width;
		$yScale = $this->uploader->image_src_y / $format->height;
		if($xScale > $yScale)
			$this->describeH($format);
		else
			$this->describeW($format);
	}
	
	private function describeW($format)
	{
		$this->describeInternal($format, "w");
	}
	
	private function describeH($format)
	{
		$this->describeInternal($format, "h");
	}
	
	private function describeInternal($format, $side)
	{
		$this->uploader->image_resize = true;
		$this->uploader->image_x = $format->width;
		$this->uploader->image_y = $format->height;
		if(!$format->crop){
			if($side == "w")
				$this->uploader->image_ratio_y = true;
			else
				$this->uploader->image_ratio_x = true;
		}
		else
			$this->uploader->image_ratio_crop = true;
		if($format->bg)
			$this->uploader->image_ratio_fill = true;
		if(!$format->enlarge)
			$this->uploader->image_ratio_no_zoom_in = true;
	}
	
	private function resolveImageType($type)
	{
		$this->uploader->image_convert = $type;
	}
	
	public function clean()
	{
		$this->uploader->clean();
	}
	
	private function getInfo()
	{
		$info = getimagesize($this->uploader->file_dst_pathname);
		return array('path'=>$this->uploader->file_dst_pathname, 'width'=>$this->uploader->image_dst_x, 'height'=>$this->uploader->image_dst_y, 'mime'=>$info['mime'], 'size'=>filesize($this->uploader->file_dst_pathname));
	}
}
?>