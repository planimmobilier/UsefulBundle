<?php

namespace Resomedia\UsefulBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('resomedia_useful');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('autocomplete_entities')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('choice_label')
                                ->defaultValue('title')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('role')->end()
                            ->arrayNode('searches')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('label')
                                            ->defaultValue('title')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->enumNode('search')
                                            ->values(['begins_with', 'ends_with', 'contains'])
                                            ->defaultValue('begins_with')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->enumNode('searches_operator')
                                ->values(['OR', 'AND'])
                                ->defaultValue('OR')
                            ->end()
                            ->scalarNode('where')->end()
			                ->booleanNode('case_insensitive')
                                 ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
