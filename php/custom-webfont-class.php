<?php

if(!class_exists('HT_Custom_Webfont')){
	class HT_Custom_Webfont {

		function __construct($args){
			foreach($args as $key => $value){
		        $this->{$key} = $value;
		      }
		}
	}
}