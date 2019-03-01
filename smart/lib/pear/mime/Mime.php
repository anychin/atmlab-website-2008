<?php
/**
 * The Mail_Mime class is used to create MIME E-mail messages
 *
 * The Mail_Mime class provides an OO interface to create MIME
 * enabled email messages. This way you can create emails that
 * contain plain-text bodies, HTML bodies, attachments, inline
 * images and specific headers.
 *
 * Compatible with PHP versions 4 and 5
 *
 * LICENSE: This LICENSE is in the BSD license style.
 * Copyright (c) 2002-2003, Richard Heyes <richard@phpguru.org>
 * Copyright (c) 2003-2006, PEAR <pear-group@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * - Neither the name of the authors, nor the names of its contributors 
 *   may be used to endorse or promote products derived from this 
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Mail
 * @package   Mail_Mime
 * @author    Richard Heyes  <richard@phpguru.org>
 * @author    Tomas V.V. Cox <cox@idecnet.com>
 * @author    Cipriano Groenendal <cipri@php.net>
 * @author    Sean Coates <sean@php.net>
 * @copyright 2003-2006 PEAR <pear-group@php.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   CVS: $Id: mime.php,v 1.81 2007/06/21 19:08:28 cipri Exp $
 * @link      http://pear.php.net/package/Mail_mime
 *
 *            This class is based on HTML Mime Mail class from
 *            Richard Heyes <richard@phpguru.org> which was based also
 *            in the mime_mail.class by Tobias Ratschiller <tobias@dnet.it>
 *            and Sascha Schumann <sascha@schumann.cx>
 */


class Pear_Mime_Mime
{
    public $_txtbody;

    public $_htmlbody;

    public $_mime;

    public $_multipart;

    public $_html_images = array();

    public $_parts = array();

    public $_build_params = array();

    public $_headers = array();

    public $_eol;


    public function __construct($crlf = "\r\n")
    {
        $this->_setEOL($crlf);
        $this->_build_params = array(
                                     'head_encoding' => 'quoted-printable',
                                     'text_encoding' => '7bit',
                                     'html_encoding' => 'quoted-printable',
                                     '7bit_wrap'     => 998,
                                     'html_charset'  => 'ISO-8859-1',
                                     'text_charset'  => 'ISO-8859-1',
                                     'head_charset'  => 'ISO-8859-1'
                                    );
    }

    function __wakeup()
    {
        $this->_setEOL($this->_eol);
    }


    function setTXTBody($data, $isfile = false, $append = false)
    {
        if (!$isfile) {
            if (!$append) {
                $this->_txtbody = $data;
            } else {
                $this->_txtbody .= $data;
            }
        } else {
            $cont = $this->_file2str($data);
            if ($cont === false)
                return $cont;
            if (!$append)
                $this->_txtbody = $cont;
            else
                $this->_txtbody .= $cont;
        }
        return true;
    }

    function setHTMLBody($data, $isfile = false)
    {
        if (!$isfile) {
            $this->_htmlbody = $data;
        } else {
            $cont = $this->_file2str($data);
            if ($cont === false)
                return $cont;
            $this->_htmlbody = $cont;
        }
        return true;
    }

    function addHTMLImage($file, $c_type='application/octet-stream',
                          $name = '', $isfile = true)
    {
        $filedata = ($isfile === true) ? $this->_file2str($file)
                                           : $file;
        if ($isfile === true) {
            $filename = ($name == '' ? $file : $name);
        } else {
            $filename = $name;
        }
        if ($filedata === false)
            return $filedata;
        $this->_html_images[] = array(
                                      'body'   => $filedata,
                                      'name'   => $filename,
                                      'c_type' => $c_type,
                                      'cid'    => md5(uniqid(time()))
                                     );
        return true;
    }

