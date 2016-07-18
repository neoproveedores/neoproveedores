<?php

namespace Component\Pagination;

/**
 * Pagina una listado de documentos.
 */
class DocumentPaginator extends ArrayPaginator
{
    /**
     * {@inheritdoc}
     */
    public function paginate($documents, $skip = 0, $limit = 0)
    {
        $limit = $limit ? $limit : self::LIMIT;
        $total = $documents->count();
        $items = $documents->skip($skip)->limit($limit);
        $more = $total - $skip - $limit;

        if ($more < 0) {
            $more = 0;
        }

        return [
            'skip' => $skip,
            'limit' => $limit,
            'total' => $total,
            'items' => array_values($items->toArray()),
            'more' => $more > $limit ? $limit : $more,
            'next' => $more ? $skip + $limit : null,
        ];
    }
}
