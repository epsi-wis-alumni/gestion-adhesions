<?php

namespace App\Service;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class InvoiceManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationManager $notificationManager,
        private ContainerBagInterface $params,
    ) {
    }

    public function create(
        string $html,
        Transaction $transaction,
    ): void {
        $invoicePath = $this->generate($html, $transaction);
        $transaction->getInvoice()->setFilePath($invoicePath);

        $this->entityManager->flush();

        $invoice = $transaction->getInvoice();
        $users = [$transaction->getUser()];
        $this->notificationManager->sendNotification($invoice, $users);
    }

    public function generate(
        string $html,
        Transaction $transaction,
    ): string {
        $directory = __DIR__.$this->params->get('invoice_base_path');
        $filename = 'invoice_'.$transaction->getId().'.pdf';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $filePath = $directory.$filename;
        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }
}