    function addAttachment($file,
                           $c_type      = 'application/octet-stream',
                           $name        = '',
                            $isfile     = true,
                           $encoding    = 'base64',
                           $disposition = 'attachment',
                           $charset     = '',
                            $language   = '',
                           $location    = '')
    {
        $filedata = ($isfile === true) ? $this->_file2str($file)
                                           : $file;
		if ($isfile === true) {
            // Force the name the user supplied, otherwise use $file
            $filename = (strlen($name)) ? $name : $file;
        } else {
            $filename = $name;
        }
        if (!strlen($filename)) {
            return false;
        }
        if ($filedata === false)
            return $filedata;
        
        $this->_parts[] = array(
                                'body'        => $filedata,
                                'name'        => $filename,
                                'c_type'      => $c_type,
                                'encoding'    => $encoding,
                                'charset'     => $charset,
                                'language'    => $language,
                                'location'    => $location,
                                'disposition' => $disposition
                               );
        return true;
    }

    function &_file2str($file_name)
    {
        if (!is_readable($file_name)) {
            return false;
        }
        if (!$fd = fopen($file_name, 'rb')) {
            return false;
        }
        $filesize = filesize($file_name);
        if ($filesize == 0) {
            $cont =  "";
        } else {
            if ($magic_quote_setting = get_magic_quotes_runtime()) {
                set_magic_quotes_runtime(0);
            }
            $cont = fread($fd, $filesize);
            if ($magic_quote_setting) {
                set_magic_quotes_runtime($magic_quote_setting);
            }
        }
        fclose($fd);
        return $cont;
    }

    function &_addTextPart(&$obj, $text)
    {
        $params['content_type'] = 'text/plain';
        $params['encoding']     = $this->_build_params['text_encoding'];
        $params['charset']      = $this->_build_params['text_charset'];
        if (is_object($obj)) {
        	$ret = $obj->addSubpart($text, $params);
            return $ret;
        } else {
        	$ret = new Pear_Mime_MimePart($text, $params);
            return $ret;
        }
    }

    function &_addHtmlPart(&$obj)
    {
    	$params['content_type'] = 'text/html';
        $params['encoding']     = $this->_build_params['html_encoding'];
        $params['charset']      = $this->_build_params['html_charset'];
        if (is_object($obj)) {
            $ret = $obj->addSubpart($this->_htmlbody, $params);
            return $ret;
        } else {
        	$ret = new Pear_Mime_MimePart($this->_htmlbody, $params);
            return $ret;
        }
    }

    function &_addMixedPart()
    {
    	$params                 = array();
        $params['content_type'] = 'multipart/mixed';
        
        //Create empty multipart/mixed Mail_mimePart object to return
        $ret = new Pear_Mime_MimePart('', $params);
        return $ret;
    }

