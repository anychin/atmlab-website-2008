<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Chuck Hagenbuch <chuck@horde.org>                            |
// +----------------------------------------------------------------------+
//
// $Id: Mail.php,v 1.20 2007/10/06 17:00:00 chagenbu Exp $

class Pear_Mail
{
    protected $sep = "\r\n";
    
    protected $_params = '';

    public static function factory($driver, $params = array())
    {
        if($driver == 'Mail')
        	return new self($params);
        $class = 'Pear_Mail_' . $driver;
        return new $class($params);
    }
    
	public function __construct($params = null)
    {
        if (is_array($params))
            $this->_params = join(' ', $params);
        else
            $this->_params = $params;
        
        if (defined('PHP_EOL'))
            $this->sep = PHP_EOL;
        else
            $this->sep = (strpos(PHP_OS, 'WIN') === false) ? "\n" : "\r\n";
    }

    public function send($recipients, $headers, $body)
    {
        if (!is_array($headers))
            throw new Exception('$headers must be an array');
        
        $result = $this->_sanitizeHeaders($headers);
        
        if (is_array($recipients))
			$recipients = implode(', ', $recipients);
        
        $subject = '';
        if (isset($headers['Subject'])) {
            $subject = $headers['Subject'];
            unset($headers['Subject']);
        }
        unset($headers['To']);

        list(, $text_headers) = self::prepareHeaders($headers);

        return mail($recipients, $subject, $body, $text_headers);
    }

	protected function _sanitizeHeaders(&$headers){
        foreach ($headers as $key => $value)
			$headers[$key] = preg_replace('=((<CR>|<LF>|0x0A/%0A|0x0D/%0D|\\n|\\r)\S).*=i', null, $value);
	}

    public function prepareHeaders($headers)
    {
        $lines = array();
        $from = null;

        foreach ($headers as $key => $value) {
            if (strcasecmp($key, 'From') === 0) {
                $parser = new Pear_Mail_RFC822();
                $addresses = $parser->parseAddressList($value, 'localhost', false);
                
                $from = $addresses[0]->mailbox . '@' . $addresses[0]->host;

                if (strstr($from, ' ')) {
                    return false;
                }

                $lines[] = $key . ': ' . $value;
            } elseif (strcasecmp($key, 'Received') === 0) {
                $received = array();
                if (is_array($value)) {
                    foreach ($value as $line) {
                        $received[] = $key . ': ' . $line;
                    }
                }
                else {
                    $received[] = $key . ': ' . $value;
                }
                $lines = array_merge($received, $lines);
            } else {
                if (is_array($value))
                    $value = implode(', ', $value);
                $lines[] = $key . ': ' . $value;
            }
        }

        return array($from, join($this->sep, $lines));
    }

    function parseRecipients($recipients)
    {
        if (is_array($recipients))
            $recipients = implode(', ', $recipients);
		$addresses = Pear_Mail_RFC822::parseAddressList($recipients, 'localhost', false);
        $recipients = array();
        if (is_array($addresses)) {
            foreach ($addresses as $ob) {
                $recipients[] = $ob->mailbox . '@' . $ob->host;
            }
        }
        return $recipients;
    }

}
