<?php namespace TopBetta\Services\Validation;

use Validator as V;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class Validator {

	/**
	 * Perform validation
	 *
	 * @param $input
	 * @param $rules
	 *
	 * @return bool
	 * @throws ValidationException
	 */
	public function validate($input, $rules) {
		$validation = V::make($input, $rules);
		if ($validation->fails()) throw new ValidationException("Validation Failed", $validation->messages());

		return true;
	}

	/**
	 * Validate against default ruleset
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function validateForCreation($input) {
		return $this->validate($input, $this->mergeRules($this->rules, $this->createRules));
	}

	/**
	 * Validate against update ruleset
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function validateForUpdate($input) {
		return $this->validate($input, $this->mergeRules($this->rules, $this->updateRules));
	}

	/**
	 * @param array $rules1
	 * @param array $rules2
	 *
	 * @return array
	 */
	public function mergeRules(array $rules1, array $rules2) {

		foreach ($rules2 as $key => $rule) {
			if ( ! array_key_exists($key, $rules1)) {
				$rules1[$key] = '';
			}

			$rules1[$key] .= '|' . $rule;
		}

		return $rules1;
	}
}