    function &_addAlternativePart(&$obj)
    {
        $params['content_type'] = 'multipart/alternative';
        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            $ret = new Pear_Mime_MimePart('', $params);
            return $ret;
        }
    }

    function &_addRelatedPart(&$obj)
    {
        $params['content_type'] = 'multipart/related';
        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            $ret = new Pear_Mime_MimePart('', $params);
            return $ret;
        }
    }

    function &_addHtmlImagePart(&$obj, $value)
    {
        $params['content_type'] = $value['c_type'];
        $params['encoding']     = 'base64';
        $params['disposition']  = 'inline';
        $params['dfilename']    = $value['name'];
        $params['cid']          = $value['cid'];
        
        $ret = $obj->addSubpart($value['body'], $params);
        return $ret;
    
    }

    function &_addAttachmentPart(&$obj, $value)
    {
        $params['dfilename'] = $value['name'];
        $params['encoding']  = $value['encoding'];
        if ($value['charset']) {
            $params['charset'] = $value['charset'];
        }
        if ($value['language']) {
            $params['language'] = $value['language'];
        }
        if ($value['location']) {
            $params['location'] = $value['location'];
        }
        $params['content_type'] = $value['c_type'];
        $params['disposition']  = isset($value['disposition']) ? 
                                  $value['disposition'] : 'attachment';
        $ret = $obj->addSubpart($value['body'], $params);
        return $ret;
    }

    function getMessage(
                        $separation   = null, 
                        $build_params = null, 
                        $xtra_headers = null, 
                        $overwrite    = false
                       )
    {
        if ($separation === null) {
            $separation = MAIL_MIME_CRLF;
        }
        $body = $this->get($build_params);
        $head = $this->txtHeaders($xtra_headers, $overwrite);
        $mail = $head . $separation . $body;
        return $mail;
    }


    function &get($build_params = null)
    {
        if (isset($build_params)) {
            while (list($key, $value) = each($build_params)) {
                $this->_build_params[$key] = $value;
            }
        }
        
        if (isset($this->_headers['From'])){
            $domain = @strstr($this->_headers['From'],'@');
            //Bug #11381: Illegal characters in domain ID
            $domain = str_replace(array("<", ">", "&", "(", ")", " ", "\"", "'"), "", $domain);
            $domain = urlencode($domain);
            foreach($this->_html_images as $i => $img){
                $this->_html_images[$i]['cid'] = $this->_html_images[$i]['cid'] . $domain;
            }
        }
        
        if (count($this->_html_images) AND isset($this->_htmlbody)) {
            foreach ($this->_html_images as $key => $value) {
                $regex   = array();
                $regex[] = '#(\s)((?i)src|background|href(?-i))\s*=\s*(["\']?)' .
                            preg_quote($value['name'], '#') . '\3#';
                $regex[] = '#(?i)url(?-i)\(\s*(["\']?)' .
                            preg_quote($value['name'], '#') . '\1\s*\)#';

                $rep   = array();
                $rep[] = '\1\2=\3cid:' . $value['cid'] .'\3';
                $rep[] = 'url(\1cid:' . $value['cid'] . '\2)';

                $this->_htmlbody = preg_replace($regex, $rep, $this->_htmlbody);
                $this->_html_images[$key]['name'] = 
                    basename($this->_html_images[$key]['name']);
            }
        }

        $null        = null;
        $attachments = count($this->_parts)                 ? true : false;
        $html_images = count($this->_html_images)           ? true : false;
        $html        = strlen($this->_htmlbody)             ? true : false;
        $text        = (!$html AND strlen($this->_txtbody)) ? true : false;

        switch (true) {
        case $text AND !$attachments:
            $message =& $this->_addTextPart($null, $this->_txtbody);
            break;

        case !$text AND !$html AND $attachments:
            $message =& $this->_addMixedPart();
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $text AND $attachments:
            $message =& $this->_addMixedPart();
            $this->_addTextPart($message, $this->_txtbody);
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $html AND !$attachments AND !$html_images:
            if (isset($this->_txtbody)) {
                $message =& $this->_addAlternativePart($null);
                $this->_addTextPart($message, $this->_txtbody);
                $this->_addHtmlPart($message);
            } else {
                $message =& $this->_addHtmlPart($null);
            }
            break;

        case $html AND !$attachments AND $html_images:
            $message =& $this->_addRelatedPart($null);
            if (isset($this->_txtbody)) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $this->_addHtmlPart($alt);
            } else {
                $this->_addHtmlPart($message);
            }
            for ($i = 0; $i < count($this->_html_images); $i++) {
                $this->_addHtmlImagePart($message, $this->_html_images[$i]);
            }
            break;

        case $html AND $attachments AND !$html_images:
            $message =& $this->_addMixedPart();
            if (isset($this->_txtbody)) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $this->_addHtmlPart($alt);
            } else {
                $this->_addHtmlPart($message);
            }
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $html AND $attachments AND $html_images:
            $message =& $this->_addMixedPart();
            if (isset($this->_txtbody)) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $rel =& $this->_addRelatedPart($alt);
            } else {
                $rel =& $this->_addRelatedPart($message);
            }
            $this->_addHtmlPart($rel);
            for ($i = 0; $i < count($this->_html_images); $i++) {
                $this->_addHtmlImagePart($rel, $this->_html_images[$i]);
            }
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        }

        if (isset($message)) {
            $output = $message->encode();
            
            $this->_headers = array_merge($this->_headers,
                                          $output['headers']);
            $body = $output['body'];
            return $body;

        } else {
            $ret = false;
            return $ret;
        }
    }

    function &headers($xtra_headers = null, $overwrite = false)
    {
        // Content-Type header should already be present,
        // So just add mime version header
        $headers['MIME-Version'] = '1.0';
        if (isset($xtra_headers)) {
            $headers = array_merge($headers, $xtra_headers);
        }
        if ($overwrite) {
            $this->_headers = array_merge($this->_headers, $headers);
        } else {
            $this->_headers = array_merge($headers, $this->_headers);
        }

        $encodedHeaders = $this->_encodeHeaders($this->_headers);
        return $encodedHeaders;
    }

    function txtHeaders($xtra_headers = null, $overwrite = false)
    {
        $headers = $this->headers($xtra_headers, $overwrite);
        
        $ret = '';
        foreach ($headers as $key => $val) {
            $ret .= "$key: $val" . MAIL_MIME_CRLF;
        }
        return $ret;
    }

    function setSubject($subject)
    {
        $this->_headers['Subject'] = $subject;
    }

    function setFrom($email)
    {
        $this->_headers['From'] = $email;
    }

    function addCc($email)
    {
        if (isset($this->_headers['Cc'])) {
            $this->_headers['Cc'] .= ", $email";
        } else {
            $this->_headers['Cc'] = $email;
        }
    }

    function addBcc($email)
    {
        if (isset($this->_headers['Bcc'])) {
            $this->_headers['Bcc'] .= ", $email";
        } else {
            $this->_headers['Bcc'] = $email;
        }
    }

    function encodeRecipients($recipients)
    {
        $input = array("To" => $recipients);
        $retval = $this->_encodeHeaders($input);
        return $retval["To"] ;
    }

    function _encodeHeaders($input, $params = array())
    {
        
        $build_params = $this->_build_params;
        while (list($key, $value) = each($params)) {
            $build_params[$key] = $value;
        }
        //$hdr_name: Name of the heaer
        //$hdr_value: Full line of header value.
        //$hdr_value_out: The recombined $hdr_val-atoms, or the encoded string.
                
        $useIconv = true;        
        if (isset($build_params['ignore-iconv'])) {
            $useIconv = !$build_params['ignore-iconv'];
        }            
        foreach ($input as $hdr_name => $hdr_value) {
            if (preg_match('#([\x80-\xFF]){1}#', $hdr_value)) {
                if (function_exists('iconv_mime_encode') && $useIconv) {
                    $imePrefs = array();
                    if ($build_params['head_encoding'] == 'base64') {
                        $imePrefs['scheme'] = 'B';
                    } else {
                        $imePrefs['scheme'] = 'Q';
                    }
                    $imePrefs['input-charset']  = $build_params['head_charset'];
                    $imePrefs['output-charset'] = $build_params['head_charset'];
                    $imePrefs['line-length'] = 74;
                    $imePrefs['line-break-chars'] = "\r\n"; //Specified in RFC2047
                    
                    $hdr_value = iconv_mime_encode($hdr_name, $hdr_value, $imePrefs);
                    $hdr_value = preg_replace("#^{$hdr_name}\:\ #", "", $hdr_value);
                } elseif ($build_params['head_encoding'] == 'base64') {
                    //Base64 encoding has been selected.
                    //Base64 encode the entire string
                    $hdr_value = base64_encode($hdr_value);
                    
                    //Generate the header using the specified params and dynamicly 
                    //determine the maximum length of such strings.
                    //75 is the value specified in the RFC. The first -2 is there so 
                    //the later regexp doesn't break any of the translated chars.
                    //The -2 on the first line-regexp is to compensate for the ": "
                    //between the header-name and the header value
                    $prefix = '=?' . $build_params['head_charset'] . '?B?';
                    $suffix = '?=';
                    $maxLength = 75 - strlen($prefix . $suffix) - 2;
                    $maxLength1stLine = $maxLength - strlen($hdr_name) - 2;

                    //We can cut base4 every 4 characters, so the real max
                    //we can get must be rounded down.
                    $maxLength = $maxLength - ($maxLength % 4);
                    $maxLength1stLine = $maxLength1stLine - ($maxLength1stLine % 4);
                    
                    $cutpoint = $maxLength1stLine;
                    $hdr_value_out = $hdr_value;
                    $output = "";
                    while ($hdr_value_out) {
                        //Split translated string at every $maxLength
                        $part = substr($hdr_value_out, 0, $cutpoint);
                        $hdr_value_out = substr($hdr_value_out, $cutpoint);
                        $cutpoint = $maxLength;
                        //RFC 2047 specifies that any split header should 
                        //be seperated by a CRLF SPACE. 
                        if ($output) {
                            $output .=  "\r\n ";
                        }
                        $output .= $prefix . $part . $suffix;
                    }
                    $hdr_value = $output;
                } else {
                    //quoted-printable encoding has been selected

                    //Fix for Bug #10298, Ota Mares <om@viazenetti.de>
                    //Check if there is a double quote at beginning or end of
                    //the string to prevent that an open or closing quote gets 
                    //ignored because it is encapsuled by an encoding pre/suffix.
                    //Remove the double quote and set the specific prefix or 
                    //suffix variable so that we can concat the encoded string and
                    //the double quotes back together to get the intended string.
                    $quotePrefix = $quoteSuffix = '';
                    if ($hdr_value{0} == '"') {
                        $hdr_value = substr($hdr_value, 1);
                        $quotePrefix = '"';
                    }
                    if ($hdr_value{strlen($hdr_value)-1} == '"') {
                        $hdr_value = substr($hdr_value, 0, -1);
                        $quoteSuffix = '"';
                    }
                    
                    //Generate the header using the specified params and dynamicly 
                    //determine the maximum length of such strings.
                    //75 is the value specified in the RFC. The -2 is there so 
                    //the later regexp doesn't break any of the translated chars.
                    //The -2 on the first line-regexp is to compensate for the ": "
                    //between the header-name and the header value
                    $prefix = '=?' . $build_params['head_charset'] . '?Q?';
                    $suffix = '?=';
                    $maxLength = 75 - strlen($prefix . $suffix) - 2 - 1;
                    $maxLength1stLine = $maxLength - strlen($hdr_name) - 2;
                    $maxLength = $maxLength - 1;
                    
                    //Replace all special characters used by the encoder.
                    $search  = array('=',   '_',   '?',   ' ');
                    $replace = array('=3D', '=5F', '=3F', '_');
                    $hdr_value = str_replace($search, $replace, $hdr_value);
                    
                    //Replace all extended characters (\x80-xFF) with their
                    //ASCII values.
                    $hdr_value = preg_replace('#([\x80-\xFF])#e',
                        '"=" . strtoupper(dechex(ord("\1")))',
                        $hdr_value);

                    //This regexp will break QP-encoded text at every $maxLength
                    //but will not break any encoded letters.
                    $reg1st = "|(.{0,$maxLength1stLine}[^\=][^\=])|";
                    $reg2nd = "|(.{0,$maxLength}[^\=][^\=])|";
                    //Fix for Bug #10298, Ota Mares <om@viazenetti.de>
                    //Concat the double quotes and encoded string together
                    $hdr_value = $quotePrefix . $hdr_value . $quoteSuffix;
                    

                    $hdr_value_out = $hdr_value;
                    $realMax = $maxLength1stLine + strlen($prefix . $suffix);
                    if (strlen($hdr_value_out) >= $realMax) {
                        //Begin with the regexp for the first line.
                        $reg = $reg1st;
                        $output = "";
                        while ($hdr_value_out) {
                            //Split translated string at every $maxLength
                            //But make sure not to break any translated chars.
                            $found = preg_match($reg, $hdr_value_out, $matches);
                            
                            //After this first line, we need to use a different
                            //regexp for the first line.
                            $reg = $reg2nd;
                            
                            //Save the found part and encapsulate it in the
                            //prefix & suffix. Then remove the part from the
                            //$hdr_value_out variable.
                            if ($found) {
                                $part = $matches[0];
                                $len = strlen($matches[0]);
                                $hdr_value_out = substr($hdr_value_out, $len);
                            } else {
                                $part = $hdr_value_out;
                                $hdr_value_out = "";
                            }
                            
                            //RFC 2047 specifies that any split header should 
                            //be seperated by a CRLF SPACE
                            if ($output) {
                                $output .=  "\r\n ";
                            }
                            $output .= $prefix . $part . $suffix;
                        }
                        $hdr_value_out = $output;
                    } else {
                        $hdr_value_out = $prefix . $hdr_value_out . $suffix;
                    }
                    $hdr_value = $hdr_value_out;
                }
            }
            $input[$hdr_name] = $hdr_value;
        }
        return $input;
    }

    function _setEOL($eol)
    {
        $this->_eol = $eol;
        if (!defined('MAIL_MIME_CRLF')) {
            define('MAIL_MIME_CRLF', $this->_eol, true);
        }
    }
}