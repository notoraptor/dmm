<?php
function template($data) {
ob_start();?><!DOCTYPE html>
<html>
	<head>
		<title><?php echo $data->title; ?></title>
		<meta charset="UTF-8"/>
		<link rel="shortcut icon" type="image/x-icon" href="../data/main/favicon.ico" />
		<link rel="icon" type="image/x-icon" href="../data/main/favicon.ico"/>
		<link rel="stylesheet" href="css/style.css"/>
	</head>
	<body>
	<div class="page">
		<div class="messages"><?php echo $data->messages;?></div>
		<div class="content"><?php echo $data->content;?></div>
	</div>
	<script type="text/javascript">//<!--
	function confirmer(message) {
		return confirm(message);
	}
	function hexadecimal(str) {
		var output = '';
		for(var i = 0; i < str.length; ++i) {
			output += str.charCodeAt(i).toString(16);
		}
		return output;
	}
	function careful(indices) {
		for(let i = 0; i < indices.length; ++i) {
			const field = document.getElementById(indices[i]);
			field.value = hexadecimal(field.value);
		}
	}
	function loadNiceEditor(fieldName) {
        new nicEditor({
            iconsPath: 'nicEdit/nicEditorIcons.gif',
            buttonList: [
                'fontSize','bold','italic','underline','left','center','right','justify','link',
                'unlink','removeformat','xhtml'
            ]}).panelInstance(fieldName);
    }
	//--></script>
	</body>
</html>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
?>