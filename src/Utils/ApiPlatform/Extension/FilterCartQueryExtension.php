<?php

namespace App\Utils\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Cart;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class FilterCartQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param string|null $operationName
     * @return void
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array $identifiers
     * @param string|null $operationName
     * @param array $context
     * @return void
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     * @return void
     */
    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Cart::class !== $resourceClass) {
            return;
        }
        
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $request = Request::createFromGlobals();
        $phpSessionId = $request->cookies->get('PHPSESSID');

        $queryBuilder->andWhere(
            sprintf("%s.sessionId = '%s'", $rootAlias, $phpSessionId)
        );

    }

}