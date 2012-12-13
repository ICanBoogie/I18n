<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * http://unicode.org/Public/cldr/1.9.0/
 * http://unicode.org/reports/tr35/
 */
namespace ICanBoogie\I18n\Build;

const REPOSITORY = 'http://unicode.org/cldr/trac/export/7939/tags/release-22-1/';

header('Content-Type: text/html; charset=utf-8');

function log($level, $message)
{
	static $logger;

	if (!$logger)
	{
		if (0)//strpos(PHP_SAPI, 'apache') !== false)
		{
			$logger = function($level, $message) {

				static $levels = array
				(
					'i' => "0;36m",
					'd' => "0;32m",
					'w' => "0;33m",
					'c' => "0;31m"
				);

				echo "\033[" . $levels[$level] . $message . "\033[0m" . PHP_EOL;

			};
		}
		else
		{
			$logger = function($level, $message) {

				static $levels = array
				(
					'i' => "blue",
					'd' => "green",
					'w' => "orange",
					'c' => "red"
				);

				echo '<span style="color: ' . $levels[$level] . '">' . htmlentities($message, ENT_COMPAT, 'utf-8') . '</span><br />';
			};
		}
	}

	$logger($level, $message);
}

function log_info($message)
{
	log('i', $message);
}

function log_debug($message)
{
	return;

	log('d', $message);
}

function log_warning($message)
{
	log('w', $message);
}

function log_critical($message)
{
	log('c', $message);
}

function get_xml($path)
{
	$pathname = __DIR__ . DIRECTORY_SEPARATOR . $path;

	if (!file_exists($pathname))
	{
		$dirname = dirname($pathname);

		if (!is_dir($dirname))
		{
			log_info("Create directory: $dirname");

			mkdir($dirname, 0777, true);
		}

		log_info("Downloading " . REPOSITORY . $path);

		$content = file_get_contents(REPOSITORY . $path);

		if (!$content)
		{
			throw new \Exception('Unable to download ' . REPOSITORY . $path);
		}

		file_put_contents($pathname, $content);
	}

	return simplexml_load_file($pathname);
}

class ConventionCompiler
{
	protected function create_path($path, \SimpleXMLElement $el)
	{
		$path .= '/' . $el->getName();

		$attributes = $el->attributes();

		if ($attributes->type)
		{
			$path .= '[@type="' . $attributes->type . '"]';
		}
		else if ($attributes->id)
		{
			$path .= '[@id="' . $attributes->id . '"]';
		}
		else if ($attributes->numberSystem)
		{
			$path .= '[@numberSystem="' . $attributes->numberSystem . '"]';
		}

		return $path;
	}

	protected $skip;
	protected $warpzone;
	protected $reroute;
	protected $rename;

	protected $xml = array();
	protected $convention = array();

	protected $aliases = array();

	public function __construct($id, array $options)
	{
		list($language, $territory) = explode('_', $id) + array(1 => null);

		$xml = array
		(
			'root' => get_xml("common/main/root.xml"),
// 			$language => get_xml("common/main/{$language}.xml")
		);

		if ($territory)
		{
// 			$xml[$id] = get_xml("common/main/{$id}.xml");
		}

		$this->xml = $xml;

		/*
		foreach ($xml as $id => $x)
		{
			$matches = $x->xpath('/ldml/dates/calendars/calendar[@type=\'gregorian\']/months/monthContext[@type=\'format\']/monthWidth');

			echo "in $id";
			var_dump($matches);
		}

		exit;
		*/
		#

		$this->skip = $options['skip'];
		$this->warpzone = $options['warpzone'];
		$this->reroute = $options['reroute'];
		$this->rename = $options['rename'];
	}

