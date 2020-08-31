<?php

namespace DataParser;

use DataParser\Helper;
/**
 *

 */
class Handler
{

	private $config;

	function __construct($config=[])
	{
		$this->helper = new Helper;
		$result = require DATA_PARSE_CONFIG;
		$this->config = array_merge($result, $config);
	}

	function __call($rule, $args) {
		$data = $this->helper->arrayGet($args, 0, []);
		$type = $this->helper->arrayGet($args, 1);

		return $this->run($data, $rule, $type);
	}

	private function run($data, $rule, $type=null)
	{
		$data = is_array($data) ? $data : [];
		$rule_path = $this->helper->arrayGet($this->config, $rule);
		$rules = require $rule_path;
		$result = null;
		switch ($type) {
			case 'MULTIPE':
				$result = [];
				foreach($data as $item) {
					$item = is_array($item) ? $item : [];
					$result[] = $this->handler($item, $rules);
				}
			break;
			default:
				$result = $this->handler($data, $rules);
			break;
		}
		$result = json_decode(json_encode($result));
		return $result;
	}

	private function handler($data, $rules)
	{
		$result = [];
		foreach($rules as $rule) {
			$type = $this->helper->arrayGet($rule, 'type');
			switch($type) {
				case 'number':
					$result[$rule["name"]] = 0;
				break;
				case 'string':
					$result[$rule["name"]] = '';
				break;
				case 'multipe':
					$result[$rule["name"]] = [];
				break;
				default:
					$result[$rule["name"]] = empty($rule["rule"]) ? [] : $this->run([], $rule["rule"]);
				break;
			}
			foreach(explode('|', $rule["key"]) as $key) {
				$value = $this->helper->arrayGet($data, $key, null);
				if($value !== null) {
					switch($type) {
						case 'number':
							$result[$rule["name"]] = is_array($value) || is_object($value) ? '' : (int)$value;
						break;
						case 'string':
							$result[$rule["name"]] = is_array($value) || is_object($value) ? '' : $value;
						break;
						case 'multipe':
							if(!empty($rule["decode"])) {
								$value = json_decode(base64_decode($value), true);
							}
							$result[$rule["name"]] = empty($rule["rule"]) ? (is_array($value) ? $value : []) : $this->run($value, $rule["rule"], 'MULTIPE');
						break;
						default:
							if(!empty($rule["decode"])) {
								$value = json_decode(base64_decode($value), true);
							}
							$result[$rule["name"]] = empty($rule["rule"]) ? (is_array($value) ? $value : []) : $this->run($value, $rule["rule"]);
						break;
					}
				}
			}
		}
		return $result;
	}
}
