<?php
declare(strict_types=1);

namespace Encount\Sender;

use Cake\I18n\Time;
use Cake\Mailer\Email;
use Encount\Collector\EncountCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class Mail implements SenderInterface
{
    /**
     * send email
     *
     * @access public
     * @author sakuragawa
     */
    public function send($config, EncountCollector $collector)
    {
        $transport = Configure::read('Email.error.transport');

        $format = 'text';
        if ($config['mail']['html'] === true) {
            $format = 'html';
        }

        $from = Configure::read('Email.error.from');
        $to = Configure::read('Email.error.to');
        $subject = $this->subject($config, $collector);
        $body = $this->body($config, $collector);

        TransportFactory::setConfig(Configure::consume('EmailTransport'));
        $mailer = new Mailer();
        $mailer->setTransport($transport)
            ->setEmailFormat($format)
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->deliver($body);
    }

    /**
     * generate subject
     *
     * @access private
     * @author sakuragawa
     */
    private function subject($config, $collector)
    {
        $prefix = $config['mail']['prefix'];
        $date = Time::now()->format('Ymd H:i:s');

        $subject = $prefix . '[' . $date . '][' . strtoupper($collector->errorType) . '][' . $collector->url . '] ' . $collector->description;
        return $subject;
    }

    /**
     * generate body
     *
     * @access private
     * @author sakuragawa
     */
    private function body($config, $collector)
    {
        $html = $config['mail']['html'];
        if ($html === true) {
            return self::getHtml($collector);
        } else {
            return self::getText($collector);
        }
    }

    /**
     * get the body for text message
     *
     * @access private
     * @author sakuragawa
     */
    private function getText($collector)
    {
        $message = $collector->description;
        $params = $collector->requestParams;
        $trace = $collector->trace;
        $session = $collector->session;
        $file = $collector->file;
        $line = $collector->line;
        $context = $collector->context;

        $msg = [
            $message,
            $file . '(' . $line . ')',
            '',
            '-------------------------------',
            'Backtrace:',
            '-------------------------------',
            '',
            trim(print_r($trace, true)),
            '',
            '-------------------------------',
            'Request:',
            '-------------------------------',
            '',
            '* URL       : ' . $collector->url,
            '* Client IP : ' . $collector->ip,
            '* Referer   : ' . $collector->referer,
            '* Parameters: ' . trim(print_r($params, true)),
            '* Cake root : ' . APP,
            '',
            '-------------------------------',
            'Environment:',
            '-------------------------------',
            '',
            trim(print_r($_SERVER, true)),
            '',
            '-------------------------------',
            'Session:',
            '-------------------------------',
            '',
            trim(print_r($session, true)),
            '',
            '-------------------------------',
            'Cookie:',
            '-------------------------------',
            '',
            trim(print_r($_COOKIE, true)),
            '',
            '-------------------------------',
            'Context:',
            '-------------------------------',
            '',
            trim(print_r($context, true)),
            '',
            ];
        return join("\n", $msg);
    }

    /**
     * get the body for htmll message
     *
     * @access private
     * @author sakuragawa
     */
    private function getHtml($collector)
    {
        $message = $collector->description;
        $params = $collector->requestParams;
        $trace = $collector->trace;
        $session = $collector->session;
        $file = $collector->file;
        $line = $collector->line;
        $context = $collector->context;

        $msg = [
            '<p><strong>',
            $message,
            '</strong></p>',
            '<p>',
            $file . '(' . $line . ')',
            '</p>',
            '',
            '<h2>',
            'Backtrace:',
            '</h2>',
            '',
            '<pre>',
            self::dumper($trace),
            '</pre>',
            '',
            '<h2>',
            'Request:',
            '</h2>',
            '',
            '<h3>URL</h3>',
            $collector->url,
            '<h3>Client IP</h3>',
            $collector->ip,
            '<h3>Referer</h3>',
            env('HTTP_REFERER'),
            '<h3>Parameters</h3>',
            self::dumper($params),
            '<h3>Cake root</h3>',
            APP,
            '',
            '<h2>',
            'Environment:',
            '</h2>',
            '',
            self::dumper($_SERVER),
            '',
            '<h2>',
            'Session:',
            '</h2>',
            '',
            self::dumper($session),
            '',
            '<h2>',
            'Cookie:',
            '</h2>',
            '',
            self::dumper($_COOKIE),
            '',
            '<h2>',
            'Context:',
            '</h2>',
            '',
            self::dumper($context),
            '',
            ];
        return join("", $msg);
    }


    /**
     * generate message
     *
     * @access private
     * @author sakuragawa
     */
    private function dumper($obj)
    {
        ob_start();
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $handler = function ($obj) use ($cloner, $dumper) {
            $dumper->dump($cloner->cloneVar($obj));
        };
        call_user_func($handler, $obj);
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
}
