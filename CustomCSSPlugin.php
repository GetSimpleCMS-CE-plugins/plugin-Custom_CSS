<?php
/*
Plugin Name: Custom CSS
Description: Add custom CSS to theme
Version: 0.1.2
Author: Pavol Bokor
Author URI: https://www.4enzo.sk
Licence: GNU DPL v 3.0
*/

# get correct id for plugin
$thisfile_customcss=basename(__FILE__, ".php");
$customcss_file=GSDATAOTHERPATH .'CustomCSS.xml';

# add in this plugin's language file
i18n_merge($thisfile_customcss) || i18n_merge($thisfile_customcss, 'en_US');

# register plugin
register_plugin(
	$thisfile_customcss, 								# ID of plugin, should be filename minus php
	i18n_r($thisfile_customcss.'/CUSTOMCSS_TITLE'), 	# Title of plugin
	'0.1.3', 											# Version of plugin
	'Pavol Bokor / CE Team',							# Author of plugin
	'https://www.4enzo.sk', 							# Author URL
	i18n_r($thisfile_customcss.'/CUSTOMCSS_DESC'), 		# Plugin Description
	'theme', 											# Page type of plugin
	'customcss_show'  									# Function that displays content
);

# hooks
	add_action('theme-footer','customcss_echo_to_theme'); 
	add_action('theme-sidebar','createSideMenu',array($thisfile_customcss, i18n_r($thisfile_customcss.'/CUSTOMCSS_TITLE'))); 

# load codemirror
	register_script('codemirror', $SITEURL . $GSADMIN . '/template/js/codemirror/codemirror.min.js', '6.65.7', FALSE);
	register_script('codemirror-style', $SITEURL . $GSADMIN . '/template/js/codemirror/clike.min.js', '6.65.7', FALSE);
	
	register_style('codemirror-css', $SITEURL . $GSADMIN . '/template/js/codemirror/codemirror.min.css', 'screen', FALSE);
	register_style('codemirror-theme', $SITEURL . $GSADMIN . '/template/js/codemirror/blackboard.min.css', 'screen', FALSE);
	
	queue_script('codemirror', GSBACK);
	queue_script('codemirror-style', GSBACK);
	
	queue_style('codemirror-css', GSBACK);
	queue_style('codemirror-theme', GSBACK);

# get XML data
if (file_exists($customcss_file)) {
	$customcss_data = getXML($customcss_file);
}
# print custom CSS to theme footer
$echo_to_theme = '';
if(isset($customcss_data->customcss_content)) $echo_to_theme = $customcss_data->customcss_content;

function customcss_echo_to_theme() {
	global $echo_to_theme;
	echo 
"
<!-- Custom CSS -->
<style>
" . $echo_to_theme . "
</style>
";
}

function customcss_show() {
	global $customcss_file, $customcss_data, $thisfile_customcss;
	$success=$error=null;
	
	// submitted form
	if (isset($_POST['submit'])) {		
		
		if ($_POST['customcss_content'] != '') {
			$resp['customcss_content'] = $_POST['customcss_content'];
		}
		
		# if there are no errors, save data
		if (!$error) {
			$xml = @new SimpleXMLElement('<item></item>');
			if(isset($resp['customcss_content'])) $xml->addChild('customcss_content', htmlspecialchars($resp['customcss_content']));
							
			if (! $xml->asXML($customcss_file)) {
				$error = i18n_r('CHMOD_ERROR');
			} else {
				$customcss_data = getXML($customcss_file);
				$success = i18n_r('SETTINGS_UPDATED');
			}
		}
	}
	?>

	<h3><?php i18n($thisfile_customcss.'/CUSTOMCSS_TITLE'); ?></h3>
	
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		<?php
			$value = '';
			if(isset($customcss_data->customcss_content)) $value = $customcss_data->customcss_content;
		?>
		<p>
			<textarea id="lb_customcss_title" name="customcss_content" class="text" type="text"><?php echo $value; ?></textarea>
		</p>
		<p>
			<input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" />
		</p>

	</form>
	
	<div id="paypal">
		<small> <a href="https://github.com/bokorpavol/CustomCSS" target="_blank">Custom CSS on GitHub</a>::<a href="https://www.4enzo.sk/" target="_blank">Author website</a></small>
	</div>
	
	<script>
	var editor = CodeMirror.fromTextArea(document.querySelector('#lb_customcss_title'), {
		theme: "blackboard",
		lineNumbers: true,
		matchBrackets: true,
		indentUnit: 4,
		indentWithTabs: true,
		lineWrapping: true,
		enterMode: "keep",
		tabMode: "shift",
		mode: 'clike',
		inlineDynamicImports: true
	});
	</script>
	
<?php
}
?>