<?php


if (!isset($argv[1])) {
	echo "Please specify the IA file as the first argument.\n";
	exit(1);
}

$arg1 = $argv[1];

if (!is_file($arg1)) {
	echo "$file is not a valid file.\n";
	exit(2);
}

$iafile = fopen($arg1, 'r');

class Element {
	public $handle;
	public $title;
	public $type;

	function __construct($title) {
		static $uid = 0;
		$uid++;

		// Extract type, if present
		if (preg_match("/<<.*>>/", $title, $match)) {
			$title = preg_replace("/<<.*>>/", '', $title);
			$this->type = trim($match[0], '<>');
		}
		else {
			$this->type = 'Page';
		}
		$this->handle = preg_replace('/[^0-9A-Za-z]+/', '', $title).$uid;
		$this->title = trim($title);
	}
}

/**
 * Keeps track of the current parents.
 */
class Stack {
	private $stack = array();

	function level() {
		return count($this->stack)-1;
	}

	function push($item) {
		array_unshift($this->stack, $item);
	}

	function pop() {
		return array_shift($this->stack);
	}

	function peek() {
		if (isset($this->stack[0])) return $this->stack[0];
	}
}

/**
 * Process the file, line by line.
 */
$stack = new Stack();
$line = 0;
while($ialine = fgets($iafile)) {
	$line++;

	// Empty line
	if (trim($ialine)=='') {
		echo "\n";
		continue;
	}

	// Figure out the indent level by looking at the amount of tabs in the front
	$currentLevel = 0;
	preg_match('/^(\t*)/', $ialine, $matches);
	if (isset($matches[1])) {
		$currentLevel = strlen($matches[1]);
	}

	$element = new Element($ialine);

	// Popping
	if ($currentLevel>$stack->level()+1) {
		// Too big change in indentation
		echo "Parse error: double (or more) indenting on line $line";
		exit(3);
	}
	else if ($currentLevel==$stack->level()+1) {
		// One level deeper, no need to pop.
	}
	else if ($currentLevel<$stack->level()+1) {
		// One or more levels shallower, need to pop.
		$diff = $stack->level()-$currentLevel;
		for ($i = 0; $i<=$diff; $i++) {
			$stack->pop();
		}
	}

	// Convert to yml
	echo "$element->type:\n";
	echo "\t$element->handle:\n";
	echo "\t\tTitle: \"$element->title\"\n";
	if ($parent = $stack->peek()) {
		echo "\t\tParent: =>$parent->type.$parent->handle\n";
	}

	// Pushing
	$stack->push($element);
}
