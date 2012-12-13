<?php

namespace ICanBoogie\I18n;

/**
 * A formatted string.
 *
 * The string is formatted by replacing placeholders with the values provided.
 */
class FormattedString
{
	protected $format;
	protected $args;
	protected $options;

	/**
	 * Initializes the {@link $format}, {@link $args} and {@link $options} properties.
	 *
	 * @param string $format String format.
	 * @param array $args Format arguments.
	 * @param array $options I18n options.
	 */
	public function __construct($format, $args=null, array $options=array())
	{
		if (!is_array($args))
		{
			$args = func_get_args();
			array_shift($args);
			$options = array();
		}

		$this->format = $format;
		$this->args = (array) $args;
		$this->options = $options;
	}

	/**
	 * Returns the string formatted with the {@link format()} function.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return t($this->format, $this->args, $this->options);
	}
}

namespace ICanBoogie;

class FormattedString extends I18n\FormattedString
{

}