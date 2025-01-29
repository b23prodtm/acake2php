<?php
/**
 * Used when a markdown file cannot be found.
 *
 * @package       App.Error
 */
namespace Markdown\Lib\Error;

class MissingMarkdownException extends \Exception {

	protected $_messageTemplate = 'Markdown file %s is missing.';

}
?>