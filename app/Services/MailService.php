<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

/**
 * MailService class
 */
class MailService
{
    /**
     * Send email
     *
     * @param array $data
     *
     * @return bool
     */
    public static function send(array $data): bool
    {
        try {
            $transport = Transport::fromDsn(setting('mailer.dsn'));
            $mailer = new Mailer($transport);

            $toAddress = new Address($data['to_email'], $data['to_name'] ?? '');
            $fromAddress = new Address($data['from_email'], $data['from_name'] ?? '');

            $email = (new Email())
                ->to($toAddress)
                ->from($fromAddress)
                ->subject($data['subject'])
                ->text($data['text']);

            $mailer->send($email);
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
