<?php

namespace Resomedia\UsefulBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxAutocompleteJSONController
 * @package Resomedia\UsefulBundle\Controller
 */
class AjaxAutocompleteJSONController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     * @Route("/useful_ajaxautocomplete", name="useful_ajaxautocomplete")
     */
    public function getJSONAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $this->getParameter('useful.autocomplete_entities');

        $entity_alias = $request->get('entity_alias');
        $entity_inf = $entities[$entity_alias];

        $this->denyAccessUnlessGranted($entity_inf['role'], null, 'Unable to access this page!');

        $letters = $request->get('letters');
        $maxRows = $request->get('maxRows');

        switch ($entity_inf['search']) {
            case "begins_with":
                $like = $letters . '%';
                break;
            case "ends_with":
                $like = '%' . $letters;
                break;
            case "contains":
                $like = '%' . $letters . '%';
                break;
            default:
                throw new \Exception('Unexpected value of parameter "search"');
        }

        $property = $entity_inf['choice_label'];

        if (isset($entity_inf['case_insensitive'])) {
            $where = 'WHERE LOWER(e.' . $property . ')';
            $where .= ' LIKE LOWER(:like)';
        } else {
            $where = 'WHERE e.' . $property;
            $where .= ' LIKE :like';

        }

        if (isset($entity_inf['where']) && !empty($entity_inf['where'])) {
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

        $res = array();
        foreach ($results AS $r) {
            $res[] = $r[$entity_inf['choice_label']];
        }

        return new Response(json_encode($res));

    }
}
