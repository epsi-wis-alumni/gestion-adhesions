<?php

namespace App\Service;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Stripe\Checkout\Session;
use Stripe\Invoice;

final readonly class InvoiceManager
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
        $directory = $this->params->get('invoice_base_path');
        $invoice_id = $transaction->getInvoice()->getInvoiceId();

        $filename = "invoice_$invoice_id.pdf";

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

    function generateInvoiceId(): string
    {
        $uniquePart = strtoupper(bin2hex(random_bytes(4)));
        $randomNumber = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        return sprintf('%s-%s', $uniquePart, $randomNumber);
    }

    public function sendInvoiceLink(
        Transaction $transaction,
    ): void {
        $session = Session::retrieve($transaction->getSessionId());
        $invoice = Invoice::retrieve($session->invoice);
        $invoice_url = $invoice->hosted_invoice_url;
        $user = $transaction->getUser();

        $this->notificationManager->sendInvoiceLink($invoice_url, $user);
    }
}
