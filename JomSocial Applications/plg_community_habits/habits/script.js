var Habits = {
	cAppsChangeStatus: function(id, type){
		jax.call('community', 'plugins,habits,ajaxChangeStatus',id, type);
	}	
}