	public function __invoke()
	{
		$this->convention = array();

		# collect aliases

		foreach ($this->xml as $id => $xml)
		{
			log_info("Parsing '$id' for aliases");

			$this->collect_aliases($xml, $xml);
		}

		log_info(count($this->aliases) . ' aliases');
		echo implode('<br />', array_keys($this->aliases));

		foreach ($this->aliases as $path => $alias)
		{
			
		}

		exit;




		foreach ($this->xml as $id => $xml)
		{
			log_info("Parsing '$id'");

			$this->parse_20121205($xml);
		}









// 		var_dump($this->aliases); exit;

		foreach ($this->aliases as $path => $alias)
		{
			$this->resolve_alias($alias, $path);
		}

		log_info(count($this->aliases) . ' aliases');

		foreach ($this->aliases as $path => $alias)
		{
			if (!($alias instanceof \SimpleXMLElement) && !is_array($alias))
			{
				log_critical("failed to resolve alias for $path");
			}

// 			log_info("alias: $path");
		}

		# parse xml

		foreach ($this->xml as $id => $xml)
		{
			log_info("Parsing '$id'");

			$this->parse($xml);
		}

		return $this->convention;
	}

	protected function collect_aliases(\SimpleXMLElement $xml, \SimpleXMLElement $el, $path=null)
	{
		$path = $this->create_path($path, $el);

		#

		$alias = $el->alias;

		if ($alias)
		{
			$this->aliases[$path] = $alias->attributes()->path;

			return;
		}

		#

		foreach ($el as $child)
		{
			$this->collect_aliases($xml, $child, $path);
		}

		/*
		$path = $this->create_path($path, $el);

		foreach ($el as $child)
		{
			$this->collect_aliases($child, $path);
		}

		$alias = $el->alias;

		if ($alias)
		{
			$source = (string) $alias->attributes()->source;
			$xpath = (string) $alias->attributes()->path;
			$absolute_path = $path;
			$relative_xpath = $xpath;

			while (substr($relative_xpath, 0, 3) == '../')
			{
				$relative_xpath = substr($relative_xpath, 3);
				$absolute_path = dirname($absolute_path);
			}

			$this->aliases[$path] = $absolute_path . '/' . $relative_xpath;

			return;
		}

		#
		# the alias is overrode by a _real_ element.
		#

		if (isset($this->aliases[$path]))
		{
			log_warning("aliases element overode: $path");

			var_dump($el);
// 			echo "aliases overriden:"; var_dump($path, $this->aliases[$path], $el);

			$this->aliases[$path] = $el;
		}
		*/
	}

	protected function resolve_alias($alias, $path)
	{
		/*
		log_debug("in: $path");

		if (is_string($alias))
		{
			$xpath = $alias;
			$alias = null;

			foreach (array_reverse($this->xml) as $id => $xml)
			{
				$alias = $xml->xpath($xpath);

				if ($alias) break;
			}

			if (!$alias)
			{
				log_error("Unable to resolve alias: $xpath");

				return;
			}

			$this->aliases[$path] = $alias;
		}
		else if ($alias instanceof \SimpleXMLElement)
		{
			$this->aliases[$path] = $alias;
		}
		else
		{
			log_error("unsupported alias type in $path");
			var_dump($alias);

			return;
		}

		foreach ($alias as $child)
		{
			$p = $this->create_path($path, $child);

			$this->resolve_alias($child, $p);
		}
		*/
	}

	protected function skippable(\SimpleXMLElement $el, $path)
	{
		$attributes = $el->attributes();

		if ($attributes->draft == 'unconfirmed')
		{
			return true;
		}
		else if ($attributes->alt == 'variant')
		{
			return true;
		}
		else if ($attributes->numberSystem && $attributes->numberSystem != 'latn')
		{
			return true;
		}
		else if (in_array($path, $this->skip))
		{
			log_debug("skip path: $path");

			return true;
		}
	}

