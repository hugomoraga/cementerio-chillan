<?php

namespace MABEL_BHI_LITE\Models
{
	if(!defined('ABSPATH')){die;}

	class Location
	{

		public $name;

		public $vacations;

		public $specials;

		public $opening_hours;

		public function __construct($name = null){

			$this->name = $name;
			$this->opening_hours = array();
			$this->specials = array();
			$this->vacations = array();

		}

	}

}