<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminTransactionController extends AbstractController
{
    #[Route('/admin/transaction', name: 'app_admin_transaction_index')]
    public function index(
        Request $request,
        TransactionRepository $transactionRepository,
    ): Response {
        $perPage = $request->get('perPage', 50);
        $page = $request->get('page', 1);

        // Search for transactions
        $transactions = $transactionRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
            search: $request->get('search'),
            sort: $request->query->get('sort', 'createdAt'),
            order: $request->query->get('order', 'desc'),
        );

        // Count transactions
        $transactionTotalCount = $transactionRepository->count();
        $transactionMatchingSearchCount = $request->get('search')
            ? $transactionRepository->countBySearch($request->get('search', ''))
            : $transactionTotalCount
        ;
        $transactionCurrentPageCount = count($transactions);

        return $this->render('admin/transaction/index.html.twig', [
            'transactions' => $transactions,
            'pages' => ceil($transactionMatchingSearchCount / $perPage),
            'page' => $page,
            'transaction_count' => $request->get('search') ? $transactionCurrentPageCount.'/'.$transactionMatchingSearchCount : $transactionMatchingSearchCount,
        ]);
    }
}
