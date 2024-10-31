(function() {
	var config = JSON.parse(document.getElementById('nxAdsData').innerHTML);
	var container = config.container;
	var cValues = new Array();

	container.forEach(function(entry) 
	{
		cValues.push({text: entry, value: entry});
	});

	tinymce.create('tinymce.plugins.nx_ads', 
	{
		init : function(ed, url) 
		{ 
			ed.addButton('nx_ads', 
			{
				title : config.button_title,
				cmd : 'nx_ads',
				image : url + '/nx-ads.png'
			});
 
			ed.addCommand('nx_ads', function() 
			{
				ed.windowManager.open({
					title: config.window_title,
					width: 350,
					height: 100,
					body: [
						{
							type: 'listbox',
							name: 'container',
							label: 'Container',
							values: cValues
						}
					],
					onsubmit: function(e) 
					{
						ed.insertContent('%' + e.data.container + '%');
					}
				});
			});
		}
	});

	tinymce.PluginManager.add( 'nx_ads', tinymce.plugins.nx_ads );
})();