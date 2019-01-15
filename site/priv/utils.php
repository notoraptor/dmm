<?php
require_once(__DIR__.'/password.php');
require_once(__DIR__.'/Set.php');

$GLOBALS['CONFIG_FIELDS'] = array(
	'home_text_left',
	'home_text_right',
	'home_text_bottom',
	'contact_text',
	'contact_video',
	'submission_title',
	'submission_text',
	'submission_bottom_photo_text'
);

$GLOBALS['MODEL_FIELDS'] = array(
	'model_id',
	'first_name',
	'last_name',
	'hint',
	'trend_rank',
	'category',
	'instagram_link',
	'video_link',
	'age',
	"sex",
	'height',
	'hair',
	'eyes'
);

$GLOBALS['AGENT_FIELDS'] = array(
	'agent_id',
	'first_name',
	'last_name',
	'role',
	'email'
);

$GLOBALS['DIR_DB'] = server_dir() . '/data';
$GLOBALS['DIR_DB_MAIN'] = $GLOBALS['DIR_DB'] . '/main';

function CONFIG_FIELDS() {return $GLOBALS['CONFIG_FIELDS'];}
function MODEL_FIELDS() {return $GLOBALS['MODEL_FIELDS'];}
function AGENT_FIELDS() {return $GLOBALS['AGENT_FIELDS'];}
function DIR_DB() {return $GLOBALS['DIR_DB'];}
function DIR_DB_MAIN() {return $GLOBALS['DIR_DB_MAIN'];}

function utils_photo($folder, $name) {
    $files = scandir($folder);
    $found = array();
    foreach($files as $file) if (strpos($file, $name.'.') === 0) $found[] = $file;
    return count($found) == 1 ? $folder.'/'.$found[0] : false;
}
function utils_photos($folder, $prefix) {
	$files = scandir($folder);
	$found = array();
	foreach($files as $file) {
		if (strpos($file, $prefix) === 0) {
			$pos_dot = strpos($file, '.');
			$file_name = substr($file, 0, $pos_dot);
			if (isset($found[$file_name]))
				return false;
			$found[$file_name] = $file;
		}
	}
	$files_found = array_values($found);
	sort($files_found);
	$paths = array();
	foreach($files_found as $file)
		$paths[] = DIR_DB().'/'.$file;
	return $paths;
}

function utils_home_photo_1_name() {return 'home_1';}
function utils_home_photo_2_name() {return 'home_2';}
function utils_submission_photo_name() {return 'submission';}
function utils_submission_bottom_photo_name() {return 'submission_bottom';}
function utils_model_photo_prefix($model_id) {return 'model_'.$model_id;}
function utils_model_photo_name($model_id, $photo_id) {return utils_model_photo_prefix($model_id).'_'.$photo_id;}
function utils_model_card_name($model_id) {return 'card_'.$model_id.'.pdf';}
function utils_contact_photo_prefix() {return 'contact';}
function utils_contact_photo_name($photo_id) {return utils_contact_photo_prefix().'_'.$photo_id;}

function utils_home_photo_1() {return utils_photo(DIR_DB(), utils_home_photo_1_name());}
function utils_home_photo_2() {return utils_photo(DIR_DB(), utils_home_photo_2_name());}
function utils_submission_photo() {return utils_photo(DIR_DB(), utils_submission_photo_name());}
function utils_submission_bottom_photo() {return utils_photo(DIR_DB(), utils_submission_bottom_photo_name());}
function utils_model_photo($model_id, $photo_id) {return utils_photo(DIR_DB(), utils_model_photo_name($model_id, $photo_id));}
function utils_model_photos($model_id) {return utils_photos(DIR_DB(), utils_model_photo_prefix($model_id));}
function utils_model_card($model_id) {
    $path = DIR_DB().'/'.utils_model_card_name($model_id);
    return is_file($path) ? $path: false;
}
function utils_contact_photo($photo_id) {return utils_photo(DIR_DB(), utils_contact_photo_name($photo_id));}
function utils_contact_photos() {return utils_photos(DIR_DB(), utils_contact_photo_prefix());}

function utils_as_link($path) {return str_replace(server_dir(), server_http(), $path);}

function utils_array_to_lines($table, $line_length) {
	$lines = array();
	$count_elements = count($table);
	for ($i = 0; $i < $count_elements; ++$i) {
		if ($i % $line_length == 0) {
			$lines[] = array();
		}
		$lines[count($lines) - 1][] = $table[$i];
	}
	return $lines;
}


class DatabaseRow {
	protected $data = array();
	public function __construct($data) {$this->data = $data;}
}

class Admin extends DatabaseRow {
	public function username() {return $this->data['username'];}
	public function password() {return $this->data['password'];}
	public function is_valid() {return $this->data !== false;}
	public function approved() {return $this->data['approved'] != '0';}
	public function id() {return $this->data['admin_id'];}
}

class Config extends DatabaseRow  {
	public function home_text_left() { return $this->data['home_text_left']; }
	public function home_text_right() { return $this->data['home_text_right']; }
	public function home_text_bottom() { return $this->data['home_text_bottom']; }
	public function contact_text() { return $this->data['contact_text']; }
	public function contact_video() { return $this->data['contact_video']; }
	public function submission_title() { return $this->data['submission_title']; }
	public function submission_text() { return $this->data['submission_text']; }
	public function submission_bottom_photo_text() { return $this->data['submission_bottom_photo_text']; }
}

