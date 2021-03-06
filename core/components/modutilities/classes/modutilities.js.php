<?php
$res = [];
$user = [];
$lex = "{}";
/** @var modX $modx */
/** @var modResource $resource */
if (!class_exists('modUtilities')) {
	return FALSE;
}
if ($modx->config['use_modUtilFrontJs_resource'] == TRUE) {
	if (!$modx->resource) {
		$r = $modx->newQuery('modResource');
		$r->where($modx->resourceIdentifier);
		$r->limit(1);
		$r->select('alias,pagetitle,properties,type,template,show_in_tree,published,searchable,pub_date,parent,menuindex,longtitle,link_attributes,isfolder,introtext,id,hidemenu,hide_children_in_tree,description,createdon,cacheable,class_key,contentType');
		if ($r->prepare() and $r->stmt->execute() and $res = $r->stmt->fetch(PDO::FETCH_ASSOC)) {
			$res['properties'] = json_decode($res['properties']);
		}
		$res['id'] = $modx->resourceIdentifier;
	} else {
		$res = $modx->resource->toArray();
	}

}
if ($modx->config['use_modUtilFrontJs_user'] == TRUE) {
	if ($modx->user instanceof modUser) {
		/** @var modUser $user */
		$user = $modx->user->_fields;
		if ($user['id']) {
			$user['member'] = $modx->util->member($user)[0];
			$p = $modx->newQuery('modUserProfile');
			$p->select('address,extended,email,country,city,photo,state,phone,gender,fullname,fax,mobilephone,website,zip');
			$p->limit(1);
			$p->where(['internalKey' => $user['id']]);
			if ($p->prepare() and $p->stmt->execute() and $profile = $p->stmt->fetch(PDO::FETCH_ASSOC)) {
				$user['profile'] = $profile;
				$user['profile']['extended'] = json_decode($user['profile']['extended']);
			}
			unset($user['salt']);
			unset($user['password']);
			unset($user['remote_data']);
			unset($user['remote_key']);
			unset($user['session_stale']);
			unset($user['sudo']);
			unset($user['hash_class']);
		} else {
			$user = [];
		}
	}
}
if ($modx->config['use_modUtilFrontJs_Lexicon'] == TRUE) {
	if (!function_exists('esc09430')) {
		function esc09430($lex)
		{
			return strtr($lex, ['\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/']);
		}
	}
	$entries = $modx->lexicon->fetch();
	$modx->lexicon->load('extaext:default');
	$lex = '{';
	foreach ($entries as $k => $v) {
		$lex .= "'$k': " . '"' . esc09430($v) . '",';
	}
	$lex = trim($lex, ',');
	$lex .= '}';

}


ob_start();

//https://regex101.com/r/vrH6XK/1/ cookie
//https://regex101.com/r/vrH6XK/2 trim
?>
//javascript
modUtilities = {
	'resource': <?=json_encode($res, 256) ?: '{}'?>,
	'lang': <?=$lex?>,
	'user': <?=json_encode($user, 256) ?: '{}'?>,
	'converterRule': <?=json_encode($modx->util->converterRule, 256) ?: '{}'?>,
	'translitRule': <?=json_encode(modutilities::translitRule, 256) ?: '{}'?>,
}
<?php
return ob_get_clean();
