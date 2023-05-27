<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationService
{

    public function paginate($query, $page = 1, $limit = 10)
    {
        $paginator = new Paginator($query);
        $totalItems = count($paginator);

        $paginator->getQuery()->setFirstResult($limit * ($page - 1))
        ->setMaxResults($limit);

        $pagesCount = ceil($totalItems / $limit);

        $results = [];
        
        foreach($paginator as $item){
            $results[] = $item;
        }

        return [
            'results'     => $results,
            'total_items' => $totalItems,
            'current_page'=> $page,
            'total_pages' => $pagesCount,

        ];
    }
}