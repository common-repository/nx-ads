(function($) {
	'use strict';

	$(function() 
	{
		nxAdInit.apply(
		{
			config: JSON.parse($('#nxAdsData').html()),
			$area:  $("#plugin-nx-ads"),
			bind: {},
			cleanName: function(_, v) 
			{
				return v.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
			},
			removeEl: function($el)
			{
				$el.hide("fast", function() 
				{
					$el.remove();
				});
			},
			setFieldNames: function($inputs, val) 
			{
				$inputs.each(function() 
				{
					var $el = $(this);
					var fieldSource = $el.attr("data-field");
					var fieldName = fieldSource.replace("%fieldname%", val);
					$el.attr("name", fieldName);
				});
			},
			storeFieldNames: function($inputs, val)
			{
				$inputs.each(function() 
				{
					var $el = $(this);
					var name = $el.attr("name");

					$el.attr('data-field', name);
					name = name.replace("%fieldname%", val);
					$el.attr("name", name);
				});
			},
			scrollTo: function($el)
			{
				$('html, body').animate({
					scrollTop: $el.offset().top - 100
				}, 400);
			},
			codeMirror: function(myTextArea, opts)
			{
				if (myTextArea && window.wp.CodeMirror) {

					var cmOpts = {
						value: myTextArea.value,
					};

					for (var index in opts) {
						cmOpts[index] = opts[index];
					}

					var myCodeMirror = wp.CodeMirror(function(elt) 
					{
						myTextArea.parentNode.appendChild(elt);
						myTextArea.style.display = 'none';
						
					}, cmOpts);

					myCodeMirror.on('change',function(cMirror)
					{
						myTextArea.value = cMirror.getValue();
					});
				}
			}
		}, [$, function() 
		{
			for (var index in this.bind) {
				this.bind[index].apply(this);
			}
		}]);
	});
})(jQuery);
