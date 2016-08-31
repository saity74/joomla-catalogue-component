<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Catalogue.
 *
 * Provides an image upload field
 *
 * @since  11.1
 */
class JFormFieldDropimages extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Dropimages';

	/**
	 * The allowable src of img field.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $src;

	/**
	 * The allowable upload_url.
	 *
	 * @var    string
	 */
	protected $upload_url;

	/**
	 * JSON with images path.
	 *
	 * @var    string
	 */
	protected $images;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'upload_url':
			case 'images':
			case 'src':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'upload_url':
			case 'images':
			case 'src':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->src    = (string) $this->element['src'] ? (string) $this->element['src'] : '';
			$this->upload_url    = (string) $this->element['upload_url'] ? (string) $this->element['upload_url'] : '';
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components/com_catalogue/assets/css/dropzone.css')
			->addScript('components/com_catalogue/assets/js/dropzone.js')
			->addScript('components/com_catalogue/assets/js/jquery-ui.min.js')
			->addScript('components/com_catalogue/assets/js/init-dropzone.js');

		$item_id = $this->form->getData()->get('id', '0');

		$js = '
			jQuery(function($) {
				window.initDropzone(
					"' . $this->upload_url . '&item_id=' . $item_id . '",
					"' . ($this->value ? addslashes(json_encode($this->value)) : "{}") . '"
				);
			});
		';

		$doc->addScriptDeclaration($js);

		$html = '
			<ul id="imagesContainer" class="dropzone unstyled">
				<div class="dz-message">
					<div class="dz-message-label">
						' . JText::_('COM_CATALOGUE_DROP_IMAGE_TEXT') . '
					</div>
					<svg width="80px" height="80px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400.1 400.1">
						<path fill="#73D0F4" d="M137 121.5c6.7 0 12 5.4 12 12V288h102V133.6c0-6.6 5.4-12 12-12h32.7L200 45.2l-95.7 76.3H137z"/>
						<g fill="#3D6889">
							<path d="M70 145.5h55V300c0 6.7 5.4 12 12 12h126c6.6 0 12-5.3 12-12V145.6h55c6.7 0 12-5.4 12-12 0-4-2-7.5-5-9.7L207.6 20.5c-4.4-3.5-10.6-3.5-15 0L62.5 124c-4 3.3-5.5 8.7-3.8 13.5 1.7 4.8 6.2 8 11.3 8zm225.7-24H263c-6.6 0-12 5.4-12 12V288H149V133.6c0-6.6-5.3-12-12-12h-32.7L200 45.2l95.7 76.3z"/>
							<path d="M388 261.2c-6.6 0-12 5.3-12 12v85H24v-85c0-6.7-5.4-12-12-12s-12 5.3-12 12v97c0 6.6 5.4 12 12 12h376c6.7 0 12-5.4 12-12v-97c0-6.7-5.3-12-12-12z"/>
						</g>
					</svg>
				</div>
			</ul>
		';

		return $html;
	}
}
