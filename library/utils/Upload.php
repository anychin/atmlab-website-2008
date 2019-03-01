<?php
class Upload
{
	public $file_src_name = '';
	public $file_src_name_body = '';
	public $file_src_name_ext = '';
	public $file_src_mime = '';
	public $file_src_size = '';
	public $file_src_pathname = '';
	public $file_src_temp = '';
	public $file_dst_path = '';
	public $file_dst_name = '';
	public $file_dst_name_body = '';
	public $file_dst_name_ext = '';
	public $file_dst_pathname = '';
	public $image_src_x = null;
	public $image_src_y = null;
	public $image_src_bits = null;
	public $image_src_pixels = null;
	public $image_src_type = null;
	public $image_dst_x = 0;
	public $image_dst_y = 0;
	public $image_supported;
	public $file_is_image = false;
	public $uploaded = true;
	public $no_upload_check = false;
	public $processed = true;
	public $file_new_name_body = '';
	public $file_name_body_add = '';
	public $file_new_name_ext = '';
	public $file_safe_name = true;
	public $mime_check = true;
	public $mime_magic_check = false;
	public $dir_auto_chmod = true;
	public $dir_chmod = 0777;
	public $file_max_size;
	public $image_resize = false;
	public $image_convert = '';
	public $image_x = 150;
	public $image_y = 150;
	public $image_ratio = false;
	public $image_ratio_crop = false;
	public $image_ratio_fill = false;
	public $image_ratio_pixels = false;
	public $image_ratio_no_zoom_in = false;
	public $image_ratio_no_zoom_out = false;
	public $image_ratio_x = false;
	public $image_ratio_y = false;
	public $image_max_width = null;
	public $image_max_height = null;
	public $image_max_pixels = null;
	public $image_max_ratio = null;
	public $image_min_width = null;
	public $image_min_height = null;
	public $image_min_pixels = null;
	public $image_min_ratio = null;
	public $jpeg_quality = 85;
	public $jpeg_size = null;
	public $preserve_transparency = false;
	public $image_is_transparent = false;
	public $image_transparent_color = null;
	public $image_background_color = null;
	public $image_default_color = '#ffffff';
	public $image_is_palette = false;
	public $image_brightness = null;
	public $image_contrast = null;
	public $image_threshold = null;
	public $image_tint_color = null;
	public $image_overlay_color = null;
	public $image_overlay_percent = null;
	public $image_negative = false;
	public $image_greyscale = false;
	public $image_text = null;
	public $image_text_direction = null;
	public $image_text_color = '#ffffff';
	public $image_text_percent = 100;
	public $image_text_background = null;
	public $image_text_background_percent = 100;
	public $image_text_font = 5;
	public $image_text_position = null;
	public $image_text_x = null;
	public $image_text_y = null;
	public $image_text_padding = 0;
	public $image_text_padding_x = null;
	public $image_text_padding_y = null;
	public $image_text_alignment = 'C';
	public $image_text_line_spacing = 0;
	public $image_reflection_height = null;
	public $image_reflection_space = 2;
	public $image_reflection_color = '#ffffff';
	public $image_reflection_opacity = 60;
	public $image_flip = null;
	public $image_rotate = null;
	public $image_crop = null;
	public $blur = false;
	public $image_bevel = null;
	public $image_bevel_color1 = '#ffffff';
	public $image_bevel_color2 = '#000000';
	public $image_border = null;
	public $image_border_color = '#ffffff';
	public $image_frame = null;
	public $image_frame_colors = '#ffffff #999999 #666666 #000000';
	public $image_watermark = null;
	public $image_watermark_position = null;
	public $image_watermark_x = null;
	public $image_watermark_y = null;
	
	public function __construct()
	{
		$val = trim(ini_get('upload_max_filesize'));
		$last = strtolower($val{strlen($val) - 1});
		if($last == 'g' || $last == 'm' || $last == 'k')
			$val *= 1024 * 1024;
		$this->file_max_size = $val;
		
		$this->image_supported = array('image/gif'=>'gif', 'image/jpg'=>'jpg', 'image/jpeg'=>'jpg', 'image/pjpeg'=>'jpg', 'image/png'=>'png', 'image/x-png'=>'png', 'image/bmp'=>'bmp', 'image/x-ms-bmp'=>'bmp', 'image/x-windows-bmp'=>'bmp');
	}
	
