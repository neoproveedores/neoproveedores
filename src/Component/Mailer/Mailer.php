<?php

namespace Component\Mailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Envargado de enviar emails al pueblo llano
 */
class Mailer
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected $templating;

    /**
     * @var string
     */
    protected $config;

    /**
     * @param Swift_Mailer $mailer
     * @param TwigEngine   $templating
     * @param array        $config
     */
    public function __construct($mailer, $templating, $config)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->config = $config;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return boolean
     */
    public function send($to, $subject, $body)
    {
        $message = \Swift_Message::newInstance();

        $message
            ->setTo($to)
            ->setSubject($subject)
            ->setFrom($this->config['sender'])
            ->setBody($body, 'text/html')
        ;

        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $template
     * @param array  $data
     * @return boolean
     */
    public function sendTemplate($to, $subject, $template, $data = [])
    {
        $converter = new CssToInlineStyles();
        $template = sprintf('mail/%s.html.twig', $template);
        $html = $this->templating->render($template, $data);
        $css = file_get_contents(__DIR__.'/../../../web/styles/mail.css');
        $body = $converter->convert($html, $css);

        return $this->send($to, $subject, $body);
    }
}
