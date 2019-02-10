<?php
class Data {
	public $meta_description = array();
	public $meta_keywords = array();
	public $title = '';
	public $head = '';
	public $pagename = '';
	public $messages = '';
	public $show_menu = true;
	public $content = '';
	public $scripts = '';
	public $content_class = 'container';
	public function __construct() {
		if(session_id()) {
			if(isset($_SESSION['messages'])) {
				if(isset($_SESSION['messages']['errors']))
					$this->messages .= '<div class="errors"><div class="error">'.implode('</div><div class="error">', $_SESSION['messages']['errors']).'</div></div>';
				if(isset($_SESSION['messages']['attentions']))
					$this->messages .= '<div class="attentions"><div class="attention">'.implode('</div><div class="attention">', $_SESSION['messages']['attentions']).'</div></div>';
				if(isset($_SESSION['messages']['successes']))
					$this->messages .= '<div class="successes"><div class="success">'.implode('</div><div class="success">', $_SESSION['messages']['successes']).'</div></div>';
				unset($_SESSION['messages']);
			}
		}
	}
}
?>