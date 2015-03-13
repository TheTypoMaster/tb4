<?php

namespace TopBetta\admin\controllers;

use TopBetta\Services\Accounting\PromotionService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use View;
use Input;
use Redirect;

class PromotionController extends \BaseController {

	/**
	 * @var PromotionService
	 */
	private $promotionService;

	public function __construct(PromotionService $promotionService)
	{
		$this->promotionService = $promotionService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get("q", null);
		return View::make("admin::promotions.index", array(
			"promotions" => $this->promotionService->getPromotions($search),
			"search" => $search
		));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make("admin::promotions.create");
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$this->promotionService->createPromotion(Input::except("q"));
		} catch (ValidationException $e) {
			return Redirect::back()->with("errors", $e->getErrors());
		}

		return Redirect::to("admin/promotions");
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		return View::make("admin::promotions.edit", array(
			"promotion" => $this->promotionService->find($id),
			"search" => Input::get("q", "")
		));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$this->promotionService->updatePromotion($id, Input::except("q"));
		} catch (ValidationException $e) {
			return Redirect::back()->with("errors", $e->getErrors());
		}

		return Redirect::route('admin.promotions.index', array($id, "q"=>Input::get("q", "")))
			->with('flash_message', 'Saved!');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->promotionService->deletePromotion($id);

		return Redirect::route('admin.promotions.index', array($id, "q"=>Input::get("q", "")))
			->with('flash_message', 'Deleted!');
	}



}
