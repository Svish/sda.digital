<?php

namespace View\Helper;
use Mustache_LambdaHelper;

/**
 * Helper: SVG importer for Mustache templates.
 *
 * If the name includes a ";" everything
 * following it will be added to the <svg> 
 * tag as attributes.
 */
class FileIcon extends Svg
{

	public function __invoke($type, $render = null)
	{
		if($render)
			$type = $render($type);

		$type = explode(',', $type);
		$type = array_map([$this, 'type'], $type);
		$type = array_map([$this, 'get'], $type);

		return implode($type);
	}


	public function type($t)
	{
		// Audio
		if(starts_with('audio/', $t))
			return 'voice';

		// Video
		if(starts_with('video/', $t))
			return 'monitor';

		// PDF
		if($t == 'application/pdf')
			return 'file-pdf';

		// Plain text
		if(starts_with('text/', $t))
			return 'file-text';

		// Image
		if(starts_with('image/', $t))
			return 'file-image';

		// Archive
		if(in_array($t, [
			'application/zip',
			'application/x-7z-compressed',
			]))
			return 'file-zip';

		// Presentation
		if(in_array($t, [
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			]))
			return 'file-presentation';

		// Word document
		if(in_array($t, [
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			]))
			return 'file-word';

		// Excel
		if($t == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
			return 'file-x';

		// Unknown
		return 'file';
	}
}
