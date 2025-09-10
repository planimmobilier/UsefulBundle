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

        $like = match ($entity_inf['search']) {
            "begins_with" => $letters . '%',
            "ends_with" => '%' . $letters,
            "contains" => '%' . $letters . '%',
            default => throw new \Exception('Unexpected value of parameter "search"'),
        };

        $property = $entity_inf['choice_label'];

        if (isset($entity_inf['case_insensitive'])) {
            $where = 'WHERE LOWER(e.' . $property . ')';
            $where .= ' LIKE LOWER(:like)';
        } else {
            $where = 'WHERE e.' . $property;
            $where .= ' LIKE :like';

        }

        if (!empty($entity_inf['where'])) {
            $where .= ' AND e.' . $entity_inf['where'];
        }

        $results = $em->createQuery(
            'SELECT e.' . $property . '
             FROM ' . $entity_inf['class'] . ' e ' .
            $where . ' ' .
            'ORDER BY e.' . $property)
            ->setParameter('like', $like)
            ->setMaxResults($maxRows)
            ->getScalarResult();

        $res = [];
        foreach ($results AS $r) {
            $res[] = $r[$entity_inf['choice_label']];
        }

        return new Response(json_encode($res));

    }
}
