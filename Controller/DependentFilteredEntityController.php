<?php

namespace Resomedia\UsefulBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DependentFilteredEntityController
 * @package Resomedia\UsefulBundle\Controller
 */
class DependentFilteredEntityController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     * @Route("/secure/useful_dependent_filtered_entity", name="useful_dependent_filtered_entity")
     */
    public function getOptionsAction(Request $request)
    {
        $translator = $this->get('translator');

        $entity_alias = $request->get('entity_alias');
        $parent_id    = $request->get('parent_id');
        $empty_value  = $request->get('placeholder');
        $em = $this->getDoctrine()->getManager();

        $entities = $this->getParameter('shtumi.dependent_filtered_entities');
        $entity_inf = $entities[$entity_alias];

        if (false === $this->get('security.authorization_checker')->isGranted( $entity_inf['role'] )) {
            throw new AccessDeniedException();
        }

        $qb = $em->getRepository($entity_inf['class'])
                ->createQueryBuilder('e')
                ->where('e.' . $entity_inf['parent_property'] . ' = :parent_id')
                ->orderBy('e.' . $entity_inf['order_property'], $entity_inf['order_direction'])
                ->setParameter('parent_id', $parent_id);


        if (null !== $entity_inf['callback']) {
            $repository = $em->getRepository($entity_inf['class']);

            if (!method_exists($repository, $entity_inf['callback'])) {
                throw new \InvalidArgumentException(sprintf('Callback function "%s" in Repository "%s" does not exist.', $entity_inf['callback'], get_class($repository)));
            }

            $repository->$entity_inf['callback']($qb);
        }

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return new Response('<option value="">' . $translator->trans($entity_inf['no_result_msg']) . '</option>');
        }

        $html = '';
        if ($empty_value !== false)
            $html .= '<option value="">' . $translator->trans($empty_value) . '</option>';

        $getter =  $this->getGetterName($entity_inf['choice_label']);

        foreach($results as $result)
        {
            if ($entity_inf['choice_label'])
                $res = $result->$getter();
            else $res = (string)$result;

            $html = $html . sprintf("<option value=\"%d\">%s</option>",$result->getId(), $res);
        }

        return new Response($html);

    }

    /**
     * @param $property
     * @return string
     */
    private function getGetterName($property)
    {
        $name = "get";
        $name .= mb_strtoupper($property[0]) . substr($property, 1);

        while (($pos = strpos($name, '_')) !== false){
            $name = substr($name, 0, $pos) . mb_strtoupper(substr($name, $pos+1, 1)) . substr($name, $pos+2);
        }

        return $name;

    }
}
