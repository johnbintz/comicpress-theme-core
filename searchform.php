<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
	<div>
		<input type="text" value="Search Site..." name="s" id="s" onfocus="this.value=(this.value=='Search Site...') ? '' : this.value;" onblur="this.value=(this.value=='') ? 'Search Site...' : this.value;" />
		<input type="submit" id="searchsubmit" value="&raquo;" />
	</div>
</form>