class Model extends DatabaseRow {
	public function id() {return $this->data['model_id'];}
	public function first_name() {return $this->data['first_name'];}
	public function last_name() {return $this->data['last_name'];}
	public function trend_rank() {return $this->data['trend_rank'];}
	public function hint() {return $this->data['hint'];}
	public function category() {return $this->data['category'];}
	public function instagram_link() {return $this->data['instagram_link'];}
	public function video_link() {return $this->data['video_link'];}
	public function age() {return $this->data['age'];}
	public function sex() {return $this->data['sex'];}
	public function height() {return $this->data['height'];}
	public function hair() {return $this->data['hair'];}
	public function eyes() {return $this->data['eyes'];}
	public function photos() {return $this->data['photos'];}
	public function full_name() {return $this->first_name().' '.$this->last_name();}
	public function to_post() {
	    $post = array();
	    foreach(MODEL_FIELDS() as $field) $post[$field] = $this->data[$field];
	    unset($post['model_id']);
	    return $post;
    }
    public function get_profile_photo() {
	    $photos = $this->photos();
	    return $photos ? $photos[0]->getURL() : null;
    }
}

class Agent extends DatabaseRow {
	public function id() {return $this->data['agent_id'];}
	public function first_name() {return $this->data['first_name'];}
	public function last_name() {return $this->data['last_name'];}
	public function role() {return $this->data['role'];}
	public function email() {return $this->data['email'];}
	public function full_name() {return $this->first_name().' '.$this->last_name();}
	public function to_post() {
		$post = array();
		foreach(AGENT_FIELDS() as $field) $post[$field] = $this->data[$field];
		unset($post['agent_id']);
		return $post;
	}
}

class Photo extends DatabaseRow {
    private $model_id = false;
    public function __construct($data, $model_id = null) {
		parent::__construct($data);
		$this->model_id = $model_id;
	}
	public function id() {return $this->data['photo_id'];}
	public function rank() {return $this->data['photo_rank'];}
	public function setAsConfig() {$this->model_id = false;}
	public function setAsModel($model_id) {$this->model_id = $model_id;}
	public function isConfig() {return !$this->model_id;}
	public function isModel() {return $this->model_id;}
	public function getPath() {return $this->model_id ? utils_model_photo($this->model_id, $this->id()) : utils_contact_photo($this->id());}
	public function getURL() {return utils_as_link($this->getPath());}
	static function sort(Photo $photo_a, Photo $photo_b) {
		$t = $photo_a->rank() - $photo_b->rank();
		if (!$t)
			$t = $photo_a->id() - $photo_b->id();
		return $t;
	}
}