	protected function resolve_alias_20121205(\SimpleXMLElement $alias, $path)
	{
		$source = (string) $alias->attributes()->source;
		$xpath = (string) $alias->attributes()->path;
		$absolute_path = $path;
		$relative_xpath = $xpath;

		while (substr($relative_xpath, 0, 3) == '../')
		{
			$relative_xpath = substr($relative_xpath, 3);
			$absolute_path = dirname($absolute_path);
		}

		foreach (array_reverse($this->xml) as $xml)
		{
			$rc = $xml->xpath($absolute_path . '/' . $relative_xpath);

			if ($rc)
			{
				$rc = count($rc) == 1 ? current($rc) : $rc;

				/*
				if ($rc instanceof \SimpleXMLElement && $rc->alias)
				{
					echo "an alias in an alias!";

					var_dump($rc);

					$rc = $this->resolve_alias_20121205($rc, $path);

					var_dump($rc);
				}
				*/

				return $rc;
			}
		}

		log_critical("Unable to resolve alias: $xpath in $path");
	}

	protected function parse_20121205(\SimpleXMLElement $el, $path=null)
	{
		$path = $this->create_path($path, $el);

		if ($this->skippable($el, $path))
		{
			return;
		}

		$attributes = $el->attributes();
		$children = $el->children();

		if ($el->alias)
		{
			log_critical("$path has an alias!");

			$alias = $this->resolve_alias_20121205($el->alias, $path);

// 			var_dump($alias);

			if ($alias instanceof \SimpleXMLElement)
			{
				$children = $alias->children();
			}
			else if (is_array($alias))
			{
				$children = $alias;
			}
			else
			{
				log_critical("Unsupported data type for alias in $path");

				var_dump($alias);

				return;
			}
		}

		if (count($children))
		{
			foreach ($children as $child)
			{
				$this->parse_20121205($child, $path);
			}

			return;
		}

		#

		log_info("$path := $el");
	}

	protected function parse(\SimpleXMLElement $el, $path=null, $flatten_path=null)
	{
		global $convention;

		if ($this->skippable($el, $path))
		{
			return;
		}

		$name = $el->getName();
		$path = $this->create_path($path, $el);

		$children = $el->children();
		$attributes = $el->attributes();

		if ($attributes->type)
		{
			$name = $attributes->type;
		}
		else if ($attributes->id)
		{
			$name = $attributes->id;
		}
		else if ($attributes->request) // appendItems/appendItem
		{
			$name = $attributes->request;
		}
		else if ($name == 'unitPattern')
		{
			$name = (string) $attributes->count;

			if (!$name)
			{
				$name = '0';
			}

			$alt = (string) $attributes->alt;

			if ($alt)
			{
				$name = $alt . '/' . $name;
			}
		}

		$flatten_path .= '/' . $name;


		/*
		#
		# wrapzone
		#

		if (isset($this->warpzone[$path]))
		{
			$warpzone = $this->warpzone[$path];

			$matches = $el->xpath($warpzone);

			if (!$matches)
			{
				log_critical("No matches for warpzone path: $path");

				return;
			}

			list($el) = $matches;

			log_info("warpzone: $path := {$this->warpzone[$path]}");

			$this->parse($el, $path, $flatten_path);

			return;
		}
		*/

// 		log_info($path);

		if (isset($this->aliases[$path]))
		{
			$alias = $this->aliases[$path];

			if ($name == 'wide')
			{
				log_critical("$path has alias !");
				var_dump($el, $alias);
			}

			if (is_array($alias))
			{
				$children = $alias;
			}
			else
			{
				$children = $alias->children();
				$attributes = $alias->attributes();
			}
		}

		#






		#

		if (count($children))
		{
			foreach ($children as $child)
			{
				$this->parse($child, $path, $flatten_path);
			}

			return;
		}

		$value = $attributes->choice ? (string) $attributes->choice : (string) $el;

		log_info("$path ($flatten_path) := $value");

		$array_path = to_array_path($flatten_path);
		eval("\$this->convention{$array_path} = \$value;");

		return;














		if ($this->skippable($el, $path))
		{
			return;
		}

		#

		$name = $el->getName();

		if ($name == 'alias')
		{
			log_critical("What am I doing here ? $path/$name");

			var_dump($this->aliases[$path]);
		}

		$path .= '/' . $name;

		#
		# reroute
		#

		if (isset($this->reroute[$path]))
		{
			log_debug("rewrite: $path := $this->reroute[$path]");

			$path = $this->reroute[$path];
		}

		#

		$attributes = $el->attributes();

		if ($attributes->type)
		{
			$name = $attributes->type;
		}
		else if ($attributes->id)
		{
			$name = $attributes->id;
		}
		else if ($attributes->request) // appendItems/appendItem
		{
			$name = $attributes->request;
		}
		else if ($name == 'unitPattern')
		{
			$name = (string) $attributes->count;

			if (!$name)
			{
				$name = '0';
			}

			$alt = (string) $attributes->alt;

			if ($alt)
			{
				$name = $alt . '/' . $name;
			}
		}

		$flatten_path .= '/' . $name;

		#
		# wrapzone
		#

		if (isset($this->warpzone[$path]))
		{
			$warpzone = $this->warpzone[$path];

			$matches = $el->xpath($warpzone);

			if (!$matches)
			{
				log_critical("No matches for warpzone path: $path");

				return;
			}

			list($el) = $matches;

			log_info("warpzone: $path := {$this->warpzone[$path]}");

			$this->parse($el, $path, $flatten_path);

			return;
		}

		#

		if (isset($this->aliases[$path]))
		{
			$alias = $this->aliases[$path];

			if (is_array($alias))
			{
				$children = $alias;
			}
			else if ($alias instanceof \SimpleXMLElement)
			{
				$this->parse($alias, $path, $flatten_path);

				return;
			}
		}
		else
		{
			$children = $el->children();
		}

		#

		if (count($children))
		{
			foreach ($children as $child)
			{
				$this->parse($child, $path, $flatten_path);
			}

			return;
		}

		# node

		if (isset($this->rename[$flatten_path]))
		{
			$as = $this->rename[$flatten_path];

			log_debug("Rename {$flatten_path} as {$as}");

			$flatten_path = $as;
		}

		$value = $attributes->choice ? (string) $attributes->choice : (string) $el;

		log_debug("{$flatten_path}: $value");

		$array_path = to_array_path($flatten_path);
		eval("\$this->convention{$array_path} = \$value;");
	}

