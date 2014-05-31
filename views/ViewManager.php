<?php
/**
 * Static class with utils functions for a better template management and bring a DRY code.
 *
 * @package   wpmu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

if (! class_exists("ViewManager") ){

	class ViewManager {
		
		protected $base_view_dir = "";
		
		public function __construct( $base_view_dir = ""){
			$this->base_view_dir = $base_view_dir == "" ? plugin_dir_path(__FILE__) : $base_view_dir ;
		}

		/**
		*
		* Partial render a template and returns the template as string.
		*
		* @param $filePath - include path to the template.
		* @param null $viewData - any data to be used within the template.
		*
		* @return string - The template to be rendered.
		*
		*/
		public function partialRender( $filePath, $viewData = null ) {
			$filePath = $this->base_view_dir . $filePath;
			( $viewData ) ? extract( $viewData ) : null;
			ob_start();
			require ( $filePath );
			$template = ob_get_contents();
			ob_end_clean();
			return $template;
		}

		/**
		*
		* Render a template and echoes it.
		*
		* @param $filePath - include path to the template.
		* @param null $viewData - any data to be used within the template.
		*
		*/
		public function render( $filePath, $viewData = null, $from_views_dir = true ) {
			$filePath = $this->base_view_dir . $filePath;
			( $viewData ) ? extract( $viewData ) : null;
			include ( $filePath );
		}
	}
	
}
?>