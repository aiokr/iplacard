<script>
	jQuery(function($){
		$('#profile_edit_close').hide();
	});
</script>

<div id="profile_edit_open">
	<p>如非必要且征得代表许可，您不应编辑代表资料。代表资料的变动将同时显示给对应代表。</p>
	
	<p><a class="btn btn-info" id="profile_edit_enable" onclick="$('#profile_edit_close').show(); $('#profile_edit_open').hide(); $('.profile_editable').editable('toggleDisabled');"><?php echo icon('pencil');?>编辑代表资料</a></p>
</div>

<div id="profile_edit_close">
	<p>现在可以单击需要编辑的资料内容并在弹出的提示框中修改。</p>
	
	<p><a class="btn btn-success" id="profile_edit_disable" onclick="$('#profile_edit_open').show(); $('#profile_edit_close').hide(); $('.profile_editable').editable('toggleDisabled');"><?php echo icon('check');?>结束代表资料</a></p>
</div>