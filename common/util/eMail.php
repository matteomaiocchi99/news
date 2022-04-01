<?php

class eMail
{
    public static function sendMail($receiver,$subject, $content)
    {
        Yii::$app->mailer->compose( null)
            ->setFrom(Yii::$app->params["senderEmail"])
            ->setTo("$receiver")
            ->setSubject($subject)
            ->setHtmlBody($content)
            ->send();
    }
}