<?php

namespace Controller;
use Data\File as F;
use HTTP, View, Model, Session;

/**
 * Handles serving of files.
 *
 * @see http://stackoverflow.com/a/7591130/39321
 */
class File extends Controller
{
	public function get($id, $slug = null)
	{
		$file = Model::files()->for_page($id);

		if($slug == 'download')
			return $this->download($file);

		return View::template(get_defined_vars(), 'file/file')
			->output();
	}

	public function download(F $f)
	{
		Session::close();
		header_remove();

		$file = fopen($f->path, 'rb');
		$size = filesize($f->path);
		$buffer = 1024*8;

		$range = array(0, $size - 1);

		if(array_key_exists('HTTP_RANGE', $_SERVER))
		{
			$range = array_map('intval', explode('-', preg_replace('~.*=([^,]*).*~', '$1', $_SERVER['HTTP_RANGE'])));

			if (empty($range[1]))
				$range[1] = $size - 1;

			foreach($range as $key => $value)
				$range[$key] = max(0, min($value, $size - 1));

			if(($range[0] > 0) || ($range[1] < ($size - 1)))
				header(sprintf('%s %03u %s', 'HTTP/1.1', 206, 'Partial Content'), true, 206);
		}

		header('Accept-Ranges: bytes');
		header('Content-Range: bytes ' . sprintf('%u-%u/%u', $range[0], $range[1], $size));

		header('Pragma: public');
		header('Cache-Control: public, no-cache');
		header("Content-Type: {$f->type}");
		header('Content-Length: ' . sprintf('%u', $range[1] - $range[0] + 1));
		header("Content-Disposition: attachment; filename=\"{$f->name}\"");
		header('Content-Transfer-Encoding: binary');

		if ($range[0] > 0)
			fseek($file, $range[0]);


		// NOTE: Apparently not supported on One.com :(
		//set_time_limit(0);

		while ((feof($file) !== true) && (connection_status() === CONNECTION_NORMAL))
		{
			echo fread($file, $buffer);
			flush();
		}

		fclose($file);
	}
}