	protected function is_array(\SimpleXMLElement $el)
	{
		$last_name = null;

		$children = $el->children();

		if (count($children) < 2)
		{
			return false;
		}

		foreach ($children as $child)
		{
			$name = $child->getName();

			if ($last_name === null)
			{
				$last_name = $name;

				continue;
			}

			if ($last_name != $name)
			{
				return false;
			}
		}

		return true;
	}
}

function from_camel_case($str)
{
	static $callback;

	return $str;

	if (!$callback)
	{
		$callback = create_function('$c', 'return "_" . strtolower($c[1]);');
	}

	if (!preg_match('#[^A-Z0-1]+#', $str))
	{
		return strtolower($str);
	}

	$str[0] = strtolower($str[0]);

    return preg_replace_callback('/([A-Z])/', $callback, $str);
}

function to_array_path($str)
{
	$str = from_camel_case($str);

	return substr(str_replace('/', "']['", $str) . "']", 2);
}

function encode($var, $pad='')
{
    if (is_array($var))
    {
        $code = '';

        foreach ($var as $key => $value)
        {
       		$code .= "\t$pad" . (is_numeric($key) ? "$key=>" : "'$key'=>") . encode($value, "\t$pad") . ",\n";
        }

        return "array\n$pad(\n" . substr($code, 0, -2) . "\n$pad)";
    }
    else
    {
    	if (is_numeric($var))
    	{
    		return $var;
    	}
    	else if (is_string($var))
        {
            return "'" . addslashes($var) . "'";
        }
        elseif (is_bool($code))
        {
            return ($code ? 'true' : 'false');
        }
        else
        {
            return 'null';
        }
    }
}

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 10);

/*
 *
 */

