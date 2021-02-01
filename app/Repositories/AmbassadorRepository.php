<?php

namespace App\Repositories;

use App\Models\Ambassador;

class AmbassadorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->queryBuilder = (new Ambassador())->query();
    }

    public function getPaginatedBy(int $length, ?string $sortBy, ?string $orderBy, ?array $searchValues)
    {
        $queryBuilder = parent::getBy($sortBy, $orderBy, $searchValues);
        $queryBuilder->select(
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
        );

        if (isset($searchValues['rank'])) {
            $queryBuilder->where('rank_id', $searchValues['rank']);
        }

        if (isset($searchValues['search'])) {
            $queryBuilder->where("xxxxxxxxxxxx", "LIKE", "%{$searchValues['search']}%")
                ->orWhere('xxxxxxxxxxxx', "LIKE", "%{$searchValues['search']}%")
                ->orWhere('xxxxxxxxxxxx', "LIKE", "%{$searchValues['search']}%")
                ->orWhere('xxxxxxxxxxxx', "LIKE", "%{$searchValues['search']}%")
                ->orWhere('xxxxxxxxxxxx', "LIKE", "%{$searchValues['search']}%")
                ->orWhere('xxxxxxxxxxxx', "LIKE", "%{$searchValues['search']}%");
        }

        return $queryBuilder->paginate($length);
    }

    public function getOne(string $ambassadorId)
    {
        return $this->queryBuilder->select(
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
            'xxxxxxxxxxxx',
        )->find($ambassadorId);
    }
}
