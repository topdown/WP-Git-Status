<?php
// Some security to stop the trolls

include_once '../../../wp-load.php';

get_currentuserinfo();
if (!current_user_can('manage_options'))
{
	die();
}
?>
<pre style="display: block; float: left; background: #4d4d4d; padding: 10px; color: #fff;">
	<?php echo shell_exec("git status"); ?>
</pre>