if (isset($_GET['id']))
{
	$id = $_GET['id'];
}
else if (isset($_SERVER['argv'][1]))
{
	$id = $_SERVER['argv'][1];
}
else
{
	exit(-1);
}

log_info("Building convention for locale: $id");

$compiler = new ConventionCompiler
(
	$id, array
	(
		'skip' => array
		(
			'/ldml/fallback',
			'/ldml/identity',
			'/ldml/layout',
			'/ldml/posix',
			'/ldml/references',

			// more

// 			'/ldml/localeDisplayNames/languages',
// 			'/ldml/localeDisplayNames/territories',
			'/ldml/localeDisplayNames/scripts',
			'/ldml/localeDisplayNames/variants',
			'/ldml/localeDisplayNames/types',
			'/ldml/dates/timeZoneNames',
// 			'/ldml/numbers/currencies',


			/*
			// debug

			'/dates/timeZoneNames',
			'/localeDisplayNames',
			'/numbers',
			'/dates/dateTimeFormats',
			'/dates/fields',
			'/units'
			*/
		),

		'warpzone' => array
		(
			'/ldml/dates' => "calendars/calendar[@type='gregorian']"
			//'/numbers/decimalFormats/decimalFormatLength' => "decimalFormat/pattern",
			//'/numbers/decimalFormats/short' => "decimalFormat"
		),

		'reroute' => array
		(
			'/ldml/dates/calendars' => '/ldml/dates',
			'/ldml/dates/eras/eraNames' => '/ldml/dates/eras/wide',
			'/ldml/dates/eras/eraAbbr' => '/ldml/dates/eras/abbreviated',
			'/ldml/dates/eras/eraNarrow' => '/ldml/dates/eras/narrow'
		),

		'rename' => array
		(
			'/ldml/dates/dateFormats/full/dateFormat/pattern' => '/ldml/dates/dateFormats/full',
			'/ldml/dates/dateFormats/long/dateFormat/pattern' => '/ldml/dates/dateFormats/long',
			'/ldml/dates/dateFormats/medium/dateFormat/pattern' => '/ldml/dates/dateFormats/medium',
			'/ldml/dates/dateFormats/short/dateFormat/pattern' => '/ldml/dates/dateFormats/short',

			'/ldml/dates/timeFormats/full/timeFormat/pattern' => '/ldml/dates/timeFormats/full',
			'/ldml/dates/timeFormats/long/timeFormat/pattern' => '/ldml/dates/timeFormats/long',
			'/ldml/dates/timeFormats/medium/timeFormat/pattern' => '/ldml/dates/timeFormats/medium',
			'/ldml/dates/timeFormats/short/timeFormat/pattern' => '/ldml/dates/timeFormats/short',

			'/ldml/dates/dateTimeFormats/full/dateTimeFormat/pattern' => '/ldml/dates/dateTimeFormats/full',
			'/ldml/dates/dateTimeFormats/long/dateTimeFormat/pattern' => '/ldml/dates/dateTimeFormats/long',
			'/ldml/dates/dateTimeFormats/medium/dateTimeFormat/pattern' => '/ldml/dates/dateTimeFormats/medium',
			'/ldml/dates/dateTimeFormats/short/dateTimeFormat/pattern' => '/ldml/dates/dateTimeFormats/short'
		)
	)
);

$convention = $compiler();
$convention_encoded = encode($convention['ldml']);

$date = date('Y-m-d H:i:s');
$export = <<<EOT
<?php

/* AUTOMATICALLY CONVERTED $date */

return $convention_encoded;
EOT;

if (strpos(PHP_SAPI, 'apache') !== false)
{
	echo '<pre>' . htmlentities($export, ENT_COMPAT, 'utf-8') . '</pre>';
}
else
{
	if (isset($_SERVER['argv'][2]))
	{
		$destination = realpath($_SERVER['argv'][2]) . DIRECTORY_SEPARATOR . $id . '.php';

		log_info("Saving convention to: $destination");

		file_put_contents($destination, $export);
	}
}