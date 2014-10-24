<?php

namespace ICanBoogie\I18n;

use ICanBoogie\PropertyNotDefined;

/**
 * A formatted string.
 *
 * The string is formatted by replacing placeholders with the values provided.
 *
 * @property-read string $format String format.
 * @property-read array $args Format arguments.
 * @property-read array $options I18n options.
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
	public function __construct($format, $args=null, array $options=[])
	{
		if (!is_array($args))
		{
			$args = func_get_args();
			array_shift($args);
			$options = [];
		}

		$this->format = $format;
		$this->args = (array) $args;
		$this->options = $options;
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'format': return $this->format;
			case 'args': return $this->args;
			case 'options': return $this->options;
		}

		throw new PropertyNotDefined([ $property, $this ]);
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
