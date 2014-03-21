<?php
/**
 * @author Tomáš Blatný
 */
namespace WebDev\Templating;

use FSHL\Highlighter;
use FSHL\Lexer\Php;
use Nette\Object;
use Nette\Templating\Template;
use Nette\Utils\Html;
use Nette\Utils\Strings;

class Helpers extends Object {
	public static function prepareTemplate(Template $template)
	{
		$texy = new \Texy();
		$highlighter = new Highlighter(new \FSHL\Output\Html(),
			Highlighter::OPTION_TAB_INDENT);
		$highlighter->setLexer(new Php());

		$template->registerHelper('syntax', function($text) use($highlighter) {
			return Html::el('pre')->setHtml($highlighter->highlight($text));
		});

		$template->registerHelper('texy', function($text) use($texy) {
			$text = Strings::replace($text, '#--(.*?)--#', function($match){
				return "<s>$match[1]</s>";
			});
			return Html::el('')->setHtml($texy->process($text));
		});

		$template->registerHelper('role', function($text) {
			return (in_array($text, array('member'))) ? '' :
				Html::el('span', array('class' => 'label label-role-'.$text))->setText(ucfirst($text));
		});

		$template->registerHelper('nl2br', function($text) {
			return Html::el('')->setHtml(nl2br(\Nette\Templating\Helpers::escapeHtml($text)));
		});

		$template->registerHelper('time', function($text) {
			return date('j.n.Y G:i:s', $text);
		});

		$template->registerHelper('date', function($dateTime) {
			return (!$dateTime ? 'nenastaven' : $dateTime->format('j.n.Y'));
		});
	}
}