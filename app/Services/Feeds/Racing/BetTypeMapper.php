<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/08/2015
 * Time: 10:47 AM
 */

namespace TopBetta\Services\Feeds\Racing;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetTypeMapper
{

    private $mapping = array(
        'W'  => BetTypeRepositoryInterface::TYPE_WIN,
        'P'  => BetTypeRepositoryInterface::TYPE_PLACE,
        'Q'  => BetTypeRepositoryInterface::TYPE_QUINELLA,
        'E'  => BetTypeRepositoryInterface::TYPE_EXACTA,
        'T'  => BetTypeRepositoryInterface::TYPE_TRIFECTA,
        'FF' => BetTypeRepositoryInterface::TYPE_FIRSTFOUR,
    );
    /**
     * @var
     */
    private $betTypeRepository;

    public function __construct(BetTypeRepositoryInterface $betTypeRepository)
    {
        $this->betTypeRepository = $betTypeRepository;
    }

    /**
     * Returns BetTypeModel given the bet type code from the data provider
     * @param $betType
     * @return \TopBetta\Models\BetTypeModel | null
     */
    public function getBetType($betType)
    {
        if (!array_get($this->mapping, $betType)) {
            return null;
        }

        return $this->betTypeRepository->getBetTypeByName($this->mapping[$betType]);
    }
}