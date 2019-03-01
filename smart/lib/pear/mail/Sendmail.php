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

/**
 * Sendmail implementation of the PEAR Mail:: interface.
 * @access public
 * @package Mail
 * @version $Revision: 1.19 $
 */
class Pear_Mail_Sendmail extends Pear_Mail {

    protected $sendmail_path = '/usr/sbin/sendmail';

    protected $sendmail_args = '-i';

    public function __construct($params)
    {
        if (isset($params['sendmail_path']))
            $this->sendmail_path = $params['sendmail_path'];
        if (isset($params['sendmail_args']))
            $this->sendmail_args = $params['sendmail_args'];
        if (defined('PHP_EOL'))
            $this->sep = PHP_EOL;
        else
            $this->sep = (strpos(PHP_OS, 'WIN') === false) ? "\n" : "\r\n";
    }

    function send($recipients, $headers, $body)
    {
        if (!is_array($headers))
            throw new Exception('$headers must be an array');
        
        $result = $this->_sanitizeHeaders($headers);
        
        $recipients = $this->parseRecipients($recipients);
        $recipients = escapeShellCmd(implode(' ', $recipients));

        $headerElements = $this->prepareHeaders($headers);
        if (!$headerElements)
            throw new Exception();
        list($from, $text_headers) = $headerElements;

        if (!empty($headers['Return-Path'])) {
            $from = $headers['Return-Path'];
        }

        if (!isset($from)) {
            throw new Exception();
        } elseif (strpos($from, ' ') !== false ||
                  strpos($from, ';') !== false ||
                  strpos($from, '&') !== false ||
                  strpos($from, '`') !== false) {
            throw new Exception('From address specified with dangerous characters.');
        }

        $from = escapeShellCmd($from);
        $mail = @popen($this->sendmail_path . (!empty($this->sendmail_args) ? ' ' . $this->sendmail_args : '') . " -f$from -- $recipients", 'w');
        if (!$mail) {
            error_log('Failed to open sendmail [' . $this->sendmail_path . '] for execution.');
        	return false;
        }

        fputs($mail, $text_headers . $this->sep . $this->sep);

        fputs($mail, $body);
        $result = pclose($mail);
        if ($result != 0)
            return false;
        
        return true;
    }

}
