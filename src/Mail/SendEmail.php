<?php

namespace App\Mail;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;


class SendEmail {

    public const TO = 'emile00013@gmail.com';

    public function __Construct(MailerInterface $mailerInterface, UserRepository $utilisateurRepo) {

        $this->to = self::TO;
        $this->mailerInterface = $mailerInterface;
        $this->utilisateurRepo = $utilisateurRepo;

    }

    public function SendEmail(User $user): void
    {
        $now = new \DateTime();
        $dateLimit = $now->modify('+1 day');
        
        $verificationCode = bin2hex(random_bytes(64));
        $verificationCode = substr($verificationCode, 0, 64);

        $user->setVerificationCode($verificationCode);
        $user->setVerificationDate($dateLimit);
        $user->setVerfication(false);
        $this->utilisateurRepo->save($user, true);

        $email = (new Email())
        ->from(new Address($this->from))
        ->to(new Address($user->getEmail()))   
        ->subject('Vérification de l\'email')
        ->htmlTemplate('mail/verified_email.html.twig')
        ->context([
                'name' => $user->getNomUtilisateur(),
                'code_verification' => $verificationCode
        ]);

        $this->mailerInterface->send($email);

    }
}