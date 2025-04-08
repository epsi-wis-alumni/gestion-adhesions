<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/transaction')]
final class AdminTransactionController extends AbstractController
{
    #[Route(name: 'app_admin_transaction_index')]
    public function index(
        Request $request,
        TransactionRepository $transactionRepository,
    ): Response
    {
        $perPage = $request->get('perPage', 50);
        $page = $request->get('page', 1);
        $totalTransactionCount = count($transactionRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
        ));
        $transactions = $transactionRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
            search: $request->get('search'),
            sort: $request->query->get('sort', 'createdAt'),
            order: $request->query->get('order', 'desc'),
        );
        $transactionCount = count($transactions);

        return $this->render('admin/transaction/index.html.twig', [
            'transactions' => $transactions,
            'pages' => ceil($transactionCount / $perPage),
            'page' => $page,
            'transaction_count' => $request->get('search') ? count($transactions).'/'.$totalTransactionCount : $totalTransactionCount,
        ]);
    }
}
