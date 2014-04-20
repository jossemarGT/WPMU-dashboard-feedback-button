<?php
/**
 * Static class with utils functions for a better template management and bring a DRY code.
 *
 * @package   mu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

class ViewManager {
	
	/**
	*
	* Partial render a template and returns the template as string.
	*
	* @param $filePath - include path to the template.
	* @param null $viewData - any data to be used within the template.
	* @return string - The template to be rendered.
	*
	*/
	public static function partialRender( $filePath, $viewData = null ) {
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
	public static function render( $filePath, $viewData = null ) {
		( $viewData ) ? extract( $viewData ) : null;
		include ( $filePath );
	}
}
?>