class Database {
	//.
	private $requetes_tables = array();
	private $bdd = null;
	public function __construct() {
		try {
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$this->bdd = new PDO('mysql:host='.NOM_HOTE.';dbname='.NOM_BASE, NOM_UTILISATEUR, MOT_DE_PASSE, $pdo_options);
			$this->verifier_existence_tables();
			$this->verifier_existence_bdd_sur_disque();
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur');
		}
	}
	private function secure_query($requete, $parametres = array()) {
		try {
			$execution = $this->bdd->prepare($requete);
			$execution->execute($parametres);
			$donnees = array();
			while(($ligne = $execution->fetch())) {
				$donnees_ligne = array();
				foreach($ligne as $key => $value) {
					if(is_string($value)) $value = utils_unescape($value);	//todo useful?
					if(!is_int($key)) $donnees_ligne[$key] = $value;
				}
				$donnees[] = $donnees_ligne;
			}
			$execution->closeCursor();
			return $donnees;
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur pendant une requ&ecirc;te de s&eacute;lection');
		}
	}
	private function oneResult($requete, $parametres = array()) {
		$donnees = $this->secure_query($requete, $parametres);
		return count($donnees) == 1 ? $donnees[0] : false;
	}
	private function secure_modif($requete, $parametres = array()) {
		try {
			$execution = $this->bdd->prepare($requete);
			$execution->execute($parametres);
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur pendant une requ&ecirc;te de modification: '.$requete);
		}
	}
	private function throw_exception(Exception &$e, $prefix = '') {
		throw new Exception( ($prefix == '' ? '' : $prefix . ': ') . $e->getMessage() );
	}
	private function verifier_existence_tables() {
		$this->requetes_tables = array(
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'admin ('.
				'admin_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'username VARCHAR (255) NOT NULL UNIQUE,'.
				'password VARCHAR (255) NOT NULL,'.
				'approved TINYINT NOT NULL DEFAULT 0,'.
				'PRIMARY KEY (admin_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'configuration ('.
				'config_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'home_text_left TEXT,'.
				'home_text_right TEXT,'.
				'home_text_bottom TEXT,'.
				'contact_text TEXT,'.
				'contact_video VARCHAR (255),'.
				'submission_title TEXT,'.
				'submission_text TEXT,'.
				'submission_bottom_photo_text TEXT,'.
				'PRIMARY KEY (config_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'model ('.
				'model_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'first_name VARCHAR(255) NOT NULL,'.
				'last_name VARCHAR(255) NOT NULL,'.
				'hint VARCHAR(255),'.
				'trend_rank INTEGER NOT NULL DEFAULT -1,'.
				'category VARCHAR(512),'.
				'instagram_link VARCHAR (255),'.
				'video_link VARCHAR (255),'.
				'age INTEGER NOT NULL,'.
				"sex ENUM('male', 'female', 'X') NOT NULL,".
				'height VARCHAR(255),'.
				'hair VARCHAR(255),'.
				'eyes VARCHAR(255),'.
				'PRIMARY KEY (model_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'agent ('.
				'agent_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'first_name VARCHAR(255) NOT NULL,'.
				'last_name VARCHAR(255) NOT NULL,'.
				'role VARCHAR(255),'.
				'email VARCHAR(255),'.
				'PRIMARY KEY (agent_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'model_photo ('.
				'photo_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'model_id INT UNSIGNED NOT NULL,'.
				'photo_rank INT UNSIGNED NOT NULL,'.
				'PRIMARY KEY (photo_id),'.
				'CONSTRAINT fk_model_photo FOREIGN KEY (model_id) REFERENCES model (model_id) ON DELETE CASCADE'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'contact_photo ('.
				'photo_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'photo_rank INT UNSIGNED NOT NULL,'.
				'PRIMARY KEY (photo_id)'.
			') ENGINE = INNODB;',
		);
		$compte = count($this->requetes_tables);
		for($i = 0; $i < $compte; ++$i) $this->secure_modif($this->requetes_tables[$i]);
		$this->alterer_tables();
		$this->autres_verifications();
	}
	private function alterer_tables() {
		$alterations = array(
			/*
			array(
				'SHOW COLUMNS FROM '.DB_PREFIX.'model LIKE \'article_content\'',
				'ALTER TABLE '.DB_PREFIX.'model ADD article_content TEXT',
			),
			*/
		);
		$compte = count($alterations);
		for($i = 0; $i < $compte; ++$i) {
			$alteration = $alterations[$i];
			switch(count($alteration)) {
				case 1:
					$this->secure_modif($alteration[0]);
					break;
				case 2:
					$donnees = $this->secure_query($alteration[0]);
					if(count($donnees) == 0) $this->secure_modif($alteration[1]);
					break;
				default:break;
			}
		}
	}
	private function autres_verifications() {
		$data = $this->secure_query('SELECT COUNT(config_id) AS count FROM '.DB_PREFIX.'configuration');
		if($data[0]['count'] == 0)
			$this->secure_modif('INSERT INTO '.DB_PREFIX.'configuration (config_id) VALUES(1)');
	}
	private function verifier_existence_bdd_sur_disque() {
		$folders = array(DIR_DB(), DIR_DB_MAIN());
		$countFolders = count($folders);
		for($i = 0; $i < $countFolders; ++$i) {
			if(!file_exists($folders[$i]))
				mkdir($folders[$i]);
			if(!file_exists($folders[$i]) || !is_dir($folders[$i]))
				throw new Exception('Unable to create database folder: '.$folders[$i]);
		}
	}
	// Méthodes.
	public function admin($id) {
		return $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
	}
	public function admin_login($username, $password) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		return $data && password_verify($password, $data['password']) ? new Admin($data) : false;
	}
	public function admin_create($username, $password) {
		$data = $this->oneResult('SELECT admin_id FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		if($data) return false;
		$data = $this->oneResult('SELECT COUNT(admin_id) AS count FROM '.DB_PREFIX.'admin');
		$approved = $data['count'] == 0 ? 1 : 0;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'admin (username, password, approved) VALUES(?,?,?)', array(
			$username, password_hash($password, PASSWORD_DEFAULT), $approved
		));
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		return new Admin($data);
	}
	public function admin_update($username, $oldPassword, $newPassword) {
		$data = $this->oneResult('SELECT password FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		if(!$data || !password_verify($oldPassword, $data['password'])) return false;
		$this->secure_modif('UPDATE '.DB_PREFIX.'admin SET password = ? WHERE username = ?', array(
			password_hash($newPassword, PASSWORD_DEFAULT), $username
		));
		return new Admin($this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username)));
	}
	public function admin_approve($id) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		if(!$data) return false;
		$this->secure_modif('UPDATE '.DB_PREFIX.'admin SET approved = 1 WHERE admin_id = ?', array($id));
		$data['approved'] = 1;
		return new Admin($data);
	}
	public function admin_delete($id) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		if(!$data) return false;
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		return new Admin($data);
	}
	public function all_admins_but($id) {
		$rows = $this->secure_query('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE NOT admin_id = ?', array($id));
		$administrators = array();
		foreach($rows as $row) $administrators[] = new Admin($row);
		return $administrators;
	}
	public function config() {
		$data = $this->oneResult('SELECT '.implode(', ', CONFIG_FIELDS()).' FROM '.DB_PREFIX.'configuration');
		return $data ? new Config($data) : false;
	}
	public function config_update($config_dict) {
		$config_query = array();
		$config_params = array();
		foreach(CONFIG_FIELDS() as $config_field) {
			if (isset($config_dict[$config_field])) {
				$config_query[] = $config_field.' = ?';
				$config_params[] = $config_dict[$config_field];
			}
		}
		if ($config_query)
			$this->secure_modif('UPDATE '.DB_PREFIX.'configuration SET '.implode(', ', $config_query), $config_params);
	}
	public function model_photos($id) {
		$photos = array();
		$data_photos = $this->secure_query('SELECT * FROM '.DB_PREFIX.'model_photo WHERE model_id = ?', array($id));
		foreach($data_photos as $row)
			$photos[] = new Photo($row, $id);
		usort($photos, array('Photo', 'sort'));
		return $photos;
	}
	public function model_photos_max_rank($model_id) {
	    $data = $this->secure_query('SELECT MAX(photo_rank) AS max_rank FROM '.DB_PREFIX.'model_photo WHERE model_id = ?', array($model_id));
	    return $data && $data[0]['max_rank'] ? $data[0]['max_rank'] : 0;
    }
	public function model_photo_add($model_id) {
	    $photo_rank = $this->model_photos_max_rank($model_id) + 1;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'model_photo (model_id, photo_rank) VALUES (?,?)', array($model_id, $photo_rank));
		return $this->bdd->lastInsertId();
	}
	public function model_photo_delete($model_id, $photo_id) {
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'model_photo WHERE model_id = ? AND photo_id = ?', array($model_id, $photo_id));
		$link = utils_model_photo($model_id, $photo_id);
		if ($link)
		    unlink($link);
		$photos = $this->model_photos($model_id);
		for ($i = 0; $i < count($photos); ++$i) {
			$this->secure_modif('UPDATE '.DB_PREFIX.'model_photo SET photo_rank = ? WHERE model_id = ? AND photo_id = ?', array($i, $model_id, $photos[$i]->id()));
		}
		return $link;
	}
	public function model_photo_update($model_id, $photo_id, $photo_rank) {
		$photos = $this->model_photos($model_id);
		if (empty($photos))
			return false;
		$other_photos = array();
		$updated_photo = null;
		foreach($photos as $photo) {
			if ($photo->id() != $photo_id) {
				$other_photos[] = $photo;
			} else {
				$updated_photo = $photo;
			}
		}
		if (!$updated_photo || empty($other_photos))
			return false;
		if ($photo_rank <= $other_photos[0]->rank())
			array_splice($other_photos, 0, 0, array($updated_photo));
		else if ($photo_rank > $other_photos[count($other_photos) - 1]->rank()) {
			$other_photos[] = $updated_photo;
		} else for ($i = 1; $i < count($other_photos); ++$i) {
			$current_rank = $other_photos[$i]->rank();
			$previous_rank = $other_photos[$i-1]->rank();
			if ($photo_rank == $current_rank || ($previous_rank < $photo_rank && $photo_rank < $current_rank)) {
				array_splice($other_photos, $i, 0, array($updated_photo));
				break;
			}
		}
		for ($i = 0; $i < count($other_photos); ++$i) {
			$this->secure_modif('UPDATE '.DB_PREFIX.'model_photo SET photo_rank = ? WHERE model_id = ? AND photo_id = ?', array($i, $model_id, $other_photos[$i]->id()));
		}
	}
	public function model_exists($id) {
		return $this->oneResult('SELECT model_id FROM '.DB_PREFIX.'model WHERE model_id = ?', array($id));
	}
	public function model($id) {
		$data = $this->oneResult('SELECT * FROM '.DB_PREFIX.'model WHERE model_id = ?', array($id));
		if(!$data) return false;
		$data['photos'] = $this->model_photos($id);
		return new Model($data);
	}
	public function models() {
		$data = $this->secure_query('SELECT * FROM '.DB_PREFIX.'model ORDER BY trend_rank ASC, model_id ASC');
		$models = array();
		// Vidéos.
		$countModels = count($data);
		for($i = 0; $i < $countModels; ++$i) {
			$data[$i]['photos'] = $this->model_photos($data[$i]['model_id']);
			$models[] = new Model($data[$i]);
		}
		return $models;
	}
	public function model_update($id, $fields) {
		$keys = array_keys($fields);
		$keysForDB = array();
		$values = array();
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			$keysForDB[] = $keys[$i] . '= ?';
			$values[] = $fields[$keys[$i]];
		}
		$values[] = $id;
		$this->secure_modif('UPDATE '.DB_PREFIX.'model SET '.(implode(',', $keysForDB)).' WHERE model_id = ?', $values);
		return $this->model($id);
	}
	public function model_create($mainValues) {
		$data = $this->secure_query('SELECT model_id FROM '.DB_PREFIX.'model WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		if(!empty($data)) return false;
		$fields = array();
		$holders = array();
		$params = array();
		foreach(MODEL_FIELDS() as $field) {
			if (isset($mainValues[$field])) {
				$fields[] = $field;
				$holders[] = '?';
				$params[] = $mainValues[$field];
			}
		}
		if (!$fields) return false;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'model ('.implode(',', $fields).') VALUES('.implode(',', $holders).')', $params);
		$data = $this->oneResult('SELECT model_id FROM '.DB_PREFIX.'model WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		return $this->model($data['model_id']);
	}
	public function model_delete($id) {
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'model WHERE model_id = ?', array($id));
		$model_card = utils_model_card($id);
		if ($model_card)
		    unlink($model_card);
		$photos = utils_model_photos($id);
		if ($photos) foreach($photos as $photo) unlink($photo);
		return true;
	}
	public function agent_exists($id) {
		return $this->oneResult('SELECT agent_id FROM '.DB_PREFIX.'ageent WHERE agent_id = ?', array($id));
	}
	public function agent($id) {
		$data = $this->oneResult('SELECT * FROM '.DB_PREFIX.'agent WHERE agent_id = ?', array($id));
		if(!$data) return false;
		return new Agent($data);
	}
	public function agents() {
		$data = $this->secure_query('SELECT * FROM '.DB_PREFIX.'agent');
		$agents = array();
		// Vidéos.
		$countAgents = count($data);
		for($i = 0; $i < $countAgents; ++$i) {
			$agents[] = new Agent($data[$i]);
		}
		return $agents;
	}
	public function agent_update($id, $fields) {
		$keys = array_keys($fields);
		$keysForDB = array();
		$values = array();
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			$keysForDB[] = $keys[$i] . '= ?';
			$values[] = $fields[$keys[$i]];
		}
		$values[] = $id;
		$this->secure_modif('UPDATE '.DB_PREFIX.'agent SET '.(implode(',', $keysForDB)).' WHERE agent_id = ?', $values);
		return $this->agent($id);
	}
	public function agent_create($mainValues) {
		$data = $this->secure_query('SELECT agent_id FROM '.DB_PREFIX.'agent WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		if(!empty($data)) return false;
		$fields = array();
		$holders = array();
		$params = array();
		foreach(AGENT_FIELDS() as $field) {
			if (isset($mainValues[$field])) {
				$fields[] = $field;
				$holders[] = '?';
				$params[] = $mainValues[$field];
			}
		}
		if (!$fields) return false;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'agent ('.implode(',', $fields).') VALUES('.implode(',', $holders).')', $params);
		$data = $this->oneResult('SELECT agent_id FROM '.DB_PREFIX.'agent WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		return $this->agent($data['agent_id']);
	}
	public function agent_delete($id) {
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'agent WHERE agent_id = ?', array($id));
		return true;
	}
	public function contact_photos() {
		$photos = array();
		$data_photos = $this->secure_query('SELECT * FROM '.DB_PREFIX.'contact_photo');
		foreach($data_photos as $row)
			$photos[] = new Photo($row);
		usort($photos, array('Photo', 'sort'));
		return $photos;
	}
	public function contact_photos_max_rank() {
		$data = $this->secure_query('SELECT MAX(photo_rank) AS max_rank FROM '.DB_PREFIX.'contact_photo');
		return $data && $data[0]['max_rank'] ? $data[0]['max_rank'] : 0;
	}
	public function contact_photo_add() {
		$photo_rank = $this->contact_photos_max_rank() + 1;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'contact_photo (photo_rank) VALUES (?)', array($photo_rank));
		return $this->bdd->lastInsertId();
	}
	public function contact_photo_delete($photo_id) {
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'contact_photo WHERE photo_id = ?', array($photo_id));
		$link = utils_contact_photo($photo_id);
		if ($link)
			unlink($link);
		$photos = $this->contact_photos();
		for ($i = 0; $i < count($photos); ++$i) {
			$this->secure_modif('UPDATE '.DB_PREFIX.'contact_photo SET photo_rank = ? WHERE photo_id = ?', array($i, $photos[$i]->id()));
		}
		return $link;
	}
	public function contact_photo_update($photo_id, $photo_rank) {
		$photos = $this->contact_photos();
		if (empty($photos))
			return false;
		$other_photos = array();
		$updated_photo = null;
		foreach($photos as $photo) {
			if ($photo->id() != $photo_id) {
				$other_photos[] = $photo;
			} else {
				$updated_photo = $photo;
			}
		}
		if (!$updated_photo || empty($other_photos))
			return false;
		if ($photo_rank <= $other_photos[0]->rank())
			array_splice($other_photos, 0, 0, array($updated_photo));
		else if ($photo_rank > $other_photos[count($other_photos) - 1]->rank()) {
			$other_photos[] = $updated_photo;
		} else for ($i = 1; $i < count($other_photos); ++$i) {
			$current_rank = $other_photos[$i]->rank();
			$previous_rank = $other_photos[$i-1]->rank();
			if ($photo_rank == $current_rank || ($previous_rank < $photo_rank && $photo_rank < $current_rank)) {
				array_splice($other_photos, $i, 0, array($updated_photo));
				break;
			}
		}
		for ($i = 0; $i < count($other_photos); ++$i) {
			$this->secure_modif('UPDATE '.DB_PREFIX.'contact_photo SET photo_rank = ? WHERE photo_id = ?', array($i, $other_photos[$i]->id()));
		}
	}
	public function list_hairs() {
		$data = $this->secure_query('SELECT hair FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['hair']);
		$set->add(array('black', 'brown', 'blond', 'auburn', 'chestnut', 'red', 'gray', 'white'));
		return $set;
	}
	public function list_roles() {
		$data = $this->secure_query('SELECT role FROM '.DB_PREFIX.'agent');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['role']);
		return $set;
	}
	public function list_eyes() {
		$data = $this->secure_query('SELECT eyes FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['eyes']);
		$set->add(array('black', 'amber', 'blue', 'brown', 'gray', 'green', 'hazel', 'red', 'violet'));
		return $set;
	}
	public function list_sex() {
		$data = $this->secure_query('SELECT sex FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['sex']);
		$set->add('male');
		$set->add('female');
		$set->add('X');
		return $set;
	}
	public function list_hints() {
		$data = $this->secure_query('SELECT hint FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['hint']);
		return $set;
	}
	public function list_categories() {
		$data = $this->secure_query('SELECT category FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['category']);
		return $set;
	}
}

class Utils {
	// Curl helper function (from VIMEO)
	static public function curl_get($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$return = curl_exec($curl);
		curl_close($curl);
		return $return;
	}
	static public function valid_url($url) {
		return !filter_var($url, FILTER_VALIDATE_URL) === false;
	}
}

function utils_message_add_error($msg) {$_SESSION['messages']['errors'][] = $msg;}
function utils_message_add_attention($msg) {$_SESSION['messages']['attentions'][] = $msg;}
function utils_message_add_success($msg) {$_SESSION['messages']['successes'][] = $msg; return true;}
function utils_redirection($chemin) {
	header('Location: '.$chemin);
	exit();
}
function utils_request_redirection($link) {$GLOBALS['redirection'] = $link;}
function utils_has_redirection() {return isset($GLOBALS['redirection']);}
function utils_execute_redirection() {
	if(utils_has_redirection()) {
		$the_redirection = $GLOBALS['redirection'];
		unset($GLOBALS['redirection']);
		header('Location: '.$the_redirection);
	}
}

function utils_safe_string($s) {
	//$chaine = htmlentities(trim($s), ENT_NOQUOTES, 'ISO-8859-1');
	$chaine = htmlentities(trim($s), ENT_NOQUOTES, 'UTF-8');
	$chaine = str_replace("'","-",$chaine);
	$chaine = str_replace('"',"-",$chaine);
	$chaine = str_replace('$',"-",$chaine);
	$chaine = str_replace("&amp;","&",$chaine);
	$chaine = preg_replace("/&([A-Za-z])(acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);/","$1",$chaine);
	$chaine = preg_replace("/&([A-Za-z]{2})(lig);/","$1",$chaine);
	$chaine = str_replace("&","-et-",$chaine);
	$chaine = preg_replace("/&[^;]+;/","",$chaine);
	$chaine = preg_replace("/[^A-Za-z0-9-]/","-",$chaine);
	$chaine = preg_replace("/-+/","-",$chaine);
	$chaine = trim($chaine,"-");
	$chaine = strtolower($chaine);
	return $chaine;
};

function utils_valid_url($url) {return !filter_var($url, FILTER_VALIDATE_URL) === false;}
function utils_valid_email($url) {return !filter_var($url, FILTER_VALIDATE_EMAIL) === false;}
function utils_valid_username($username) {return preg_match("/^[A-Za-z0-9]{5,}$/", $username);}
function utils_valid_password($password) {
	return preg_match("/^[A-Za-z0-9]{8,}$/", $password)
		&& preg_match("/[A-Z]/", $password)
		&& preg_match("/[0-9]/",$password)
		&& preg_match("/[a-z]/", $password);
}
function utils_password_error($newPassword = false) {
	return "Votre ".($newPassword ? 'NOUVEAU ' : '')."mot de passe doit comporter au moins 8 caractères composés de chiffres (au moins 1), lettres majuscules (au moins 1) et lettres minuscules (au moins 1) non accentuées de l'alphabet latin.";
}
function utils_username_error() {
	return "Votre pseudonyme doit comporter au moins 5 caractères composés de chiffres et lettres (majuscules ou minuscules) non accentuées de l'alphabet latin.";
}

function utils_posted($name) {
	if(utils_has_s_post($name) && utils_s_post($name) !== null) return ' value="'.htmlentities(utils_safe_post($name)).'"';
	return '';
}
function utils_input($title, $name, $type = 'text', $others = '', $help = '') {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><input type="'.$type.'" id="'.$name.'" name="'.$name.'"'.utils_posted($name).' '.$others.'/>'.($help == '' ? '' : ' <span class="help">'.$help.'</span>').'</div>'.
	'</div>';
}
function utils_select($title, $name, $options, $others = '', $help = '') {
    $selected_value = utils_has_s_post($name) ? utils_s_post($name) : null;
    $text_options = '';
    foreach ($options as $key => $value) {
        $text_options .= '<option '.($key === $selected_value ? 'selected' : '').' value="'.$key.'">'.$value.'</option>';
    }
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><select id="'.$name.'" name="'.$name.'"'.' '.$others.'>'.$text_options.'</select>'.($help == '' ? '' : ' <span class="help">'.$help.'</span>').'</div>'.
		'</div>';
}
function utils_required_input($title, $name, $type = 'text', $others = '', $help = '') {
	return utils_input($title.' <span class="required">*</span>', $name, $type, $others, $help);
}
function input_text($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'text', $others, $help);
}
function input_url($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'url', $others, $help);
}
function input_password($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'password', $others, $help);
}
function utils_textarea($title, $name, $others = '', $help = '') {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><textarea'.(utils_s_post($name, '') !== '' ? ' class="inputed"': '').' id="'.$name.'" name="'.$name.'" '.$others.'>'.(utils_has_s_post($name) ? htmlentities(utils_safe_post($name)) : '').'</textarea>'.($help == '' ? '' : ' <span class="help">'.$help.'</span>').'</div>'.
	'</div>';
}
function utils_checkbox($title, $name) {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><input type="checkbox" id="'.$name.'" name="'.$name.'"'.(utils_has_s_post($name) ? ' checked="checked"' : '').'/></div>'.
	'</div>';
}
function utils_date_input($title, $name, $y = 0, $m = 0, $d = 0) {
	$currentYear = intval(ltrim(date("Y"), '0'));
	if($d < 1 || $d > 31) $d = intval(date("j"));
	if($m < 1 || $m > 12) $m = intval(date("n"));
	if($y < 1850 || $y > $currentYear) $y = $currentYear;
	$selectDay = '<select id="'.$name.'" name="'.$name.'-day">';
		for($i = 1; $i <= 31; ++$i) $selectDay .= '<option value="'.$i.'"'.($d == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectDay .= '</select>';
	$selectMonth = '<select name="'.$name.'-month">';
		for($i = 1; $i <= 12; ++$i) $selectMonth .= '<option value="'.$i.'"'.($m == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectMonth .= '</select>';
	$selectYear = '<select name="'.$name.'-year">';
		for($i = min($y, $currentYear - 100); $i <= $currentYear; ++$i) $selectYear .= '<option value="'.$i.'"'.($y == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectYear .= '</select>';
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value date">'."JJ $selectDay /MM $selectMonth /AAAA $selectYear".'</div>'.
	'</div>';
}
function utils_datalist($name, Set $set) {
	return '<datalist id="'.$name.'"><option value="'.implode('"/><option value="', $set->values()).'"/></datalist>';
}

function utils_get_integer($s) {return ctype_digit($s) ? intval(ltrim($s, '0')) : false;}
function utils_check_day($s) {
	$v = utils_get_integer($s);
	return $v === false ? false : ($v >= 1 && $v <= 31);
}
function utils_check_month($s) {
	$v = utils_get_integer($s);
	return $v === false ? false : ($v >= 1 && $v <= 12);
}
function utils_check_year($s) {
	$v = utils_get_integer($s);
	$currentYear = intval(ltrim(date("Y"), '0'));
	return $v === false ? false : ($v >= 1850 && $v <= $currentYear);
}
function utils_get_date($y, $m, $d) {
	$y = $y.'';
	$m = $m.'';
	$d = $d.'';
	$strlen_y = strlen($y);
	$strlen_m = strlen($m);
	$strlen_d = strlen($d);
	if($strlen_y == 1) $y = '000'.$y;
	if($strlen_y == 2) $y = '00'.$y;
	if($strlen_y == 3) $y = '0'.$y;
	if($strlen_m == 1) $m = '0'.$m;
	if($strlen_d == 1) $d = '0'.$d;
	return $y.'-'.$m.'-'.$d;
}

function utils_unescape($texte) {
	$texte = str_replace("\\\"","\"",$texte);
	$texte = str_replace("\\'","'",$texte);
	return $texte;
}
function utils_unescape_s_post($texte) {
	$texte = utils_unescape($texte);
	$texte = str_replace("\\\\","\\",$texte);
	return $texte;
}
function utils_has_s_get($key) {
	return isset($_GET[$key]);
}
function utils_has_s_post($key) {
	return isset($_POST[$key]);
}
function utils_s_get($key, $default = '') {
	return trim(isset($_GET[$key]) ? $_GET[$key] : $default);
}
function utils_s_post($key, $default = '') {
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}
function utils_safe_post($name, $alt = '') {
	return trim(utils_unescape_s_post(utils_s_post($name, $alt)));
}

function utils_microtime() {
	list($micro, $sec) = explode(" ", microtime());
	$realsec = bcmul($sec, "1000000");
	$realmicro = $micro * 1000000;
	return bcadd($realsec, $realmicro, 0);
}

function utils_upload(
        $name,
        $updir,
        $file_name = null,
        $extension = null,
        $allowed_extensions = array(
                'jpg', 'jpeg', 'gif', 'png', 'tif' , 'tiff', 'bmp', 'pdf', 'doc', 'docx', 'rtf', 'odt')) {
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	$nom = '';
	$erreur = '';
	if (isset($_FILES[$name]) AND $_FILES[$name]['error'] == UPLOAD_ERR_OK) {
		// Testons si le fichier n'est pas trop gros.
		$tailleMaximale = 64*1024*1024; // 64 Mo.
		if ($_FILES[$name]['size'] <= $tailleMaximale) {
			// Testons si l'extension est autorisée
			$infosfichier = pathinfo($_FILES[$name]['name']);
			$extension_upload = "";
			if(isset($infosfichier['extension'])) $extension_upload = strtolower($infosfichier['extension']);
			if (in_array($extension_upload, $allowed_extensions)) {
				// On peut valider le fichier et le stocker définitivement
				if($extension_upload == 'jpeg')	$extension_upload = 'jpg';
				else if($extension_upload == 'tif')	$extension_upload = 'tiff';
				if($extension_upload != '') $extension_upload = '.'.$extension_upload;
				if ($extension !== null)
				    $extension_upload = $extension;
				$time = $file_name === null ? utils_microtime() : $file_name;
				$nom = $updir.'/'.$time.$extension_upload;
				if(!move_uploaded_file($_FILES[$name]['tmp_name'], $nom))
					$erreur = 'Unable to save file. Please re-try later!';
			} else $erreur = "Disalloed file extension (.".$extension_upload.") ".( empty($allowed_extensions) ? "" : "Allowed extensions: .".implode(', .',$allowed_extensions));
		} else $erreur = ("Fils is too big (expected at most ".($tailleMaximale/1024/1024)." Mb).");
	} else $erreur = ("Error ".$_FILES[$name]['error'].": missing file field ($name).");
	return array('file' => $nom, 'error' => $erreur);
}
function utils_upload_compcard($model_id) {
	$name = 'compcard';
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	if (isset($_FILES[$name]) AND $_FILES[$name]['error'] == UPLOAD_ERR_OK) {
		// Testons si le fichier n'est pas trop gros.
		$tailleMaximale = 64*1024*1024; // en Mo.
		if ($_FILES[$name]['size'] <= $tailleMaximale) {
			// Testons si l'extension est autorisée
			$infosfichier = pathinfo($_FILES[$name]['name']);
			$extension_upload = "";
			if(isset($infosfichier['extension'])) $extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('pdf');
			if (in_array($extension_upload, $extensions_autorisees)) {
				// On peut valider le fichier et le stocker définitivement
				$extension_upload = '.'.$extension_upload;
				$nom = utils_compcard_path($model_id);
				if(move_uploaded_file($_FILES[$name]['tmp_name'], $nom))
					return $nom; //return str_replace(server_dir().'/','',$nom);
				else return false;
			} else utils_message_add_error( "Erreur lors de l'envoi de fichier : extension (.".$extension_upload.") non autoris&eacute;e.<br/>".( empty($extensions_autorisees) ? "" : "Les fichiers accept&eacute;s doivent avoir une des extensions suivantes :<br/>.".implode(', .',$extensions_autorisees)."<br/>" ).( empty($extensions_interdites) ? "" : "Les extensions suivantes sont refus&eacute;es :<br/>.".implode(', .', $extensions_interdites) ) );
		} else utils_message_add_error("Votre fichier est trop gros (".($tailleMaximale/1024/1024)." Mo au maximum).");
	} else utils_message_add_error("Erreur ".$_FILES[$name]['error']." lors de l'envoi de fichier : champ ($name) inexistant.");
	return false;
}
function delTree($dir) {
	if(!file_exists($dir))
		return true;
	if(is_file($dir))
		return unlink($dir);
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function utils_local_photo($photopath) {
	if(is_file($photopath)) {
		$photoinfos = getimagesize($photopath);
		if($photoinfos) {
			$filename = pathinfo($photopath, PATHINFO_FILENAME);
			if (ctype_digit($filename)) {
				$time = $filename;
				return array(
					'path' => $photopath,
					'basename' => basename($photopath),
					'url' => str_replace(server_dir(),server_http(),$photopath),
					'width' => $photoinfos[0],
					'height' => $photoinfos[1],
					'time' => $time,
					'date' => date('d/m/Y', bcdiv($time, 1000000))
				);
			}
		}
	}
	return false;
}
function utils_local_photos($path) {
	if(file_exists($path) && is_dir($path)) {
		$list = scandir($path);
		$photos = array();
		foreach($list as $file) {
			$photopath = $path.'/'.$file;
			$photo = utils_local_photo($photopath);
			if($photo) $photos[] = $photo;
		}
		return $photos;
	}
	return false;
}

function capture_start() {
	ob_start();
}
function capture_end(&$content) {
	$content .= ob_get_contents();
	ob_end_clean();
}

function get_nb_followers($instagram_username) {
	$json_string = file_get_contents('https://www.instapi.io/u/'.$instagram_username);
	if (!$json_string)
		$json_string = file_get_contents('https://www.instagram.com/'.$instagram_username.'/?__a=1');
	if ($json_string) {
		$json_content = json_decode($json_string);
		if (isset($json_content->graphql->user->edge_followed_by->count)) {
			$nb_followers = $json_content->graphql->user->edge_followed_by->count;
			$text = ''.$nb_followers;
			if ($nb_followers % 1000000 != $nb_followers)
				$text = round($nb_followers / 1000000., 1).'M';
			else if ($nb_followers % 1000 != $nb_followers)
				$text = round($nb_followers / 1000., 1).'K';
			return $text;
		}
	}
	return false;
}

function utils_ensure_favourites() {
	if (!isset($_SESSION['favourites']))
		$_SESSION['favourites'] = array();
}
function utils_has_favourite($id) {
	utils_ensure_favourites();
	return array_key_exists($id, $_SESSION['favourites']);
}
function utils_count_favourites() {
	utils_ensure_favourites();
	return count($_SESSION['favourites']);
}
function utils_add_favourite($id) {
	utils_ensure_favourites();
	$_SESSION['favourites'][$id] = null;
}
function utils_remove_favourite($id) {
	if (utils_has_favourite($id))
		unset($_SESSION['favourites'][$id]);
}

function utils_mail($mail, $subject, $message) {
	$serveur = server_http();
	$headers = "Return-Path: ".$mail."\n";
	$headers .= "X-Mailer: PHP ".phpversion()."\n";
	// $headers .= "Reply-To: ".$mail."\n";
	$headers .= "Organization: SILK MANAGEMENT"."\n";
	$headers .= "X-Priority: 3 (Normal)"."\n";
	$headers .= "Mime-Version: 1.0"."\n";
	$headers .= "Content-Transfer-Encoding: 8bit"."\n";
	$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
	return mail($mail, $subject, $message, $headers);
}

function get_models_for_articles($models) {
	$models_with_articles = array();
	foreach ($models as $model) if ($model->show_article && $model->article) {
		$models_with_articles[] = $model;
	}
	if (count($models_with_articles)) {
		$sort_func = function($a, $b) {
			$t = $a->article_rank - $b->article_rank;
			if (!$t)
				$t = strcmp($a->first_name, $b->first_name);
			if (!$t)
				$t = strcmp($a->last_name, $b->last_name);
			return $t;
		};
		usort($models_with_articles, $sort_func);
	}
	return $models_with_articles;
}

function print_models_for_articles($models_with_articles) {
	$html = '';
	capture_start();
	if (count($models_with_articles)) {
		?>
		<div class="articles mt-5">
			<?php
			$count_selected = count($models_with_articles);
			$n_rows = (int)($count_selected / 3);
			if ($count_selected - 3 * $n_rows) ++$n_rows;
			$index_model = 0;
			for ($i_row = 0; $i_row < $n_rows; ++$i_row) { ?>
				<div class="row mb-4">
					<?php for ($i_col = 0; $i_col < 3; ++$i_col) { ?>
						<div class="col-md align-self-center">
							<?php
							$index_model = 3 * $i_row + $i_col;
							if ($index_model < $count_selected) {
								$model = $models_with_articles[$index_model];
								$article = $model->article;
								for($i = 1; $i <= 4; ++$i) {
									$to_search = '{photo '.$i.'}';
									$photo_id = 'photo_'.$i;
									if ($model->$photo_id) {
										$article = str_replace($to_search, '<img class="img-fluid" src="'.($model->getPhotoByBasename($model->$photo_id)['url']).'"/>', $article);
									}
								}
								?>
								<div class="article" onclick="location.href = 'article.php?id=<?php echo $model->model_id;?>';"><?php echo $article;?></div>
								<?php
							}
							?>
						</div>
					<?php } ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
	capture_end($html);
	return $html;
}



?>