	public function init($file)
	{
		$this->file_src_name = '';
		$this->file_src_name_body = '';
		$this->file_src_name_ext = '';
		$this->file_src_mime = '';
		$this->file_src_size = '';
		$this->file_src_pathname = '';
		$this->file_src_temp = '';
		$this->file_dst_path = '';
		$this->file_dst_name = '';
		$this->file_dst_name_body = '';
		$this->file_dst_name_ext = '';
		$this->file_dst_pathname = '';
		$this->image_src_x = null;
		$this->image_src_y = null;
		$this->image_src_bits = null;
		$this->image_src_pixels = null;
		$this->image_src_type = null;
		$this->image_dst_x = 0;
		$this->image_dst_y = 0;
		$this->file_is_image = false;
		$this->uploaded = true;
		$this->processed = true;
		$this->file_new_name_body = '';
		$this->file_name_body_add = '';
		$this->file_new_name_ext = '';
		$this->file_safe_name = true;
		$this->mime_check = true;
		$this->mime_magic_check = false;
		$this->dir_auto_chmod = true;
		$this->image_resize = false;
		$this->image_convert = '';
		$this->image_x = 150;
		$this->image_y = 150;
		$this->image_ratio = false;
		$this->image_ratio_crop = false;
		$this->image_ratio_fill = false;
		$this->image_ratio_pixels = false;
		$this->image_ratio_no_zoom_in = false;
		$this->image_ratio_no_zoom_out = false;
		$this->image_ratio_x = false;
		$this->image_ratio_y = false;
		$this->image_max_width = null;
		$this->image_max_height = null;
		$this->image_max_pixels = null;
		$this->image_max_ratio = null;
		$this->image_min_width = null;
		$this->image_min_height = null;
		$this->image_min_pixels = null;
		$this->image_min_ratio = null;
		$this->jpeg_size = null;
		$this->preserve_transparency = false;
		$this->image_is_transparent = false;
		$this->image_transparent_color = null;
		$this->image_background_color = null;
		$this->image_default_color = '#ffffff';
		$this->image_is_palette = false;
		$this->image_brightness = null;
		$this->image_contrast = null;
		$this->image_threshold = null;
		$this->image_tint_color = null;
		$this->image_overlay_color = null;
		$this->image_overlay_percent = null;
		$this->image_negative = false;
		$this->image_greyscale = false;
		$this->image_text = null;
		$this->image_text_direction = null;
		$this->image_text_color = '#ffffff';
		$this->image_text_percent = 100;
		$this->image_text_background = null;
		$this->image_text_background_percent = 100;
		$this->image_text_font = 5;
		$this->image_text_position = null;
		$this->image_text_x = null;
		$this->image_text_y = null;
		$this->image_text_padding = 0;
		$this->image_text_padding_x = null;
		$this->image_text_padding_y = null;
		$this->image_text_alignment = 'C';
		$this->image_text_line_spacing = 0;
		$this->image_reflection_height = null;
		$this->image_reflection_space = 2;
		$this->image_reflection_color = '#ffffff';
		$this->image_reflection_opacity = 60;
		$this->image_flip = null;
		$this->image_rotate = null;
		$this->image_crop = null;
		$this->blur = false;
		$this->image_bevel = null;
		$this->image_bevel_color1 = '#ffffff';
		$this->image_bevel_color2 = '#000000';
		$this->image_border = null;
		$this->image_border_color = '#ffffff';
		$this->image_frame = null;
		$this->image_frame_colors = '#ffffff #999999 #666666 #000000';
		$this->image_watermark = null;
		$this->image_watermark_position = null;
		$this->image_watermark_x = null;
		$this->image_watermark_y = null;
		
		if( ! $file || empty($file))
		{
			$this->uploaded = false;
			return;
		}
		if( ! is_array($file))
		{
			$this->no_upload_check = true;
			if( ! file_exists($file) ||  ! is_readable($file))
			{
				$this->uploaded = false;
				return;
			}
			$this->file_src_pathname = $file;
			$this->file_src_name = basename($file);
			$this->file_src_size = filesize($file);
		}
		else
		{
			if($file['error'] > 0)
			{
				$this->uploaded = false;
				return;
			}
			$this->file_src_pathname = $file['tmp_name'];
			$this->file_src_name = $file['name'];
			$this->file_src_size = $file['size'];
			if($this->file_src_name == '')
			{
				$this->uploaded = false;
				return;
			}
		}
		$extension = null;
		ereg('\.([^\.]*$)', $this->file_src_name, $extension);
		$this->file_src_name_ext = '';
		$this->file_src_name_body = $this->file_src_name;
		if(is_array($extension))
		{
			$this->file_src_name_ext = strtolower($extension[1]);
			$this->file_src_name_body = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext))) - 1);
		}
		$info = $this->getMime();
		if(array_key_exists($this->file_src_mime, $this->image_supported))
		{
			$this->file_is_image = true;
			$this->image_src_type = $this->image_supported[$this->file_src_mime];
		}
		else
		{
			$this->uploaded = false;
			return;
		}
		if(is_array($info))
		{
			$this->image_src_x = $info[0];
			$this->image_src_y = $info[1];
			$this->image_src_pixels = $this->image_src_x * $this->image_src_y;
			$this->image_src_bits = array_key_exists('bits', $info) ? $info['bits'] : null;
		}
	}
	
	function getMime()
	{
		$info = getimagesize($this->file_src_pathname);
		$this->file_src_mime = null;
		if(function_exists("exif_imagetype"))
		{
			$mime = exif_imagetype($this->file_src_pathname);
			if($mime == IMAGETYPE_GIF)
				$this->file_src_mime = "image/gif";
			if($mime == IMAGETYPE_JPEG)
				$this->file_src_mime = "image/jpeg";
			if($mime == IMAGETYPE_PNG)
				$this->file_src_mime = "image/png";
			if($mime == IMAGETYPE_BMP)
				$this->file_src_mime = "image/bmp";
		}
		else
			$this->file_src_mime = $info['mime'];
		if(empty($this->file_src_mime) && function_exists('mime_content_type'))
			$this->file_src_mime = mime_content_type($this->file_src_pathname);
		return $info;
	}
	
	function rmkdir($path, $mode = 0777)
	{
		return is_dir($path) || ($this->rmkdir(dirname($path), $mode) && $this->_mkdir($path, $mode));
	}
	
	function _mkdir($path, $mode = 0777)
	{
		$old = umask(0);
		$res = @mkdir($path, $mode);
		umask($old);
		return $res;
	}
	
	function imagecreatenew($x, $y, $fill = true, $trsp = false)
	{
		if( ! $this->image_is_palette)
		{
			$dst_im = imagecreatetruecolor($x, $y);
			if(empty($this->image_background_color) || $trsp)
			{
				imagealphablending($dst_im, false);
				imagefilledrectangle($dst_im, 0, 0, $x, $y, imagecolorallocatealpha($dst_im, 0, 0, 0, 127));
			}
		}
		else
		{
			$dst_im = imagecreate($x, $y);
			if(($fill && $this->image_is_transparent && empty($this->image_background_color)) || $trsp)
			{
				imagefilledrectangle($dst_im, 0, 0, $x, $y, $this->image_transparent_color);
				imagecolortransparent($dst_im, $this->image_transparent_color);
			}
		}
		if($fill &&  ! empty($this->image_background_color) &&  ! $trsp)
		{
			list($red, $green, $blue) = $this->getcolors($this->image_background_color);
			$background_color = imagecolorallocate($dst_im, $red, $green, $blue);
			imagefilledrectangle($dst_im, 0, 0, $x, $y, $background_color);
		}
		return $dst_im;
	}
	
	function imagetransfer($src_im, $dst_im)
	{
		if(is_resource($dst_im))
			imagedestroy($dst_im);
		$dst_im = & $src_im;
		return $dst_im;
	}
	
	function imagecopymergealpha(&$dst_im, &$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct = 0)
	{
		$dst_x = (int)$dst_x;
		$dst_y = (int)$dst_y;
		$src_x = (int)$src_x;
		$src_y = (int)$src_y;
		$src_w = (int)$src_w;
		$src_h = (int)$src_h;
		$pct = (int)$pct;
		$dst_w = imagesx($dst_im);
		$dst_h = imagesy($dst_im);
		
		for($y = $src_y; $y < $src_h; $y++)
		{
			for($x = $src_x; $x < $src_w; $x++)
			{
				
				if($x >= 0 && $x <= $dst_w && $y >= 0 && $y <= $dst_h)
				{
					$dst_pixel = imagecolorsforindex($dst_im, imagecolorat($dst_im, $x + $dst_x, $y + $dst_y));
					$src_pixel = imagecolorsforindex($src_im, imagecolorat($src_im, $x + $src_x, $y + $src_y));
					
					$src_alpha = 1 - ($src_pixel['alpha'] / 127);
					$dst_alpha = 1 - ($dst_pixel['alpha'] / 127);
					$opacity = $src_alpha * $pct / 100;
					if($dst_alpha >= $opacity)
						$alpha = $dst_alpha;
					if($dst_alpha < $opacity)
						$alpha = $opacity;
					if($alpha > 1)
						$alpha = 1;
					
					if($opacity > 0)
					{
						$dst_red = round((($dst_pixel['red'] * $dst_alpha * (1 - $opacity))));
						$dst_green = round((($dst_pixel['green'] * $dst_alpha * (1 - $opacity))));
						$dst_blue = round((($dst_pixel['blue'] * $dst_alpha * (1 - $opacity))));
						$src_red = round((($src_pixel['red'] * $opacity)));
						$src_green = round((($src_pixel['green'] * $opacity)));
						$src_blue = round((($src_pixel['blue'] * $opacity)));
						$red = round(($dst_red + $src_red) / ($dst_alpha * (1 - $opacity) + $opacity));
						$green = round(($dst_green + $src_green) / ($dst_alpha * (1 - $opacity) + $opacity));
						$blue = round(($dst_blue + $src_blue) / ($dst_alpha * (1 - $opacity) + $opacity));
						if($red > 255)
							$red = 255;
						if($green > 255)
							$green = 255;
						if($blue > 255)
							$blue = 255;
						$alpha = round((1 - $alpha) * 127);
						$color = imagecolorallocatealpha($dst_im, $red, $green, $blue, $alpha);
						imagesetpixel($dst_im, $x + $dst_x, $y + $dst_y, $color);
					}
				}
			}
		}
		return true;
	}
	
	function process($server_path = null)
	{
		$return_mode = false;
		$return_content = null;
		if( ! $this->uploaded)
			return false;
		if(empty($server_path) || is_null($server_path))
			$return_mode = true;
		else
		{
			if(strtolower(substr(PHP_OS, 0, 3)) === 'win')
			{
				if(substr($server_path,  - 1, 1) != '\\')
					$server_path = $server_path . '\\';
			}
			else
			{
				if(substr($server_path,  - 1, 1) != '/')
					$server_path = $server_path . '/';
			}
		}
		if($this->file_src_size > $this->file_max_size)
			return false;
		if($this->mime_check && empty($this->file_src_mime))
			return false;
		if($this->file_is_image && is_numeric($this->image_src_x) && is_numeric($this->image_src_y))
		{
			$ratio = $this->image_src_x / $this->image_src_y;
			if( ! is_null($this->image_max_width) && $this->image_src_x > $this->image_max_width)
				return false;
			if( ! is_null($this->image_min_width) && $this->image_src_x < $this->image_min_width)
				return false;
			if( ! is_null($this->image_max_height) && $this->image_src_y > $this->image_max_height)
				return false;
			if( ! is_null($this->image_min_height) && $this->image_src_y < $this->image_min_height)
				return false;
			if( ! is_null($this->image_max_ratio) && $ratio > $this->image_max_ratio)
				return false;
			if( ! is_null($this->image_min_ratio) && $ratio < $this->image_min_ratio)
				return false;
			if( ! is_null($this->image_max_pixels) && $this->image_src_pixels > $this->image_max_pixels)
				return false;
			if( ! is_null($this->image_min_pixels) && $this->image_src_pixels < $this->image_min_pixels)
				return false;
		}
		$this->file_dst_path = $server_path;
		$this->file_dst_name = $this->file_src_name;
		$this->file_dst_name_body = $this->file_src_name_body;
		$this->file_dst_name_ext = $this->file_src_name_ext;
		
		if($this->image_convert != '')
			$this->file_dst_name_ext = $this->image_convert;
		if($this->file_new_name_body != '')
			$this->file_dst_name_body = $this->file_new_name_body;
		if($this->file_new_name_ext != '')
			$this->file_dst_name_ext = $this->file_new_name_ext;
		if($this->file_name_body_add != '')
			$this->file_dst_name_body = $this->file_dst_name_body . $this->file_name_body_add;
		if($this->file_safe_name)
		{
			$this->file_dst_name_body = str_replace(array(' ', '-'), array('_', '_'), $this->file_dst_name_body);
			$this->file_dst_name_body = ereg_replace('[^A-Za-z0-9_]', '', $this->file_dst_name_body);
		}
		
		$image_manipulation = ($this->file_is_image && ($this->blur || $this->image_resize || $this->image_convert != '' || is_numeric($this->image_brightness) || is_numeric($this->image_contrast) || is_numeric($this->image_threshold) ||  ! empty($this->image_tint_color) ||  ! empty($this->image_overlay_color) ||  ! empty($this->image_text) || $this->image_greyscale || $this->image_negative ||  ! empty($this->image_watermark) || is_numeric($this->image_rotate) || is_numeric($this->jpeg_size) ||  ! empty($this->image_flip) ||  ! empty($this->image_crop) ||  ! empty($this->image_border) || $this->image_frame > 0 || $this->image_bevel > 0 || $this->image_reflection_height));
		
		$this->file_dst_name = $this->file_dst_name_body . ( ! empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
		if($image_manipulation && $this->image_convert != '')
			$this->file_dst_name = $this->file_dst_name_body . '.' . $this->image_convert;
		
		if( ! $return_mode)
			$this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
		if( ! empty($this->file_src_temp))
		{
			$this->file_src_pathname = $this->file_src_temp;
			if( ! file_exists($this->file_src_pathname))
				return false;
		}
		if( ! $this->no_upload_check)
		{
			if( ! is_uploaded_file($this->file_src_pathname))
				return false;
		}
		else
		{
			if( ! file_exists($this->file_src_pathname))
				return false;
		}
		if( ! $return_mode)
		{
			if( ! file_exists($this->file_dst_path) &&  ! $this->rmkdir($this->file_dst_path, $this->dir_chmod))
				return false;
			if( ! is_dir($this->file_dst_path))
				return false;
			$hash = md5($this->file_dst_name_body . rand(1, 1000));
			if( ! ($f = @fopen($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext, 'a+')))
			{
				if($this->dir_auto_chmod)
				{
					if( ! @chmod($this->file_dst_path, $this->dir_chmod))
						return false;
					if( ! ($f = @fopen($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext, 'a+')))
						return false;
					@fclose($f);
				}
				else
					return false;
			}
			else
			{
				@fclose($f);
				@unlink($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext);
			}
			
			if( ! $this->no_upload_check && empty($this->file_src_temp) &&  ! file_exists($this->file_src_pathname))
			{
				$hash = md5($this->file_dst_name_body . rand(1, 1000));
				if(move_uploaded_file($this->file_src_pathname, $this->file_dst_path . $hash . '.' . $this->file_dst_name_ext))
				{
					$this->file_src_pathname = $this->file_dst_path . $hash . '.' . $this->file_dst_name_ext;
					$this->file_src_temp = $this->file_src_pathname;
				}
				else
					return false;
			}
		}
		if($image_manipulation)
		{
			$f = @fopen($this->file_src_pathname, 'r');
			if(!$f)
				return false;
			@fclose($f);
			$image_src = false;
			switch($this->image_supported[$this->file_src_mime])
			{
				case 'jpg':
					$image_src = @imagecreatefromjpeg($this->file_src_pathname);
					break;
				case 'png':
					$image_src = @imagecreatefrompng($this->file_src_pathname);
					break;
				case 'gif':
					$image_src = @imagecreatefromgif($this->file_src_pathname);
					break;
				case 'bmp':
					$image_src = @$this->imagecreatefrombmp($this->file_src_pathname);
					break;
			}
			if( ! $image_src)
				return false;
			if($image_src)
			{
				if(empty($this->image_convert))
					$this->image_convert = $this->file_src_name_ext;
				if( ! in_array($this->image_convert, $this->image_supported))
					$this->image_convert = 'jpg';
				if($this->image_convert != 'png' && $this->image_convert != 'gif' &&  ! empty($this->image_default_color) && empty($this->image_background_color))
					$this->image_background_color = $this->image_default_color;
				if( ! empty($this->image_background_color))
					$this->image_default_color = $this->image_background_color;
				$this->image_src_x = imagesx($image_src);
				$this->image_src_y = imagesy($image_src);
				$this->image_dst_x = $this->image_src_x;
				$this->image_dst_y = $this->image_src_y;
				$ratio_crop = null;
				
				if( ! imageistruecolor($image_src))
				{
					$this->image_is_palette = true;
					$this->image_transparent_color = imagecolortransparent($image_src);
					if($this->image_transparent_color >= 0 && imagecolorstotal($image_src) > $this->image_transparent_color)
						$this->image_is_transparent = true;
					$transparent_color = imagecolortransparent($image_src);
					if($transparent_color >= 0 && imagecolorstotal($image_src) > $transparent_color)
					{
						$rgb = imagecolorsforindex($image_src, $transparent_color);
						$transparent_color = ($rgb['red'] << 16) | ($rgb['green'] << 8) | $rgb['blue'];
						imagecolortransparent($image_src, imagecolorallocate($image_src, 0, 0, 0));
					}
					$true_color = imagecreatetruecolor($this->image_src_x, $this->image_src_y);
					imagealphablending($image_src, false);
					imagesavealpha($image_src, true);
					imagecopy($true_color, $image_src, 0, 0, 0, 0, $this->image_src_x, $this->image_src_y);
					$image_src = $this->imagetransfer($true_color, $image_src);
					if($transparent_color >= 0)
					{
						imagealphablending($image_src, false);
						imagesavealpha($image_src, true);
						for($x = 0; $x < $this->image_src_x; $x++)
							for($y = 0; $y < $this->image_src_y; $y++)
								if(imagecolorat($image_src, $x, $y) == $transparent_color)
									imagesetpixel($image_src, $x, $y, 127 << 24);
					}
					$this->image_is_palette = false;
				}
				
				if($this->image_resize)
				{
					if($this->image_ratio_x)
					{
						$this->image_dst_x = round(($this->image_src_x * $this->image_y) / $this->image_src_y);
						$this->image_dst_y = $this->image_y;
					}
					else if($this->image_ratio_y)
					{
						$this->image_dst_x = $this->image_x;
						$this->image_dst_y = round(($this->image_src_y * $this->image_x) / $this->image_src_x);
					}
					else if(is_numeric($this->image_ratio_pixels))
					{
						$pixels = $this->image_src_y * $this->image_src_x;
						$diff = sqrt($this->image_ratio_pixels / $pixels);
						$this->image_dst_x = round($this->image_src_x * $diff);
						$this->image_dst_y = round($this->image_src_y * $diff);
					}
					else if($this->image_ratio || $this->image_ratio_crop || $this->image_ratio_fill || $this->image_ratio_no_zoom_in || $this->image_ratio_no_zoom_out)
					{
						if(( ! $this->image_ratio_no_zoom_in &&  ! $this->image_ratio_no_zoom_out) || ($this->image_ratio_no_zoom_in && ($this->image_src_x > $this->image_x || $this->image_src_y > $this->image_y)) || ($this->image_ratio_no_zoom_out && $this->image_src_x < $this->image_x && $this->image_src_y < $this->image_y))
						{
							$this->image_dst_x = $this->image_x;
							$this->image_dst_y = $this->image_y;
							if($this->image_ratio_crop)
							{
								if( ! is_string($this->image_ratio_crop))
									$this->image_ratio_crop = '';
								$this->image_ratio_crop = strtolower($this->image_ratio_crop);
								if(($this->image_src_x / $this->image_x) > ($this->image_src_y / $this->image_y))
								{
									$this->image_dst_y = $this->image_y;
									$this->image_dst_x = intval($this->image_src_x * ($this->image_y / $this->image_src_y));
									$ratio_crop = array();
									$ratio_crop['x'] = $this->image_dst_x - $this->image_x;
									if(strpos($this->image_ratio_crop, 'l') !== false)
									{
										$ratio_crop['l'] = 0;
										$ratio_crop['r'] = $ratio_crop['x'];
									}
									else if(strpos($this->image_ratio_crop, 'r') !== false)
									{
										$ratio_crop['l'] = $ratio_crop['x'];
										$ratio_crop['r'] = 0;
									}
									else
									{
										$ratio_crop['l'] = round($ratio_crop['x'] / 2);
										$ratio_crop['r'] = $ratio_crop['x'] - $ratio_crop['l'];
									}
									if(is_null($this->image_crop))
										$this->image_crop = array(0, 0, 0, 0);
								}
								else
								{
									$this->image_dst_x = $this->image_x;
									$this->image_dst_y = intval($this->image_src_y * ($this->image_x / $this->image_src_x));
									$ratio_crop = array();
									$ratio_crop['y'] = $this->image_dst_y - $this->image_y;
									if(strpos($this->image_ratio_crop, 't') !== false)
									{
										$ratio_crop['t'] = 0;
										$ratio_crop['b'] = $ratio_crop['y'];
									}
									else if(strpos($this->image_ratio_crop, 'b') !== false)
									{
										$ratio_crop['t'] = $ratio_crop['y'];
										$ratio_crop['b'] = 0;
									}
									else
									{
										$ratio_crop['t'] = round($ratio_crop['y'] / 2);
										$ratio_crop['b'] = $ratio_crop['y'] - $ratio_crop['t'];
									}
									if(is_null($this->image_crop))
										$this->image_crop = array(0, 0, 0, 0);
								}
							}
							else if($this->image_ratio_fill)
							{
								if( ! is_string($this->image_ratio_fill))
									$this->image_ratio_fill = '';
								$this->image_ratio_fill = strtolower($this->image_ratio_fill);
								if(($this->image_src_x / $this->image_x) < ($this->image_src_y / $this->image_y))
								{
									$this->image_dst_y = $this->image_y;
									$this->image_dst_x = intval($this->image_src_x * ($this->image_y / $this->image_src_y));
									$ratio_crop = array();
									$ratio_crop['x'] = $this->image_dst_x - $this->image_x;
									if(strpos($this->image_ratio_fill, 'l') !== false)
									{
										$ratio_crop['l'] = 0;
										$ratio_crop['r'] = $ratio_crop['x'];
									}
									else if(strpos($this->image_ratio_fill, 'r') !== false)
									{
										$ratio_crop['l'] = $ratio_crop['x'];
										$ratio_crop['r'] = 0;
									}
									else
									{
										$ratio_crop['l'] = round($ratio_crop['x'] / 2);
										$ratio_crop['r'] = $ratio_crop['x'] - $ratio_crop['l'];
									}
									if(is_null($this->image_crop))
										$this->image_crop = array(0, 0, 0, 0);
								}
								else
								{
									$this->image_dst_x = $this->image_x;
									$this->image_dst_y = intval($this->image_src_y * ($this->image_x / $this->image_src_x));
									$ratio_crop = array();
									$ratio_crop['y'] = $this->image_dst_y - $this->image_y;
									if(strpos($this->image_ratio_fill, 't') !== false)
									{
										$ratio_crop['t'] = 0;
										$ratio_crop['b'] = $ratio_crop['y'];
									}
									else if(strpos($this->image_ratio_fill, 'b') !== false)
									{
										$ratio_crop['t'] = $ratio_crop['y'];
										$ratio_crop['b'] = 0;
									}
									else
									{
										$ratio_crop['t'] = round($ratio_crop['y'] / 2);
										$ratio_crop['b'] = $ratio_crop['y'] - $ratio_crop['t'];
									}
									if(is_null($this->image_crop))
										$this->image_crop = array(0, 0, 0, 0);
								}
							}
							else
							{
								if(($this->image_src_x / $this->image_x) > ($this->image_src_y / $this->image_y))
								{
									$this->image_dst_x = $this->image_x;
									$this->image_dst_y = intval($this->image_src_y * ($this->image_x / $this->image_src_x));
								}
								else
								{
									$this->image_dst_y = $this->image_y;
									$this->image_dst_x = intval($this->image_src_x * ($this->image_y / $this->image_src_y));
								}
							}
						}
						else
						{
							$this->image_dst_x = $this->image_src_x;
							$this->image_dst_y = $this->image_src_y;
						}
					}
					else
					{
						$this->image_dst_x = $this->image_x;
						$this->image_dst_y = $this->image_y;
					}
					$image_dst = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_src_x, $this->image_src_y);
				
				}
				else
					$image_dst = & $image_src;
				if(( ! empty($this->image_crop) ||  ! is_null($ratio_crop)))
				{
					if(is_array($this->image_crop))
						$vars = $this->image_crop;
					else
						$vars = explode(' ', $this->image_crop);
					if(sizeof($vars) == 4)
					{
						$ct = $vars[0];
						$cr = $vars[1];
						$cb = $vars[2];
						$cl = $vars[3];
					}
					else if(sizeof($vars) == 2)
					{
						$ct = $vars[0];
						$cr = $vars[1];
						$cb = $vars[0];
						$cl = $vars[1];
					}
					else
					{
						$ct = $vars[0];
						$cr = $vars[0];
						$cb = $vars[0];
						$cl = $vars[0];
					}
					if(strpos($ct, '%') > 0)
						$ct = $this->image_dst_y * (str_replace('%', '', $ct) / 100);
					if(strpos($cr, '%') > 0)
						$cr = $this->image_dst_x * (str_replace('%', '', $cr) / 100);
					if(strpos($cb, '%') > 0)
						$cb = $this->image_dst_y * (str_replace('%', '', $cb) / 100);
					if(strpos($cl, '%') > 0)
						$cl = $this->image_dst_x * (str_replace('%', '', $cl) / 100);
					if(strpos($ct, 'px') > 0)
						$ct = str_replace('px', '', $ct);
					if(strpos($cr, 'px') > 0)
						$cr = str_replace('px', '', $cr);
					if(strpos($cb, 'px') > 0)
						$cb = str_replace('px', '', $cb);
					if(strpos($cl, 'px') > 0)
						$cl = str_replace('px', '', $cl);
					$ct = (int)$ct;
					$cr = (int)$cr;
					$cb = (int)$cb;
					$cl = (int)$cl;
					
					if( ! is_null($ratio_crop))
					{
						if(array_key_exists('t', $ratio_crop))
							$ct += $ratio_crop['t'];
						if(array_key_exists('r', $ratio_crop))
							$cr += $ratio_crop['r'];
						if(array_key_exists('b', $ratio_crop))
							$cb += $ratio_crop['b'];
						if(array_key_exists('l', $ratio_crop))
							$cl += $ratio_crop['l'];
					}
					$this->image_dst_x = $this->image_dst_x - $cl - $cr;
					$this->image_dst_y = $this->image_dst_y - $ct - $cb;
					if($this->image_dst_x < 1)
						$this->image_dst_x = 1;
					if($this->image_dst_y < 1)
						$this->image_dst_y = 1;
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					imagecopy($tmp, $image_dst, 0, 0, $cl, $ct, $this->image_dst_x, $this->image_dst_y);
					if($ct < 0 || $cr < 0 || $cb < 0 || $cl < 0)
					{
						if( ! empty($this->image_background_color))
						{
							list($red, $green, $blue) = $this->getcolors($this->image_background_color);
							$fill = imagecolorallocate($tmp, $red, $green, $blue);
						}
						else
						{
							$fill = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
						}
						if($ct < 0)
							imagefilledrectangle($tmp, 0, 0, $this->image_dst_x,  - $ct, $fill);
						if($cr < 0)
							imagefilledrectangle($tmp, $this->image_dst_x + $cr, 0, $this->image_dst_x, $this->image_dst_y, $fill);
						if($cb < 0)
							imagefilledrectangle($tmp, 0, $this->image_dst_y + $cb, $this->image_dst_x, $this->image_dst_y, $fill);
						if($cl < 0)
							imagefilledrectangle($tmp, 0, 0,  - $cl, $this->image_dst_y, $fill);
					}
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				if( ! empty($this->image_flip))
				{
					$this->image_flip = strtolower($this->image_flip);
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					for($x = 0; $x < $this->image_dst_x; $x++)
						for($y = 0; $y < $this->image_dst_y; $y++)
						{
							if(strpos($this->image_flip, 'v') !== false)
								imagecopy($tmp, $image_dst, $this->image_dst_x - $x - 1, $y, $x, $y, 1, 1);
							else
								imagecopy($tmp, $image_dst, $x, $this->image_dst_y - $y - 1, $x, $y, 1, 1);
						}
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				if(is_numeric($this->image_rotate))
				{
					if( ! in_array($this->image_rotate, array(0, 90, 180, 270)))
						$this->image_rotate = 0;
					if($this->image_rotate != 0)
					{
						if($this->image_rotate == 90 || $this->image_rotate == 270)
							$tmp = $this->imagecreatenew($this->image_dst_y, $this->image_dst_x);
						else
							$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
						for($x = 0; $x < $this->image_dst_x; $x++)
						{
							for($y = 0; $y < $this->image_dst_y; $y++)
							{
								if($this->image_rotate == 90)
								{
									imagecopy($tmp, $image_dst, $y, $x, $x, $this->image_dst_y - $y - 1, 1, 1);
								}
								else if($this->image_rotate == 180)
								{
									imagecopy($tmp, $image_dst, $x, $y, $this->image_dst_x - $x - 1, $this->image_dst_y - $y - 1, 1, 1);
								}
								else if($this->image_rotate == 270)
								{
									imagecopy($tmp, $image_dst, $y, $x, $this->image_dst_x - $x - 1, $y, 1, 1);
								}
								else
								{
									imagecopy($tmp, $image_dst, $x, $y, $x, $y, 1, 1);
								}
							}
						}
						if($this->image_rotate == 90 || $this->image_rotate == 270)
						{
							$t = $this->image_dst_y;
							$this->image_dst_y = $this->image_dst_x;
							$this->image_dst_x = $t;
						}
						$image_dst = $this->imagetransfer($tmp, $image_dst);
					}
				}
				
				if((is_numeric($this->image_overlay_percent) && $this->image_overlay_percent > 0 &&  ! empty($this->image_overlay_color)))
				{
					list($red, $green, $blue) = $this->getcolors($this->image_overlay_color);
					$filter = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
					$color = imagecolorallocate($filter, $red, $green, $blue);
					imagefilledrectangle($filter, 0, 0, $this->image_dst_x, $this->image_dst_y, $color);
					$this->imagecopymergealpha($image_dst, $filter, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_overlay_percent);
					imagedestroy($filter);
				}
				if(($this->image_negative || $this->image_greyscale || is_numeric($this->image_threshold) || is_numeric($this->image_brightness) || is_numeric($this->image_contrast) ||  ! empty($this->image_tint_color)))
				{
					if( ! empty($this->image_tint_color))
						list($tint_red, $tint_green, $tint_blue) = $this->getcolors($this->image_tint_color);
					imagealphablending($image_dst, true);
					for($y = 0; $y < $this->image_dst_y; $y++)
					{
						for($x = 0; $x < $this->image_dst_x; $x++)
						{
							if($this->image_greyscale)
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$r = $g = $b = round((0.2125 * $pixel['red']) + (0.7154 * $pixel['green']) + (0.0721 * $pixel['blue']));
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
							if(is_numeric($this->image_threshold))
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$c = (round($pixel['red'] + $pixel['green'] + $pixel['blue']) / 3) - 127;
								$r = $g = $b = ($c > $this->image_threshold ? 255 : 0);
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
							if(is_numeric($this->image_brightness))
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$r = max(min(round($pixel['red'] + (($this->image_brightness * 2))), 255), 0);
								$g = max(min(round($pixel['green'] + (($this->image_brightness * 2))), 255), 0);
								$b = max(min(round($pixel['blue'] + (($this->image_brightness * 2))), 255), 0);
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
							if(is_numeric($this->image_contrast))
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$r = max(min(round(($this->image_contrast + 128) * $pixel['red'] / 128), 255), 0);
								$g = max(min(round(($this->image_contrast + 128) * $pixel['green'] / 128), 255), 0);
								$b = max(min(round(($this->image_contrast + 128) * $pixel['blue'] / 128), 255), 0);
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
							if( ! empty($this->image_tint_color))
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$r = min(round($tint_red * $pixel['red'] / 169), 255);
								$g = min(round($tint_green * $pixel['green'] / 169), 255);
								$b = min(round($tint_blue * $pixel['blue'] / 169), 255);
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
							if( ! empty($this->image_negative))
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								$r = round(255 - $pixel['red']);
								$g = round(255 - $pixel['green']);
								$b = round(255 - $pixel['blue']);
								$color = imagecolorallocatealpha($image_dst, $r, $g, $b, $pixel['alpha']);
								imagesetpixel($image_dst, $x, $y, $color);
							}
						}
					}
				}
				if( ! empty($this->image_border))
				{
					if(is_array($this->image_border))
						$vars = $this->image_border;
					else
						$vars = explode(' ', $this->image_border);
					if(sizeof($vars) == 4)
					{
						$ct = $vars[0];
						$cr = $vars[1];
						$cb = $vars[2];
						$cl = $vars[3];
					}
					else if(sizeof($vars) == 2)
					{
						$ct = $vars[0];
						$cr = $vars[1];
						$cb = $vars[0];
						$cl = $vars[1];
					}
					else
					{
						$ct = $vars[0];
						$cr = $vars[0];
						$cb = $vars[0];
						$cl = $vars[0];
					}
					if(strpos($ct, '%') > 0)
						$ct = $this->image_dst_y * (str_replace('%', '', $ct) / 100);
					if(strpos($cr, '%') > 0)
						$cr = $this->image_dst_x * (str_replace('%', '', $cr) / 100);
					if(strpos($cb, '%') > 0)
						$cb = $this->image_dst_y * (str_replace('%', '', $cb) / 100);
					if(strpos($cl, '%') > 0)
						$cl = $this->image_dst_x * (str_replace('%', '', $cl) / 100);
					if(strpos($ct, 'px') > 0)
						$ct = str_replace('px', '', $ct);
					if(strpos($cr, 'px') > 0)
						$cr = str_replace('px', '', $cr);
					if(strpos($cb, 'px') > 0)
						$cb = str_replace('px', '', $cb);
					if(strpos($cl, 'px') > 0)
						$cl = str_replace('px', '', $cl);
					$ct = (int)$ct;
					$cr = (int)$cr;
					$cb = (int)$cb;
					$cl = (int)$cl;
					$this->image_dst_x = $this->image_dst_x + $cl + $cr;
					$this->image_dst_y = $this->image_dst_y + $ct + $cb;
					if( ! empty($this->image_border_color))
						list($red, $green, $blue) = $this->getcolors($this->image_border_color);
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					$background = imagecolorallocatealpha($tmp, $red, $green, $blue, 0);
					imagefilledrectangle($tmp, 0, 0, $this->image_dst_x, $this->image_dst_y, $background);
					imagecopy($tmp, $image_dst, $cl, $ct, 0, 0, $this->image_dst_x - $cr - $cl, $this->image_dst_y - $cb - $ct);
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				
				if(is_numeric($this->image_frame))
				{
					if(is_array($this->image_frame_colors))
						$vars = $this->image_frame_colors;
					else
						$vars = explode(' ', $this->image_frame_colors);
					$nb = sizeof($vars);
					$this->image_dst_x = $this->image_dst_x + ($nb * 2);
					$this->image_dst_y = $this->image_dst_y + ($nb * 2);
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					imagecopy($tmp, $image_dst, $nb, $nb, 0, 0, $this->image_dst_x - ($nb * 2), $this->image_dst_y - ($nb * 2));
					for($i = 0; $i < $nb; $i++)
					{
						list($red, $green, $blue) = $this->getcolors($vars[$i]);
						$c = imagecolorallocate($tmp, $red, $green, $blue);
						if($this->image_frame == 1)
						{
							imageline($tmp, $i, $i, $this->image_dst_x - $i - 1, $i, $c);
							imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1, $this->image_dst_x - $i - 1, $i, $c);
							imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1, $i, $this->image_dst_y - $i - 1, $c);
							imageline($tmp, $i, $i, $i, $this->image_dst_y - $i - 1, $c);
						}
						else
						{
							imageline($tmp, $i, $i, $this->image_dst_x - $i - 1, $i, $c);
							imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $this->image_dst_x - $nb + $i, $nb - $i, $c);
							imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $nb - $i, $this->image_dst_y - $nb + $i, $c);
							imageline($tmp, $i, $i, $i, $this->image_dst_y - $i - 1, $c);
						}
					}
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				if($this->image_bevel > 0)
				{
					list($red1, $green1, $blue1) = $this->getcolors($this->image_bevel_color1);
					list($red2, $green2, $blue2) = $this->getcolors($this->image_bevel_color2);
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y);
					imagecopy($tmp, $image_dst, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
					imagealphablending($tmp, true);
					for($i = 0; $i < $this->image_bevel; $i++)
					{
						$alpha = round(($i / $this->image_bevel) * 127);
						$c1 = imagecolorallocatealpha($tmp, $red1, $green1, $blue1, $alpha);
						$c2 = imagecolorallocatealpha($tmp, $red2, $green2, $blue2, $alpha);
						imageline($tmp, $i, $i, $this->image_dst_x - $i - 1, $i, $c1);
						imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i, $this->image_dst_x - $i - 1, $i, $c2);
						imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1, $i, $this->image_dst_y - $i - 1, $c2);
						imageline($tmp, $i, $i, $i, $this->image_dst_y - $i - 1, $c1);
					}
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				if($this->blur)
				{
					for($i = 0; $i < 4; $i++)
						imagefilter($image_dst, IMG_FILTER_GAUSSIAN_BLUR);
				}
				
				if($this->image_watermark != '' && file_exists($this->image_watermark))
				{
					$this->image_watermark_position = strtolower($this->image_watermark_position);
					$watermark_info = getimagesize($this->image_watermark);
					$watermark_type = (array_key_exists(2, $watermark_info) ? $watermark_info[2] : null); // 1 = GIF, 2 = JPG, 3 = PNG
					$watermark_checked = false;
					$filter = false;
					if($watermark_type == IMAGETYPE_GIF)
					{
						$filter = @imagecreatefromgif($this->image_watermark);
					}
					else if($watermark_type == IMAGETYPE_JPEG)
					{
						$filter = @imagecreatefromjpeg($this->image_watermark);
					}
					else if($watermark_type == IMAGETYPE_PNG)
					{
						$filter = @imagecreatefrompng($this->image_watermark);
					}
					else if($watermark_type == IMAGETYPE_BMP)
					{
						$filter = @$this->imagecreatefrombmp($this->image_watermark);
					}
					if($filter)
						$watermark_checked = true;
					if($watermark_checked)
					{
						$watermark_width = imagesx($filter);
						$watermark_height = imagesy($filter);
						$watermark_y = $watermark_x = 0;
						if(is_numeric($this->image_watermark_x))
						{
							$watermark_x = $this->image_watermark_x;
							if($this->image_watermark_x < 0)
								$watermark_x += $this->image_dst_x - $watermark_width;
						}
						else
						{
							if(strpos($this->image_watermark_position, 'r') !== false)
							{
								$watermark_x = $this->image_dst_x - $watermark_width;
							}
							else if(strpos($this->image_watermark_position, 'l') !== false)
							{
								$watermark_x = 0;
							}
							else
							{
								$watermark_x = ($this->image_dst_x - $watermark_width) / 2;
							}
						}
						if(is_numeric($this->image_watermark_y))
						{
							$watermark_y = $this->image_watermark_y;
							if($this->image_watermark_y < 0)
								$watermark_y += $this->image_dst_y - $watermark_height;
						}
						else
						{
							if(strpos($this->image_watermark_position, 'b') !== false)
							{
								$watermark_y = $this->image_dst_y - $watermark_height;
							}
							else if(strpos($this->image_watermark_position, 't') !== false)
							{
								$watermark_y = 0;
							}
							else
							{
								$watermark_y = ($this->image_dst_y - $watermark_height) / 2;
							}
						}
						imagecopyresampled($image_dst, $filter, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);
					}
				}
				
				if( ! empty($this->image_text))
				{
					$src_size = $this->file_src_size / 1024;
					$src_size_mb = number_format($src_size / 1024, 1, ".", " ");
					$src_size_kb = number_format($src_size, 1, ".", " ");
					$src_size_human = ($src_size > 1024 ? $src_size_mb . " MB" : $src_size_kb . " kb");
					
					$this->image_text = str_replace(array('[src_name]', '[src_name_body]', '[src_name_ext]', '[src_pathname]', '[src_mime]', '[src_size]', '[src_size_kb]', '[src_size_mb]', '[src_size_human]', '[src_x]', '[src_y]', '[src_pixels]', '[src_type]', '[src_bits]', '[dst_path]', '[dst_name_body]', '[dst_name_ext]', '[dst_name]', '[dst_pathname]', '[dst_x]', '[dst_y]', '[date]', '[time]', '[host]', '[server]', '[ip]'), array($this->file_src_name, $this->file_src_name_body, $this->file_src_name_ext, $this->file_src_pathname, $this->file_src_mime, $this->file_src_size, $src_size_kb, $src_size_mb, $src_size_human, $this->image_src_x, $this->image_src_y, $this->image_src_pixels, $this->image_src_type, $this->image_src_bits, $this->file_dst_path, $this->file_dst_name_body, $this->file_dst_name_ext, $this->file_dst_name, $this->file_dst_pathname, $this->image_dst_x, $this->image_dst_y, date('Y-m-d'), date('H:i:s'), $_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR']), $this->image_text);
					
					if( ! is_numeric($this->image_text_padding))
						$this->image_text_padding = 0;
					if( ! is_numeric($this->image_text_line_spacing))
						$this->image_text_line_spacing = 0;
					if( ! is_numeric($this->image_text_padding_x))
						$this->image_text_padding_x = $this->image_text_padding;
					if( ! is_numeric($this->image_text_padding_y))
						$this->image_text_padding_y = $this->image_text_padding;
					$this->image_text_position = strtolower($this->image_text_position);
					$this->image_text_direction = strtolower($this->image_text_direction);
					$this->image_text_alignment = strtolower($this->image_text_alignment);
					
					if( ! is_numeric($this->image_text_font) && strlen($this->image_text_font) > 4 && substr(strtolower($this->image_text_font),  - 4) == '.gdf')
					{
						if( ! ($this->image_text_font = @imageloadfont($this->image_text_font)))
							$this->image_text_font = 5;
					}
					
					$text = explode("\n", $this->image_text);
					$char_width = imagefontwidth($this->image_text_font);
					$char_height = imagefontheight($this->image_text_font);
					$line_width = $line_height = $text_width = $text_height = 0;
					
					foreach($text as $k=>$v)
					{
						if($this->image_text_direction == 'v')
						{
							$h = ($char_width * strlen($v));
							if($h > $text_height)
								$text_height = $h;
							$line_width = $char_height;
							$text_width += $line_width + ($k < (sizeof($text) - 1) ? $this->image_text_line_spacing : 0);
						}
						else
						{
							$w = ($char_width * strlen($v));
							if($w > $text_width)
								$text_width = $w;
							$line_height = $char_height;
							$text_height += $line_height + ($k < (sizeof($text) - 1) ? $this->image_text_line_spacing : 0);
						}
					}
					$text_width += (2 * $this->image_text_padding_x);
					$text_height += (2 * $this->image_text_padding_y);
					$text_x = 0;
					$text_y = 0;
					if(is_numeric($this->image_text_x))
					{
						$text_x = $this->image_text_x;
						if($this->image_text_x < 0)
							$text_x += $this->image_dst_x - $text_width;
					}
					else
					{
						if(strpos($this->image_text_position, 'r') !== false)
						{
							$text_x = $this->image_dst_x - $text_width;
						}
						else if(strpos($this->image_text_position, 'l') !== false)
						{
							$text_x = 0;
						}
						else
							$text_x = ($this->image_dst_x - $text_width) / 2;
					}
					if(is_numeric($this->image_text_y))
					{
						$text_y = $this->image_text_y;
						if($this->image_text_y < 0)
							$text_y += $this->image_dst_y - $text_height;
					}
					else
					{
						if(strpos($this->image_text_position, 'b') !== false)
						{
							$text_y = $this->image_dst_y - $text_height;
						}
						else if(strpos($this->image_text_position, 't') !== false)
						{
							$text_y = 0;
						}
						else
							$text_y = ($this->image_dst_y - $text_height) / 2;
					}
					
					if( ! empty($this->image_text_background))
					{
						list($red, $green, $blue) = $this->getcolors($this->image_text_background);
						if((is_numeric($this->image_text_background_percent)) && $this->image_text_background_percent >= 0 && $this->image_text_background_percent <= 100)
						{
							$filter = imagecreatetruecolor($text_width, $text_height);
							$background_color = imagecolorallocate($filter, $red, $green, $blue);
							imagefilledrectangle($filter, 0, 0, $text_width, $text_height, $background_color);
							$this->imagecopymergealpha($image_dst, $filter, $text_x, $text_y, 0, 0, $text_width, $text_height, $this->image_text_background_percent);
							imagedestroy($filter);
						}
						else
						{
							$background_color = imagecolorallocate($image_dst, $red, $green, $blue);
							imagefilledrectangle($image_dst, $text_x, $text_y, $text_x + $text_width, $text_y + $text_height, $background_color);
						}
					}
					$text_x += $this->image_text_padding_x;
					$text_y += $this->image_text_padding_y;
					$t_width = $text_width - (2 * $this->image_text_padding_x);
					$t_height = $text_height - (2 * $this->image_text_padding_y);
					list($red, $green, $blue) = $this->getcolors($this->image_text_color);
					if((is_numeric($this->image_text_percent)) && $this->image_text_percent >= 0 && $this->image_text_percent <= 100)
					{
						if($t_width < 0)
							$t_width = 0;
						if($t_height < 0)
							$t_height = 0;
						$filter = $this->imagecreatenew($t_width, $t_height, false, true);
						$text_color = imagecolorallocate($filter, $red, $green, $blue);
						foreach($text as $k=>$v)
							if($this->image_text_direction == 'v')
								imagestringup($filter, $this->image_text_font, $k * ($line_width + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), $text_height - (2 * $this->image_text_padding_y) - ($this->image_text_alignment == 'l' ? 0 : (($t_height - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), $v, $text_color);
							else
								imagestring($filter, $this->image_text_font, ($this->image_text_alignment == 'l' ? 0 : (($t_width - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), $k * ($line_height + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), $v, $text_color);
						$this->imagecopymergealpha($image_dst, $filter, $text_x, $text_y, 0, 0, $t_width, $t_height, $this->image_text_percent);
						imagedestroy($filter);
					
					}
					else
					{
						$text_color = imageColorAllocate($image_dst, $red, $green, $blue);
						foreach($text as $k=>$v)
							if($this->image_text_direction == 'v')
								imagestringup($image_dst, $this->image_text_font, $text_x + $k * ($line_width + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), $text_y + $text_height - (2 * $this->image_text_padding_y) - ($this->image_text_alignment == 'l' ? 0 : (($t_height - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), $v, $text_color);
							else
								imagestring($image_dst, $this->image_text_font, $text_x + ($this->image_text_alignment == 'l' ? 0 : (($t_width - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), $text_y + $k * ($line_height + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), $v, $text_color);
					}
				}
				if($this->image_reflection_height)
				{
					$image_reflection_height = $this->image_reflection_height;
					if(strpos($image_reflection_height, '%') > 0)
						$image_reflection_height = $this->image_dst_y * (str_replace('%', '', $image_reflection_height / 100));
					if(strpos($image_reflection_height, 'px') > 0)
						$image_reflection_height = str_replace('px', '', $image_reflection_height);
					$image_reflection_height = (int)$image_reflection_height;
					if($image_reflection_height > $this->image_dst_y)
						$image_reflection_height = $this->image_dst_y;
					if(empty($this->image_reflection_opacity))
						$this->image_reflection_opacity = 60;
					$tmp = $this->imagecreatenew($this->image_dst_x, $this->image_dst_y + $image_reflection_height + $this->image_reflection_space, true);
					$transparency = $this->image_reflection_opacity;
					
					imagecopy($tmp, $image_dst, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y + ($this->image_reflection_space < 0 ? $this->image_reflection_space : 0));
					
					if($image_reflection_height + $this->image_reflection_space > 0)
					{
						if( ! empty($this->image_background_color))
						{
							list($red, $green, $blue) = $this->getcolors($this->image_background_color);
							$fill = imagecolorallocate($tmp, $red, $green, $blue);
						}
						else
							$fill = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
						imagefill($tmp, round($this->image_dst_x / 2), $this->image_dst_y + $image_reflection_height + $this->image_reflection_space - 1, $fill);
					}
					
					for($y = 0; $y < $image_reflection_height; $y++)
					{
						for($x = 0; $x < $this->image_dst_x; $x++)
						{
							$pixel_b = imagecolorsforindex($tmp, imagecolorat($tmp, $x, $y + $this->image_dst_y + $this->image_reflection_space));
							$pixel_o = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $this->image_dst_y - $y - 1 + ($this->image_reflection_space < 0 ? $this->image_reflection_space : 0)));
							$alpha_o = 1 - ($pixel_o['alpha'] / 127);
							$alpha_b = 1 - ($pixel_b['alpha'] / 127);
							$opacity = $alpha_o * $transparency / 100;
							if($opacity > 0)
							{
								$red = round((($pixel_o['red'] * $opacity) + ($pixel_b['red']) * $alpha_b) / ($alpha_b + $opacity));
								$green = round((($pixel_o['green'] * $opacity) + ($pixel_b['green']) * $alpha_b) / ($alpha_b + $opacity));
								$blue = round((($pixel_o['blue'] * $opacity) + ($pixel_b['blue']) * $alpha_b) / ($alpha_b + $opacity));
								$alpha = ($opacity + $alpha_b);
								if($alpha > 1)
									$alpha = 1;
								$alpha = round((1 - $alpha) * 127);
								$color = imagecolorallocatealpha($tmp, $red, $green, $blue, $alpha);
								imagesetpixel($tmp, $x, $y + $this->image_dst_y + $this->image_reflection_space, $color);
							}
						}
						if($transparency > 0)
							$transparency = $transparency - ($this->image_reflection_opacity / $image_reflection_height);
					}
					$this->image_dst_y = $this->image_dst_y + $image_reflection_height + $this->image_reflection_space;
					$image_dst = $this->imagetransfer($tmp, $image_dst);
				}
				
				switch($this->image_convert)
				{
					case 'gif':
						if(imageistruecolor($image_dst))
						{
							$mask = array(array());
							for($x = 0; $x < $this->image_dst_x; $x++)
							{
								for($y = 0; $y < $this->image_dst_y; $y++)
								{
									$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
									$mask[$x][$y] = $pixel['alpha'];
								}
							}
							list($red, $green, $blue) = $this->getcolors($this->image_default_color);
							for($x = 0; $x < $this->image_dst_x; $x++)
							{
								for($y = 0; $y < $this->image_dst_y; $y++)
								{
									if($mask[$x][$y] > 0)
									{
										$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
										$alpha = ($mask[$x][$y] / 127);
										$pixel['red'] = round(($pixel['red'] * (1 - $alpha) + $red * ($alpha)));
										$pixel['green'] = round(($pixel['green'] * (1 - $alpha) + $green * ($alpha)));
										$pixel['blue'] = round(($pixel['blue'] * (1 - $alpha) + $blue * ($alpha)));
										$color = imagecolorallocate($image_dst, $pixel['red'], $pixel['green'], $pixel['blue']);
										imagesetpixel($image_dst, $x, $y, $color);
									}
								}
							}
							if(empty($this->image_background_color))
							{
								imagetruecolortopalette($image_dst, true, 255);
								$transparency = imagecolorallocate($image_dst, 254, 1, 253);
								imagecolortransparent($image_dst, $transparency);
								for($x = 0; $x < $this->image_dst_x; $x++)
								{
									for($y = 0; $y < $this->image_dst_y; $y++)
										if($mask[$x][$y] > 120)
											imagesetpixel($image_dst, $x, $y, $transparency);
								}
							}
							unset($mask);
						}
						break;
					case 'jpg':
					case 'bmp':
						list($red, $green, $blue) = $this->getcolors($this->image_default_color);
						$transparency = imagecolorallocate($image_dst, $red, $green, $blue);
						for($x = 0; $x < $this->image_dst_x; $x++)
						{
							for($y = 0; $y < $this->image_dst_y; $y++)
							{
								$pixel = imagecolorsforindex($image_dst, imagecolorat($image_dst, $x, $y));
								if($pixel['alpha'] == 127)
								{
									imagesetpixel($image_dst, $x, $y, $transparency);
								}
								else if($pixel['alpha'] > 0)
								{
									$alpha = ($pixel['alpha'] / 127);
									$pixel['red'] = round(($pixel['red'] * (1 - $alpha) + $red * ($alpha)));
									$pixel['green'] = round(($pixel['green'] * (1 - $alpha) + $green * ($alpha)));
									$pixel['blue'] = round(($pixel['blue'] * (1 - $alpha) + $blue * ($alpha)));
									$color = imagecolorclosest($image_dst, $pixel['red'], $pixel['green'], $pixel['blue']);
									imagesetpixel($image_dst, $x, $y, $color);
								}
							}
						}
						
						break;
				}
				$result = false;
				switch($this->image_convert)
				{
					case 'jpeg':
					case 'jpg':
						if( ! $return_mode)
							$result = @imagejpeg($image_dst, $this->file_dst_pathname, $this->jpeg_quality);
						else
						{
							ob_start();
							$result = @imagejpeg($image_dst, '', $this->jpeg_quality);
							$return_content = ob_get_contents();
							ob_end_clean();
						}
						break;
					case 'png':
						imagealphablending($image_dst, false);
						imagesavealpha($image_dst, true);
						if( ! $return_mode)
							$result = @imagepng($image_dst, $this->file_dst_pathname);
						else
						{
							ob_start();
							$result = @imagepng($image_dst);
							$return_content = ob_get_contents();
							ob_end_clean();
						}
						break;
					case 'gif':
						if( ! $return_mode)
							$result = @imagegif($image_dst, $this->file_dst_pathname);
						else
						{
							ob_start();
							$result = @imagegif($image_dst);
							$return_content = ob_get_contents();
							ob_end_clean();
						}
						break;
					case 'bmp':
						if( ! $return_mode)
							$result = $this->imagebmp($image_dst, $this->file_dst_pathname);
						else
						{
							ob_start();
							$result = $this->imagebmp($image_dst);
							$return_content = ob_get_contents();
							ob_end_clean();
						}
						break;
				}
				if( ! $result)
					return false;
				if(is_resource($image_src))
					imagedestroy($image_src);
				if(is_resource($image_dst))
					imagedestroy($image_dst);
			}
		
		}
		else
		{
			if( ! $return_mode)
			{
				if( ! copy($this->file_src_pathname, $this->file_dst_pathname))
					return false;
			}
			else
			{
				$return_content = @file_get_contents($this->file_src_pathname);
				if($return_content === false)
					return false;
			}
		}
		if($return_mode)
			return $return_content;
		return true;
	}
	
	function getcolors($color)
	{
		$r = sscanf($color, "#%2x%2x%2x");
		$red = (array_key_exists(0, $r) && is_numeric($r[0]) ? $r[0] : 0);
		$green = (array_key_exists(1, $r) && is_numeric($r[1]) ? $r[1] : 0);
		$blue = (array_key_exists(2, $r) && is_numeric($r[2]) ? $r[2] : 0);
		return array($red, $green, $blue);
	}
	
	function clean()
	{
		@unlink($this->file_src_pathname);
	}
	
	function imagecreatefrombmp($filename)
	{
		if( ! $f1 = fopen($filename, "rb"))
			return false;
		
		$file = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
		if($file['file_type'] != 19778)
			return false;
		
		$bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
		$bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
		if($bmp['size_bitmap'] == 0)
			$bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
		$bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
		$bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
		$bmp['decal'] = ($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
		$bmp['decal'] -= floor($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
		$bmp['decal'] = 4 - (4 * $bmp['decal']);
		if($bmp['decal'] == 4)
			$bmp['decal'] = 0;
		
		$palette = array();
		if($bmp['colors'] < 16777216)
			$palette = unpack('V' . $bmp['colors'], fread($f1, $bmp['colors'] * 4));
		$im = fread($f1, $bmp['size_bitmap']);
		$vide = chr(0);
		
		$res = imagecreatetruecolor($bmp['width'], $bmp['height']);
		$p = 0;
		$Y = $bmp['height'] - 1;
		while($Y >= 0)
		{
			$X = 0;
			while($X < $bmp['width'])
			{
				if($bmp['bits_per_pixel'] == 24)
					$color = unpack("V", substr($im, $p, 3) . $vide);
				elseif($bmp['bits_per_pixel'] == 16)
				{
					$color = unpack("n", substr($im, $p, 2));
					$color[1] = $palette[$color[1] + 1];
				}
				elseif($bmp['bits_per_pixel'] == 8)
				{
					$color = unpack("n", $vide . substr($im, $p, 1));
					$color[1] = $palette[$color[1] + 1];
				}
				elseif($bmp['bits_per_pixel'] == 4)
				{
					$color = unpack("n", $vide . substr($im, floor($p), 1));
					if(($p * 2) % 2 == 0)
						$color[1] = ($color[1] >> 4);
					else
						$color[1] = ($color[1] & 0x0F);
					$color[1] = $palette[$color[1] + 1];
				}
				elseif($bmp['bits_per_pixel'] == 1)
				{
					$color = unpack("n", $vide . substr($im, floor($p), 1));
					$k = ($p * 8) % 8;
					if($k == 0)
						$color[1] = $color[1] >> 7;
					elseif($k == 1)
						$color[1] = ($color[1] & 0x40) >> 6;
					elseif($k == 2)
						$color[1] = ($color[1] & 0x20) >> 5;
					elseif($k == 3)
						$color[1] = ($color[1] & 0x10) >> 4;
					elseif($k == 4)
						$color[1] = ($color[1] & 0x8) >> 3;
					elseif($k == 5)
						$color[1] = ($color[1] & 0x4) >> 2;
					elseif($k == 6)
						$color[1] = ($color[1] & 0x2) >> 1;
					elseif($k == 7)
						$color[1] = ($color[1] & 0x1);
					$color[1] = $palette[$color[1] + 1];
				}
				else
					return FALSE;
				imagesetpixel($res, $X, $Y, $color[1]);
				$X++;
				$p += $bmp['bytes_per_pixel'];
			}
			$Y--;
			$p += $bmp['decal'];
		}
		fclose($f1);
		return $res;
	}
	
	function imagebmp(&$im, $filename = "")
	{
		
		if( ! $im)
			return false;
		$w = imagesx($im);
		$h = imagesy($im);
		$result = '';
		if( ! imageistruecolor($im))
		{
			$tmp = imagecreatetruecolor($w, $h);
			imagecopy($tmp, $im, 0, 0, 0, 0, $w, $h);
			imagedestroy($im);
			$im = & $tmp;
		}
		
		$biBPLine = $w * 3;
		$biStride = ($biBPLine + 3) &  ~ 3;
		$biSizeImage = $biStride * $h;
		$bfOffBits = 54;
		$bfSize = $bfOffBits + $biSizeImage;
		
		$result .= substr('BM', 0, 2);
		$result .= pack('VvvV', $bfSize, 0, 0, $bfOffBits);
		$result .= pack('VVVvvVVVVVV', 40, $w, $h, 1, 24, 0, $biSizeImage, 0, 0, 0, 0);
		
		$numpad = $biStride - $biBPLine;
		for($y = $h - 1; $y >= 0; --$y)
		{
			for($x = 0; $x < $w; ++$x)
			{
				$col = imagecolorat($im, $x, $y);
				$result .= substr(pack('V', $col), 0, 3);
			}
			for($i = 0; $i < $numpad; ++$i)
				$result .= pack('C', 0);
		}
		
		if($filename == "")
			echo $result;
		else
		{
			$file = fopen($filename, "wb");
			fwrite($file, $result);
			fclose($file);
		}
		return true;
	}
}
?>