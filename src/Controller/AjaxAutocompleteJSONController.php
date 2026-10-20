<?php

namespace Resomedia\UsefulBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;


/**
 * Class AjaxAutocompleteJSONController
 * @package Resomedia\UsefulBundle\Controller
 */
class AjaxAutocompleteJSONController extends AbstractController
{
    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws \Exception
     */
    #[Route('/useful_ajaxautocomplete', name: 'useful_ajaxautocomplete')]
    public function getJSONAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $entities = $this->getParameter('useful.autocomplete_entities');

        $entity_alias = $request->get('entity_alias');
        $entity_inf = $entities[$entity_alias];

        if (array_key_exists('role', $entity_inf) && $entity_inf['role'] !== null)
            $this->denyAccessUnlessGranted($entity_inf['role'], null, 'Unable to access this page!');

        $letters = $request->get('letters');
        $maxRows = $request->get('maxRows');

        $property = $entity_inf['choice_label'];

        $searches = $entity_inf['searches'] ?? [];
        if (empty($searches)) {
            $searches = [['label' => $property, 'search' => 'begins_with']];
        }

        $conditions = [];
        $parameters = [];
        foreach ($searches as $i => $search) {
            $parameters['like' . $i] = match ($search['search']) {
                "begins_with" => $letters . '%',
                "ends_with" => '%' . $letters,
                "contains" => '%' . $letters . '%',
                default => throw new \Exception('Unexpected value of parameter "search"'),
            };

            if (!empty($entity_inf['case_insensitive'])) {
                $conditions[] = 'LOWER(e.' . $search['label'] . ') LIKE LOWER(:like' . $i . ')';
            } else {
                $conditions[] = 'e.' . $search['label'] . ' LIKE :like' . $i;
            }
        }

        $operator = $entity_inf['searches_operator'] ?? 'OR';
        $where = 'WHERE (' . implode(' ' . $operator . ' ', $conditions) . ')';

        if (!empty($entity_inf['where'])) {
            $where .= ' AND e.' . $entity_inf['where'];
        }

        $query = $em->createQuery(
            'SELECT e.' . $property . '
             FROM ' . $entity_inf['class'] . ' e ' .
            $where . ' ' .
            'ORDER BY e.' . $property)
            ->setMaxResults($maxRows);

        foreach ($parameters as $name => $value) {
            $query->setParameter($name, $value);
        }

        $results = $query->getScalarResult();

        $res = [];
        foreach ($results AS $r) {
            $res[] = $r[$entity_inf['choice_label']];
        }

        return new Response(json_encode($res));

    }
}
