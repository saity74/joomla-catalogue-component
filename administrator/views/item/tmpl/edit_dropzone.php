<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2015 Saity74 LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- Dropzone template -->
<ul id="template-container" style="display: none">
	<li class="dz-preview dz-file-preview image_tooltip_open">
		<a href="#" class="close"><span data-dz-remove>×</span></a>
		<div class="dz-details">
			<img data-dz-thumbnail />
			<div class="dz-filename"><span data-dz-name></span></div>
			<div class="dz-filesize"><span data-dz-size></span></div>
		</div>
		<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
		<div class="dz-success-mark">
			<svg xmlns="http://www.w3.org/2000/svg" width="54" height="54">
				<path fill="#73D0F4" fill-opacity=".8" fill-rule="evenodd" d="M23.5 31.8l-6-6c-1.5-1.4-4-1.4-5.6 0-1.7 1.7-1.7 4.2 0 5.8l8.3 8.5c0 .3.2.4.3.4 1.6 1.6 4 1.6 5.6 0l17-17c1.6-1.5 1.6-4 0-5.6-1.5-1.6-4-1.6-5.6 0l-14.2 14zM27 53c14.4 0 26-11.6 26-26S41.4 1 27 1 1 12.6 1 27s11.6 26 26 26z"/>
			</svg>
		</div>
		<div class="dz-error-mark">
			<svg xmlns="http://www.w3.org/2000/svg" width="54" height="54">
				<path fill="#D32F2F" fill-opacity=".8" fill-rule="evenodd" d="M32.7 29l5.6-5.7c1.6-1.5 1.6-4 0-5.6-1.5-1.6-4-1.6-5.6 0L27 23.3l-5.7-5.6c-1.5-1.6-4-1.6-5.6 0-1.6 1.5-1.6 4 0 5.6l5.6 5.7-5.6 5.7c-1.6 1.5-1.6 4 0 5.6 1.5 1.6 4 1.6 5.6 0l5.7-5.6 5.7 5.6c1.5 1.6 4 1.6 5.6 0 1.6-1.5 1.6-4 0-5.6L32.7 29zM27 53c14.4 0 26-11.6 26-26S41.4 1 27 1 1 12.6 1 27s11.6 26 26 26z"/>
			</svg>
		</div>
		<div class="dz-error-message"><span data-dz-errormessage></span></div>
		<input type="hidden" class="filename" name="jform[images][name][]" />
		<input type="hidden" class="filesize" name="jform[images][size][]" />
		<input type="hidden" data-attr="title" class="title editable" name="jform[images][title][]" />
		<input type="hidden" data-attr="alt" class="alt editable" name="jform[images][alt][]" />
		<input type="hidden" data-attr="color" class="color editable" name="jform[images][color][]" />
		<input type="hidden" data-attr="color_name" class="color_name editable" name="jform[images][color_name][]" />
	</li>
</ul>
