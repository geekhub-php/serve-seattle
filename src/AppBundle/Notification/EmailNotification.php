<?php

namespace AppBundle\Notification;

class EmailNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    private $from;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function sendNotification($email, $title = 'Hello!', $content = ' ')
    {
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject('Hello Email')
            ->setFrom($this->from)
            ->setTo($email)
            ->setBody($this->twig->render('AppBundle:Email:email.html.twig', [
                'title' => $title,
                'content' => $content
            ]),
                'text/html');
        $this->mailer->send($message);
    }
}
