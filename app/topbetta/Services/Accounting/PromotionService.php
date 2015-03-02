<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/03/2015
 * Time: 11:12 AM
 */

namespace TopBetta\Services\Accounting;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use TopBetta\Repositories\Contracts\PromotionRepositoryInterface;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class PromotionService {

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function getPromotions($search = null)
    {
        if($search) {
            return $this->promotionRepository->search($search);
        }

        return $this->promotionRepository->getAll();
    }

    public function find($id)
    {
        return $this->promotionRepository->find($id);
    }

    public function createPromotion($data)
    {
        $data['pro_entered_by'] = Auth::user()->id;
        $data['pro_entered_date'] = Carbon::now()->toDateTimeString();

        return $this->promotionRepository->create($data);
    }

    public function updatePromotion($id, $data)
    {
        //test for uniqueness of promo code when updating. Need to find a better way to do this!
        if($this->promotionRepository->getByPromoCode($data['pro_code'])->pro_id !== $id) {
            throw new ValidationException("Promotion code must be unique", new MessageBag(array("promotion code must be unique")));
        }

        $this->promotionRepository->validateUpdate($data);

        return $this->promotionRepository->updateWithId($id, $data);
    }

    public function deletePromotion($id)
    {
        $model = $this->promotionRepository->find($id);
        return $model->delete();
    }
}