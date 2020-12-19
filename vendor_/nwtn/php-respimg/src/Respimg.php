<?php

	/**
	 * php-respimg <https://github.com/nwtn/php-respimg>
	 */

	namespace nwtn;
	use JonnyW\PhantomJs\Client as Client;
	use JonnyW\PhantomJs\DependencyInjection\ServiceContainer as ServiceContainer;
	use JonnyW\PhantomJs\Message\Request as Request;

	if (!class_exists('Client') || !class_exists('ServiceContainer')) {
		if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
			require_once(__DIR__ . '/../vendor/autoload.php');
		} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
			require_once(__DIR__ . '/../../../autoload.php');
		} else {
			die('Couldn’t load required libraries.');
		}
	}


	/**
	 * An Imagick extension to provide better (higher quality, lower file size) image resizes.
	 *
	 * This class extends Imagick (<http://php.net/manual/en/book.imagick.php>) based on
	 * research into optimal image resizing techniques (<https://github.com/nwtn/image-resize-tests>).
	 *
	 * Using these methods with their default settings should provide image resizing that is
	 * visually indistinguishable from Photoshop’s “Save for Web…”, but at lower file sizes.
	 *
	 * @author		David Newton <david@davidnewton.ca>
	 * @copyright	2015 David Newton
	 * @license		https://raw.githubusercontent.com/nwtn/php-respimg/master/LICENSE MIT
	 * @version		1.0.1
	 */

	class Respimg extends \Imagick {


		/**
		 * Optimizes the image without reducing quality.
		 *
		 * This function calls up to four external programs, which must be installed and available in the $PATH:
		 *
		 * * SVGO
		 * * image_optim
		 * * picopt
		 * * ImageOptim
		 *
		 * Note that these are executed using PHP’s `exec` command, so there may be security implications.
		 *
		 * @access	public
		 *
		 * @param	string	$path			The path to the file or directory that should be optimized.
		 * @param	integer	$svgo			The number of times to optimize using SVGO.
		 * @param	integer	$image_optim	The number of times to optimize using image_optim.
		 * @param	integer	$picopt			The number of times to optimize using picopt.
		 * @param	integer	$imageOptim		The number of times to optimize using ImageOptim.
		 */

		public static function optimize($path, $svgo = 0, $image_optim = 0, $picopt = 0, $imageOptim = 0) {

			// make sure the path is real
			if (!file_exists($path)) {
				return false;
			}
			$is_dir = is_dir($path);
			if (!$is_dir) {
				$dir = escapeshellarg(substr($path, 0, strrpos($path, '/')));
				$file = escapeshellarg(substr($path, strrpos($path, '/') + 1));
			}
			$path = escapeshellarg($path);

			// make sure we got some ints up in here
			$svgo = (int) $svgo;
			$image_optim = (int) $image_optim;
			$picopt = (int) $picopt;
			$imageOptim = (int) $imageOptim;

			// create some vars to store output
			$output = array();
			$return_var = 0;

			// if we’re using image_optim, we need to create the YAML config file
			if ($image_optim > 0) {
				$yml = tempnam('/tmp', 'yml');
				file_put_contents($yml, "verbose: true\njpegtran:\n  progressive: false\noptipng:\n  level: 7\n  interlace: false\npngcrush:\n  fix: true\n  brute: true\npngquant:\n  speed: 11\n");
			}

			// do the svgo optimizations
			for ($i = 0; $i < $svgo; $i++) {
				if ($is_dir) {
					$command = escapeshellcmd('svgo -f ' . $path . ' --disable removeUnknownsAndDefaults');
				} else {
					$command = escapeshellcmd('svgo -i ' . $path . ' --disable removeUnknownsAndDefaults');
				}
				exec($command, $output, $return_var);

				if ($return_var != 0) {
					return false;
				}
			}

			// do the image_optim optimizations
			for ($i = 0; $i < $image_optim; $i++) {
				$command = escapeshellcmd('image_optim -r ' . $path . ' --config-paths ' . $yml);
				exec($command, $output, $return_var);

				if ($return_var != 0) {
					return false;
				}
			}

			// do the picopt optimizations
			for ($i = 0; $i < $picopt; $i++) {
				$command = escapeshellcmd('picopt -r ' . $path);
				exec($command, $output, $return_var);

				if ($return_var != 0) {
					return false;
				}
			}

			// do the ImageOptim optimizations
			// ImageOptim can’t handle the path with single quotes, so we have to strip them
			// ImageOptim-CLI has an issue where it only works with a directory, not a single file
			for ($i = 0; $i < $imageOptim; $i++) {
				if ($is_dir) {
					$command = escapeshellcmd('imageoptim -d ' . $path . ' -q');
				} else {
					$command = escapeshellcmd('find ' . $dir . ' -name ' . $file) . ' | imageoptim';
				}
				exec($command, $output, $return_var);

				if ($return_var != 0) {
					return false;
				}
			}

			return $output;

		}


		/**
		 * Rasterizes an SVG image to a PNG.
		 *
		 * Uses phantomjs to save the SVG as a PNG image at the specified size.
		 *
		 * @access	public
		 *
		 * @param	string	$file			The path to the file that should be rasterized.
		 * @param	string	$dest			The path to the directory where the output PNG should be saved.
		 * @param	integer	$columns		The number of columns in the output image. 0 = maintain aspect ratio based on $rows.
		 * @param	integer	$rows			The number of rows in the output image. 0 = maintain aspect ratio based on $columns.
		 */

		public static function rasterize($file, $dest, $columns, $rows) {

			// check the input
			if (!file_exists($file)) {
				return false;
			}

			if (!file_exists($dest) || !is_dir($dest)) {
				return false;
			}

			// figure out the output width and height
			$svgTmp = new Respimg($file);
			$width = (double) $svgTmp->getImageWidth();
			$height = (double) $svgTmp->getImageHeight();
			$new_width = $columns;
			$new_height = $rows;

			$x_factor = $columns / $width;
			$y_factor = $rows / $height;
			if ($rows < 1) {
				$new_height = round($x_factor * $height);
			} elseif ($columns < 1) {
				$new_width = round($y_factor * $width);
			}

			// get the svg data
			$svgdata = file_get_contents($file);

			// figure out some path stuff
			$dest = rtrim($dest, '/');
			$filename = substr($file, strrpos($file, '/') + 1);
			$filename_base = substr($filename, 0, strrpos($filename, '.'));

			// setup the request
			$client = Client::getInstance();

			$serviceContainer = ServiceContainer::getInstance();
			$procedureLoaderFactory = $serviceContainer->get('procedure_loader_factory');
			$procedureLoader = $procedureLoaderFactory->createProcedureLoader(__DIR__);
			$client->getProcedureLoader()->addLoader($procedureLoader);

			$request = new RespimgCaptureRequest();
			$request->setType('svg2png');
			$request->setMethod('GET');
			$request->setSVG(base64_encode($svgdata));
			$request->setViewportSize($new_width, $new_height);
			$request->setRasterFile($dest . '/' . $filename_base . '-w' . $new_width . '.png');
			$request->setWidth($new_width);
			$request->setHeight($new_height);

			$response = $client->getMessageFactory()->createResponse();

			// send + return
			$client->send($request, $response);
			return true;

		}


		/**
		 * Resizes the image using smart defaults for high quality and low file size.
		 *
		 * This function is basically equivalent to:
		 *
		 * $optim == true: `mogrify -path OUTPUT_PATH -filter Triangle -define filter:support=2.0 -thumbnail OUTPUT_WIDTH -unsharp 0.25x0.08+8.3+0.045 -dither None -posterize 136 -quality 82 -define jpeg:fancy-upsampling=off -define png:compression-filter=5 -define png:compression-level=9 -define png:compression-strategy=1 -define png:exclude-chunk=all -interlace none -colorspace sRGB INPUT_PATH`
		 *
		 * $optim == false: `mogrify -path OUTPUT_PATH -filter Triangle -define filter:support=2.0 -thumbnail OUTPUT_WIDTH -unsharp 0.25x0.25+8+0.065 -dither None -posterize 136 -quality 82 -define jpeg:fancy-upsampling=off -define png:compression-filter=5 -define png:compression-level=9 -define png:compression-strategy=1 -define png:exclude-chunk=all -interlace none -colorspace sRGB -strip INPUT_PATH`
		 *
		 * @access	public
		 *
		 * @param	integer	$columns		The number of columns in the output image. 0 = maintain aspect ratio based on $rows.
		 * @param	integer	$rows			The number of rows in the output image. 0 = maintain aspect ratio based on $columns.
		 * @param	bool	$optim			Whether you intend to perform optimization on the resulting image. Note that setting this to `true` doesn’t actually perform any optimization.
		 */

		public function smartResize($columns, $rows, $optim = false) {

			$this->setOption('filter:support', '2.0');
			$this->thumbnailImage($columns, $rows, false, false, \Imagick::FILTER_TRIANGLE);
			if ($optim) {
				$this->unsharpMaskImage(0.25, 0.08, 8.3, 0.045);
			} else {
				$this->unsharpMaskImage(0.25, 0.25, 8, 0.065);
			}
			$this->posterizeImage(136, false);
			$this->setImageCompressionQuality(82);
			$this->setOption('jpeg:fancy-upsampling', 'off');
			$this->setOption('png:compression-filter', '5');
			$this->setOption('png:compression-level', '9');
			$this->setOption('png:compression-strategy', '1');
			$this->setOption('png:exclude-chunk', 'all');
			$this->setInterlaceScheme(\Imagick::INTERLACE_NO);
			$this->setColorspace(\Imagick::COLORSPACE_SRGB);
			if (!$optim) {
				$this->stripImage();
			}

		}


		/**
		 * Changes the size of an image to the given dimensions and removes any associated profiles.
		 *
		 * `thumbnailImage` changes the size of an image to the given dimensions and
		 * removes any associated profiles.  The goal is to produce small low cost
		 * thumbnail images suited for display on the Web.
		 *
		 * With the original Imagick thumbnailImage implementation, there is no way to choose a
		 * resampling filter. This class recreates Imagick’s C implementation and adds this
		 * additional feature.
		 *
		 * Note: <https://github.com/mkoppanen/imagick/issues/90> has been filed for this issue.
		 *
		 * @access	public
		 *
		 * @param	integer	$columns		The number of columns in the output image. 0 = maintain aspect ratio based on $rows.
		 * @param	integer	$rows			The number of rows in the output image. 0 = maintain aspect ratio based on $columns.
		 * @param	bool	$bestfit		Treat $columns and $rows as a bounding box in which to fit the image.
		 * @param	bool	$fill			Fill in the bounding box with the background colour.
		 * @param	integer	$filter			The resampling filter to use. Refer to the list of filter constants at <http://php.net/manual/en/imagick.constants.php>.
		 *
		 * @return	bool	Indicates whether the operation was performed successfully.
		 */

		public function thumbnailImage($columns, $rows, $bestfit = false, $fill = false, $filter = \Imagick::FILTER_TRIANGLE) {

			// sample factor; defined in original ImageMagick thumbnailImage function
			// the scale to which the image should be resized using the `sample` function
			$SampleFactor = 5;

			// filter whitelist
			$filters = array(
				\Imagick::FILTER_POINT,
				\Imagick::FILTER_BOX,
				\Imagick::FILTER_TRIANGLE,
				\Imagick::FILTER_HERMITE,
				\Imagick::FILTER_HANNING,
				\Imagick::FILTER_HAMMING,
				\Imagick::FILTER_BLACKMAN,
				\Imagick::FILTER_GAUSSIAN,
				\Imagick::FILTER_QUADRATIC,
				\Imagick::FILTER_CUBIC,
				\Imagick::FILTER_CATROM,
				\Imagick::FILTER_MITCHELL,
				\Imagick::FILTER_LANCZOS,
				\Imagick::FILTER_BESSEL,
				\Imagick::FILTER_SINC
			);

			// Parse parameters given to function
			$columns = (double) ($columns);
			$rows = (double) ($rows);
			$bestfit = (bool) $bestfit;
			$fill = (bool) $fill;

			// We can’t resize to (0,0)
			if ($rows < 1 && $columns < 1) {
				return false;
			}

			// Set a default filter if an acceptable one wasn’t passed
			if (!in_array($filter, $filters)) {
				$filter = \Imagick::FILTER_TRIANGLE;
			}

			// figure out the output width and height
			$width = (double) $this->getImageWidth();
			$height = (double) $this->getImageHeight();
			$new_width = $columns;
			$new_height = $rows;

			$x_factor = $columns / $width;
			$y_factor = $rows / $height;
			if ($rows < 1) {
				$new_height = round($x_factor * $height);
			} elseif ($columns < 1) {
				$new_width = round($y_factor * $width);
			}

			// if bestfit is true, the new_width/new_height of the image will be different than
			// the columns/rows parameters; those will define a bounding box in which the image will be fit
			if ($bestfit && $x_factor > $y_factor) {
				$x_factor = $y_factor;
				$new_width = round($y_factor * $width);
			} elseif ($bestfit && $y_factor > $x_factor) {
				$y_factor = $x_factor;
				$new_height = round($x_factor * $height);
			}
			if ($new_width < 1) {
				$new_width = 1;
			}
			if ($new_height < 1) {
				$new_height = 1;
			}

			// if we’re resizing the image to more than about 1/3 it’s original size
			// then just use the resize function
			if (($x_factor * $y_factor) > 0.1) {
				$this->resizeImage($new_width, $new_height, $filter, 1);

			// if we’d be using sample to scale to smaller than 128x128, just use resize
			} elseif ((($SampleFactor * $new_width) < 128) || (($SampleFactor * $new_height) < 128)) {
					$this->resizeImage($new_width, $new_height, $filter, 1);

			// otherwise, use sample first, then resize
			} else {
				$this->sampleImage($SampleFactor * $new_width, $SampleFactor * $new_height);
				$this->resizeImage($new_width, $new_height, $filter, 1);
			}

			// if the alpha channel is not defined, make it opaque
			if ($this->getImageAlphaChannel() == \Imagick::ALPHACHANNEL_UNDEFINED) {
				$this->setImageAlphaChannel(\Imagick::ALPHACHANNEL_OPAQUE);
			}

			// set the image’s bit depth to 8 bits
			$this->setImageDepth(8);

			// turn off interlacing
			$this->setInterlaceScheme(\Imagick::INTERLACE_NO);

			// Strip all profiles except color profiles.
			foreach ($this->getImageProfiles('*', true) as $key => $value) {
				if ($key != 'icc' && $key != 'icm') {
					$this->removeImageProfile($key);
				}
			}

			if (method_exists($this, 'deleteImageProperty')) {
				$this->deleteImageProperty('comment');
				$this->deleteImageProperty('Thumb::URI');
				$this->deleteImageProperty('Thumb::MTime');
				$this->deleteImageProperty('Thumb::Size');
				$this->deleteImageProperty('Thumb::Mimetype');
				$this->deleteImageProperty('software');
				$this->deleteImageProperty('Thumb::Image::Width');
				$this->deleteImageProperty('Thumb::Image::Height');
				$this->deleteImageProperty('Thumb::Document::Pages');
			} else {
				$this->setImageProperty('comment', '');
				$this->setImageProperty('Thumb::URI', '');
				$this->setImageProperty('Thumb::MTime', '');
				$this->setImageProperty('Thumb::Size', '');
				$this->setImageProperty('Thumb::Mimetype', '');
				$this->setImageProperty('software', '');
				$this->setImageProperty('Thumb::Image::Width', '');
				$this->setImageProperty('Thumb::Image::Height', '');
				$this->setImageProperty('Thumb::Document::Pages', '');
			}

			// In case user wants to fill use extent for it rather than creating a new canvas
			// …fill out the bounding box
			if ($bestfit && $fill && ($new_width != $columns || $new_height != $rows)) {
				$extent_x = 0;
				$extent_y = 0;

				if ($columns > $new_width) {
					$extent_x = ($columns - $new_width) / 2;
				}
				if ($rows > $new_height) {
					$extent_y = ($rows - $new_height) / 2;
				}

				$this->extentImage($columns, $rows, 0 - $extent_x, $extent_y);
			}

			return true;

		}

	}





	/**
	 * Extension of PHP PhantomJs Request class by Jon Wenmoth <contact@jonnyw.me>
	 *
	 * Extends the normal request to pass information relevant to rendering/saving an SVG.
	 *
	 * @author		David Newton <david@davidnewton.ca>
	 * @copyright	2015 David Newton
	 * @license		https://raw.githubusercontent.com/nwtn/php-respimg/master/LICENSE MIT
	 * @version		1.0.1
	 */

	class RespimgCaptureRequest extends Request {

		/**
		 * Path/filename of output image
		 *
		 * @var		string
		 * @access	protected
		 */

		protected $rasterFile;


		/**
		 * Width of PNG output
		 *
		 * @var		int
		 * @access	protected
		 */

		protected $width;


		/**
		 * Height of PNG output
		 *
		 * @var		int
		 * @access	protected
		 */

		protected $height;


		/**
		 * SVG data
		 *
		 * @var		string
		 * @access	protected
		 */

		protected $svgdata;


		/**
		 * Get height.
		 *
		 * @access	public
		 *
		 * @return	int
		 */

		public function getHeight() {
			return (int) $this->height;
		}


		/**
		 * Get the path/filename of the output PNG
		 *
		 * @access	public
		 *
		 * @return	string
		 */

		public function getRasterFile() {
			return $this->rasterFile;
		}


		/**
		 * Get SVG data.
		 *
		 * @access	public
		 *
		 * @return	string
		 */

		public function getSVG() {
			return (string) $this->svgdata;
		}


		/**
		 * Get width.
		 *
		 * @access	public
		 *
		 * @return	int
		 */

		public function getWidth() {
			return (int) $this->width;
		}


		/**
		 * Set height.
		 *
		 * @access	public
		 *
		 * @param	int		$height			Height
		 *
		 * @return	\JonnyW\PhantomJs\Message\AbstractRequest
		 */

		public function setHeight($height) {
			$this->height  = (int) $height;
			return $this;
		}


		/**
		 * Set the path/filename of the output PNG
		 *
		 * @access public
		 *
		 * @param	string	$file			The path/filename
		 *
		 * @throws	\JonnyW\PhantomJs\Exception\NotWritableException
		 * @return	\JonnyW\PhantomJs\Message\CaptureRequest
		 */

		public function setRasterFile($file) {
			if (!is_writable(dirname($file))) {
				throw new \JonnyW\PhantomJs\Exception\NotWritableException(sprintf('Capture file is not writeable by PhantomJs: %s', $file));
			}
			$this->rasterFile = $file;
			return $this;
		}


		/**
		 * Set SVG data
		 *
		 * This sets the base64-encoded SVG data, which will be used to build a data URI.
		 *
		 * @access	public
		 *
		 * @param	string	$svgdata		base64-encoded SVG data
		 *
		 * @return	string
		 */

		public function setSVG($svgdata) {
			$this->svgdata = $svgdata;
			return $this;
		}


		/**
		 * Set width.
		 *
		 * @access	public
		 *
		 * @param	int		$width			Width
		 *
		 * @return	\JonnyW\PhantomJs\Message\AbstractRequest
		 */

		public function setWidth($width) {
			$this->width  = (int) $width;
			return $this;
		}

	}

?>
