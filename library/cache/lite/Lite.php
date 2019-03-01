<?php
define('CACHE_LITE_ERROR_RETURN', 1);
define('CACHE_LITE_ERROR_DIE', 8);

class Cache_Lite
{

    var $_cacheDir = '/tmp/';
    var $_caching = true;
    var $_lifeTime = 3600;
    var $_fileLocking = true;
    var $_refreshTime;
    var $_file;
    var $_fileName;
    var $_writeControl = true;
    var $_readControl = true;
    var $_readControlType = 'crc32';
    var $_pearErrorMode = CACHE_LITE_ERROR_RETURN;
    var $_id;
    var $_group;
    var $_memoryCaching = false;
    var $_onlyMemoryCaching = false;
    var $_memoryCachingArray = array();
    var $_memoryCachingCounter = 0;
    var $_memoryCachingLimit = 1000;
    var $_fileNameProtection = true;
    var $_automaticSerialization = false;
    var $_automaticCleaningFactor = 0;
    var $_hashedDirectoryLevel = 0;
    var $_hashedDirectoryUmask = 0700;
    var $_errorHandlingAPIBreak = false;
    function Cache_Lite($options = array(NULL))
    {
        foreach($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }
    function setOption($name, $value) 
    {
        $availableOptions = array('errorHandlingAPIBreak', 'hashedDirectoryUmask', 'hashedDirectoryLevel', 'automaticCleaningFactor', 'automaticSerialization', 'fileNameProtection', 'memoryCaching', 'onlyMemoryCaching', 'memoryCachingLimit', 'cacheDir', 'caching', 'lifeTime', 'fileLocking', 'writeControl', 'readControl', 'readControlType', 'pearErrorMode');
        if (in_array($name, $availableOptions)) {
            $property = '_'.$name;
            $this->$property = $value;
        }
    }
    function get($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        $this->_id = $id;
        $this->_group = $group;
        $data = false;
        if ($this->_caching) {
            $this->_setRefreshTime();
            $this->_setFileName($id, $group);
            clearstatcache();
            if ($this->_memoryCaching) {
            	if (isset($this->_memoryCachingArray[$this->_file])) {
                    if ($this->_automaticSerialization) {
                        return unserialize($this->_memoryCachingArray[$this->_file]);
                    }
                    return $this->_memoryCachingArray[$this->_file];
                }
                if ($this->_onlyMemoryCaching) {
                    return false;
                }                
            }
            if (($doNotTestCacheValidity) || (is_null($this->_refreshTime))) {
                if (file_exists($this->_file)) {
                    $data = $this->_read();
                }
            } else {
                if ((file_exists($this->_file)) && (@filemtime($this->_file) > $this->_refreshTime)) {
					$data = $this->_read();
				}
            }
            if (($data) and ($this->_memoryCaching)) {
                $this->_memoryCacheAdd($data);
            }
            if (($this->_automaticSerialization) and (is_string($data))) {
                $data = unserialize($data);
            }
            return $data;
        }
        return false;
    }
    function save($data, $id = NULL, $group = 'default')
    {
        if ($this->_caching) {
            if ($this->_automaticSerialization) {
                $data = serialize($data);
            }
            if (isset($id)) {
                $this->_setFileName($id, $group);
            }
            if ($this->_memoryCaching) {
                $this->_memoryCacheAdd($data);
                if ($this->_onlyMemoryCaching) {
                    return true;
                }
            }
            if ($this->_automaticCleaningFactor>0 && ($this->_automaticCleaningFactor==1 || mt_rand(1, $this->_automaticCleaningFactor)==1)) {
				$this->clean(false, 'old');			
			}
            if ($this->_writeControl) {
                $res = $this->_writeAndControl($data);
                if (is_bool($res)) {
                    if ($res) {
                        return true;  
                    }
                    // if $res if false, we need to invalidate the cache
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                }            
            } else {
                $res = $this->_write($data);
            }
            if (is_object($res)) {
                // $res is a PEAR_Error object 
                if (!($this->_errorHandlingAPIBreak)) {   
                    return false; // we return false (old API)
                }
            }
            return $res;
        }
        return false;
    }

    function remove($id, $group = 'default', $checkbeforeunlink = false)
    {
        $this->_setFileName($id, $group);
        if ($this->_memoryCaching) {
            if (isset($this->_memoryCachingArray[$this->_file])) {
                unset($this->_memoryCachingArray[$this->_file]);
                $this->_memoryCachingCounter = $this->_memoryCachingCounter - 1;
            }
            if ($this->_onlyMemoryCaching) {
                return true;
            }
        }
        if ( $checkbeforeunlink ) {
            if (!file_exists($this->_file)) return true;
        }
        return $this->_unlink($this->_file);
    }

    function clean($group = false, $mode = 'ingroup')
    {
        return $this->_cleanDir($this->_cacheDir, $group, $mode);
    }
         function setToDebug()
    {
        $this->setOption('pearErrorMode', CACHE_LITE_ERROR_DIE);
    }

    function setLifeTime($newLifeTime)
    {
        $this->_lifeTime = $newLifeTime;
        $this->_setRefreshTime();
    }

    function saveMemoryCachingState($id, $group = 'default')
    {
        if ($this->_caching) {
            $array = array(
                'counter' => $this->_memoryCachingCounter,
                'array' => $this->_memoryCachingArray
            );
            $data = serialize($array);
            $this->save($data, $id, $group);
        }
    }

    function getMemoryCachingState($id, $group = 'default', $doNotTestCacheValidity = false)
    {
        if ($this->_caching) {
            if ($data = $this->get($id, $group, $doNotTestCacheValidity)) {
                $array = unserialize($data);
                $this->_memoryCachingCounter = $array['counter'];
                $this->_memoryCachingArray = $array['array'];
            }
        }
    }
       function lastModified() 
    {
        return @filemtime($this->_file);
    }
   
    function raiseError($msg, $code)
    {
        include_once('PEAR.php');
        return PEAR::raiseError($msg, $code, $this->_pearErrorMode);
    }
    
    
    function extendLife()
    {
        @touch($this->_file);
    }
    
    
    function _setRefreshTime() 
    {
        if (is_null($this->_lifeTime)) {
            $this->_refreshTime = null;
        } else {
            $this->_refreshTime = time() - $this->_lifeTime;
        }
    }
    
    function _unlink($file)
    {
        if (!@unlink($file)) {
            return $this->raiseError('Cache_Lite : Unable to remove cache !', -3);
        }
        return true;        
    }

    
    function _cleanDir($dir, $group = false, $mode = 'ingroup')     
    {
        if ($this->_fileNameProtection) {
            $motif = ($group) ? 'cache_'.md5($group).'_' : 'cache_';
        } else {
            $motif = ($group) ? 'cache_'.$group.'_' : 'cache_';
        }
        if ($this->_memoryCaching) {
	    foreach($this->_memoryCachingArray as $key => $v) {
                if (strpos($key, $motif) !== false) {
                    unset($this->_memoryCachingArray[$key]);
                    $this->_memoryCachingCounter = $this->_memoryCachingCounter - 1;
                }
            }
            if ($this->_onlyMemoryCaching) {
                return true;
            }
        }
        if (!($dh = opendir($dir))) {
            return $this->raiseError('Cache_Lite : Unable to open cache directory !', -4);
        }
        $result = true;
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                if (substr($file, 0, 6)=='cache_') {
                    $file2 = $dir . $file;
                    if (is_file($file2)) {
                        switch (substr($mode, 0, 9)) {
                            case 'old':
                                // files older than lifeTime get deleted from cache
                                if (!is_null($this->_lifeTime)) {
                                    if ((time() - @filemtime($file2)) > $this->_lifeTime) {
                                        $result = ($result and ($this->_unlink($file2)));
                                    }
                                }
                                break;
                            case 'notingrou':
                                if (strpos($file2, $motif) === false) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                            case 'callback_':
                                $func = substr($mode, 9, strlen($mode) - 9);
                                if ($func($file2, $group)) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                            case 'ingroup':
                            default:
                                if (strpos($file2, $motif) !== false) {
                                    $result = ($result and ($this->_unlink($file2)));
                                }
                                break;
                        }
                    }
                    if ((is_dir($file2)) and ($this->_hashedDirectoryLevel>0)) {
                        $result = ($result and ($this->_cleanDir($file2 . '/', $group, $mode)));
                    }
                }
            }
        }
        return $result;
    }
      
    function _memoryCacheAdd($data)
    {
        $this->_memoryCachingArray[$this->_file] = $data;
        if ($this->_memoryCachingCounter >= $this->_memoryCachingLimit) {
            list($key, ) = each($this->_memoryCachingArray);
            unset($this->_memoryCachingArray[$key]);
        } else {
            $this->_memoryCachingCounter = $this->_memoryCachingCounter + 1;
        }
    }

    
    function _setFileName($id, $group)
    {
        
        if ($this->_fileNameProtection) {
            $suffix = 'cache_'.md5($group).'_'.md5($id);
        } else {
            $suffix = 'cache_'.$group.'_'.$id;
        }
        $root = $this->_cacheDir;
        if ($this->_hashedDirectoryLevel>0) {
            $hash = md5($suffix);
            for ($i=0 ; $i<$this->_hashedDirectoryLevel ; $i++) {
                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
            }   
        }
        $this->_fileName = $suffix;
        $this->_file = $root.$suffix;
    }
    
    
    function _read()
    {
        $fp = @fopen($this->_file, "rb");
        if ($this->_fileLocking) @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache();
            $length = @filesize($this->_file);
            $mqr = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if ($this->_readControl) {
                $hashControl = @fread($fp, 32);
                $length = $length - 32;
            } 
            if ($length) {
                $data = @fread($fp, $length);
            } else {
                $data = '';
            }
            set_magic_quotes_runtime($mqr);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($this->_readControl) {
                $hashData = $this->_hash($data, $this->_readControlType);
                if ($hashData != $hashControl) {
                    if (!(is_null($this->_lifeTime))) {
                        @touch($this->_file, time() - 2*abs($this->_lifeTime)); 
                    } else {
                        @unlink($this->_file);
                    }
                    return false;
                }
            }
            return $data;
        }
        return $this->raiseError('Cache_Lite : Unable to read cache !', -2); 
    }
    
    
    function _write($data)
    {
        if ($this->_hashedDirectoryLevel > 0) {
            $hash = md5($this->_fileName);
            $root = $this->_cacheDir;
            for ($i=0 ; $i<$this->_hashedDirectoryLevel ; $i++) {
                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
                if (!(@is_dir($root))) {
                    @mkdir($root, $this->_hashedDirectoryUmask);
                }
            }
        }
        $fp = @fopen($this->_file, "wb");
        if ($fp) {
            if ($this->_fileLocking) @flock($fp, LOCK_EX);
            if ($this->_readControl) {
                @fwrite($fp, $this->_hash($data, $this->_readControlType), 32);
            }
            $mqr = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            @fwrite($fp, $data);
            set_magic_quotes_runtime($mqr);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            return true;
        }      
        return $this->raiseError('Cache_Lite : Unable to write cache file : '.$this->_file, -1);
    }
       
    
    function _writeAndControl($data)
    {
        $result = $this->_write($data);
        if (is_object($result)) {
            return $result; # We return the PEAR_Error object
        }
        $dataRead = $this->_read();
        if (is_object($dataRead)) {
            return $dataRead; # We return the PEAR_Error object
        }
        if ((is_bool($dataRead)) && (!$dataRead)) {
            return false; 
        }
        return ($dataRead==$data);
    }
    
    function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            return $this->raiseError('Unknown controlType ! (available values are only \'md5\', \'crc32\', \'strlen\')', -5);
        }
    }
    
} 

?>
