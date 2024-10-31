"use strict";

var nxAdInit = function($, callback)
{
	var self = this;
	this.bind.codeMirror = function()
	{
		this.codeMirror(document.getElementById('nxAdsStyle'),
		{
			type: 'text/css',
			lineNumbers: true,
			smartIndent: false
		});
	};

	this.bind.helpToggle = function() {
		$(document).on("click", "a[data-show-help]", function(e) 
		{
			e.preventDefault();
			var $el = $(this).parent().next();



			if (!$el.is("[data-info-toggle]")) {
				$el = $el.find("[data-info-toggle]");
			}

			$el.slideToggle("fast");

			return false;
		});

		$(document).on("click", "a[data-show-text]", function(e) 
		{
			e.preventDefault();
			var $row = $(this).parents('[data-container]');
			var name = $row.attr('data-container');
			var $el = $row.find("[data-text='"+ name +"']");
	
			$el.slideToggle("fast");

			return false;
		});
	}

	this.bind.toggle = function()
	{
		// Add toggle mechanism to Restrict visibility
		/*$('#nx_ads_section_ads h2:first-of-type')
		.attr('data-toggle', true)
		.next().attr('data-toggle-content', true)
		.next().attr('data-toggle-content', true);*/


		$('[data-toggle]', this.$area).on('click', function()
		{
			$(this).next().toggle();
			$(this).next().next().toggle();
			$(this).toggleClass('toggle-open');
		});
	};

	this.bind.dropdowns = function()
	{
		//  updates the correct hidden types value on the table
		this.$area.on('click', '[data-dropdown] input[type=checkbox]', function()
		{

			var self = $(this);
			var $container = $(this).closest('[data-dropdown]');
			var $options = $container.find('input[type=checkbox]:checked');
			var $types = $container.find('[data-types]');
			var $entriesContainer = $container.find('[data-entries]');
			var $dropdownChoose = $container.find('[data-dropdown-choose]');


			if ($entriesContainer.length) {
				
				$entriesContainer.empty();
				
				if ($options.length) {
					$dropdownChoose.addClass('hidden');
				} else {
					$dropdownChoose.removeClass('hidden');
				}
				
				$options.each(function() 
				{
					var $option = $(this);
					var $el = $("<span />").addClass('entry');
					$el.text($option.parent().text().trim());
					$entriesContainer.append($el);
				});

				
			} else {
				$types.text($options.length);
			}
		});

		this.$area.on('click' , '[data-dropdown-handle]', function()
		{
			var $container = $(this).parent();

			if (!$container.hasClass('open')) {
				$('[data-dropdown]', self.$area).removeClass('open');
			}

			$container.toggleClass('open');

			if ($container.hasClass('open')) {;
				window.setTimeout(function() 
				{
					var globalClick = function(e) 
					{
						var $target = $(e.target);

						if (!$target.closest('[data-dropdown]').length) {
							$container.removeClass('open');
							$(document).off('click', globalClick);	
						}
					}

					$(document).on('click', globalClick);
				});
			}
		});
	};

	this.bind.inread = function() 
	{
		function uuidv4() {
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			  	var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			  	return v.toString(16);
			});
		}
		  
		 // console.log(uuidv4())

		var $inreadTemplate = $($('#inreadSetTemplate').html());
		var $inreadTable = $('#inreadAddon');

		$('[data-add-inread-set]', this.$area).on('click', function(e) 
		{
			e.preventDefault();
		
			var $newData   = $inreadTemplate.clone();
			var entryId    = uuidv4();
			var $inputs = $newData.find("select, input");

			self.setFieldNames($inputs, entryId);
			$inreadTable.find('tbody').append($newData);
		});

	};

	this.bind.containers = function()
	{
		var self = this;
		var $containerTable = $('#containerTable', this.$area);
		
		var $containerModel = $($('#containerTemplate').html());
		var $containerModelSpecial = $($('#containerTemplateSpecial').html());
		var $containerModelInread = $($('#containerTemplateInread').html());

		$('#containerTemplateSpecial').remove();
		$('#containerTemplateInread').remove();
		$('#containerTemplate').remove();

		var $containerSel = $('#containerType', this.$area);

		$containerTable.on('click', '.btn-remove', function()
		{
			var $el = $(this);
			var $tr = $el.closest("tr");
			var name =  $tr.find('[data-name]').first().text();
			if (name.length) {

				var $opt = $containerSel.find('option[value='+name+']');

				if ($opt.length) {
					$opt.removeAttr('hidden');
				}

				if (confirm( self.config.remove_container.replace('%name%', name))) {
					self.removeEl($tr);
				}
			} else {
				self.removeEl($tr);
			}

			return false;
		});

		$('[data-add-container]', this.$area).on('click', function() 
		{
			var val = $containerSel.val();
			var $newData;

			if (val === "sales" || val === "privacy" || val === "inread") {

				if (val === "inread") 
					 $newData = $containerModelInread.clone();
			 	else $newData = $containerModelSpecial.clone();
				

				var $dataContainer = $newData.find('[data-container]');
				var $showText = $dataContainer.find('[data-show-text]');
				$showText.remove();
				$dataContainer.attr('data-container', val);
				$dataContainer.removeClass('text-hidden');
				

			} else {
				$newData = $containerModel.clone();
			}
			
			var $inputs    = $newData.find('input');
			var $custInput = null;
			
			if (val.length) {
				$containerSel.find('option:selected').prop("selected", false).hide();
				self.storeFieldNames($inputs, val);

			} else {
				self.storeFieldNames($inputs, val);
				$custInput = $("<input type='text' maxlength='40' required />");
				$custInput.attr('placeholder', self.config.name_placeholder);

				$newData.find('[data-input]')
				.html($custInput)
				.removeAttr('data-name');

				$custInput.on('input', function() 
				{
					
					$(this).val(self.cleanName);

					var val = $(this).val();
					$newData.find('[data-placeholder]').text('%' + val + '%');
					self.setFieldNames($inputs, val);
				});
			}

		
			$newData.find('[data-name]').text(val);
			$newData.find('[data-placeholder]').text('%' + val + '%');
			$containerTable.find(' > tbody').append($newData);

			if ($custInput !== null) {
				$custInput.focus();
			}

			if (val == 'inread') {
				self.bind.toggle();
				self.bind.inread();
			}
			self.scrollTo($newData);
			return false;
		});
	}; // bindContainers

	this.bind.zones = function()
	{	
		var $zoneTable = $('#zoneTable', this.$area);
		var $zoneModel = $($('#zoneTemplate').html());
		var $zoneCount = $('#zoneCount');
		
		$zoneTable.on('click', '.btn-remove', function()
		{
			var $el = $(this);
			var $tr = $el.closest("tr");
			var $name = $tr.find('input[data-zone-name]');

			if (!$tr.is('[data-init]') && 
				confirm(self.config.remove_area.replace('%name%', $name.val()))) {
				self.removeEl($tr);
			} else if ($tr.is('[data-init]')) {
	
				var count = parseInt($zoneCount.val()) - 1;
				$zoneCount.val(count);
				self.removeEl($tr);
			}

			return false;
		});

		var bindInput = function($input)
		{
			var $tr = $input.closest('tr');
			var $inputs  = $tr.find('input');


			$input.on('input', function() 
			{
				$(this).val(self.cleanName);
				
				var val = $(this).val();
				self.setFieldNames($inputs, val);
			});
		};

		$zoneTable.find('input[data-zone-name]').each(function() 
		{
			bindInput($(this));
		});

		$('[data-add-zone]', this.$area).on('click', function(e) 
		{
			e.preventDefault();
			var $newData   = $zoneModel.clone();
			var $inputName = $newData.find('input[data-zone-name]');
			var $inputDesc = $newData.find('input[data-zone-desc]');
			var $inputID   = $newData.find('input[data-zone-id]');
			var $spanID    = $newData.find('span[data-zone-id]');
			var count	   = parseInt($zoneCount.val()) + 1;

			$inputID.val(count);
			$spanID.text(count);
			$zoneCount.val(count);

			if ($inputName.length) {
				bindInput($inputName);
			}

			$zoneTable.find('tbody').append($newData);

			if ($inputName.length) {
				$inputName.focus();
			}

			self.scrollTo($newData);
			return false;
		});
	}; // bindZones

	this.bind.tabs = function() 
	{
		var $tabs = $('[data-mdnx-tabs]', this.$area);
		var $tabLinks = $tabs.find('[data-mdnx-tab-target]');
		var $tabContent = $tabs.find('[data-mdnx-tab]');
		var $currentTab = $tabs.find('[data-current-tab]');

		$tabLinks.on('click', function(e) 
		{
			var $el = $(this);
			var target = $el.attr('data-mdnx-tab-target');
			e.preventDefault();

			location.hash = target;

			if ($el.hasClass('active')) return;

			$el.addClass('active').siblings().removeClass('active');
			var $newTab = $tabContent.filter("[data-mdnx-tab='"+target+"']");
			$newTab.addClass('active').siblings().removeClass('active');
		
			$currentTab.val(target);
		});

		if (location.hash && location.hash.length) {
			var hash = location.hash.substr(1);
			var $tab = $tabLinks.filter("[data-mdnx-tab-target='"+hash+"']");

			if ($tab.length) {
				$tab.trigger('click');
			}
		}

		var savedTab = $currentTab.val();

		if (savedTab.length) {
		
			var $tab = $tabLinks.filter("[data-mdnx-tab-target='"+ savedTab +"']");
			if ($tab.length) {
				$tab.trigger('click');
			}
		}

		var self = this;

		$('#submit').on('click', function(e) 
		{
			var $activeTab = $tabContent.filter(".active[data-mdnx-tab]");
			var $invalid = $activeTab.find("input:invalid").first();

			if (!$invalid.length) {
				var $invalid = self.$area.find("input:invalid").first();
				if ($invalid.length) {
					var $tab = $invalid.closest('[data-mdnx-tab]');

					if ($tab.length) {

						var name = $tab.attr('data-mdnx-tab');
						var $tab = $tabLinks.filter("[data-mdnx-tab-target='"+name+"']");

						if ($tab.length) {
							$tab.trigger('click');
						}

					}
				}

			}
		});

		$tabs.addClass('active');

	}// bindTabs

	callback.apply(this);
}