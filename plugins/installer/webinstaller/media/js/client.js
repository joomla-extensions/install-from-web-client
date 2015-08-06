// Only define the Joomla namespace if not defined.
if (typeof(Joomla) === 'undefined') {
	var Joomla = {};
}

Joomla.webinstall = {

	update : function(layout) {
		jQuery("input[name=task]").val('');
		jQuery("input[name=layout]").val(layout);
		var form = jQuery('#adminForm');
		form.submit()
	},

	resort : function() {
		jQuery("input[name=reload]").val(1);
		jQuery("input[name=task]").val('');
		var form = jQuery('#adminForm');
		form.submit()
	},

	resetsearch : function () {
		jQuery("input[name=task]").val('');
		var search = jQuery("input[name=filter-search]").val();
		if (search != '') {
			jQuery("input[name=filter_search]").val('');
            jQuery("input[name=layout]").val('');
			var form = jQuery('#adminForm');
			form.submit()
		}
	},
	search : function () {
		jQuery("input[name=task]").val('');
        jQuery("input[name=layout]").val('searchresult');
		var search = jQuery("input[name=filter_search]").val();
		if (search != '') {
			var form = jQuery('#adminForm');
			form.submit()
		}
	},
    install : function(extension) {
        var form = jQuery('#adminForm');
        jQuery("input[name=installtype]").val('url');
        jQuery("input[name=install_url]").val(extension);
        form.submit()
    }
	,
	showsubmitform : function() {
        jQuery('#plg-webinstaller-extension-form').show();
        jQuery('#plg-webinstaller-extension-info').hide();
        return false;
	},
    disablesubmitform : function() {
        jQuery('#plg-webinstaller-extension-form').hide();
        jQuery('#plg-webinstaller-extension-info').show();
        return false;
    }
};
