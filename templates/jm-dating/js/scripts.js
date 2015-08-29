/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

//Set Module's Height script

function setModulesHeight() {
	var regexp = new RegExp("_mod([0-9]+)$");

	var jmmodules = jQuery(document).find('.jm-module') || [];
	if (jmmodules.length) {
		jmmodules.each(function(index,element){
			var match = regexp.exec(element.className) || [];
			if (match.length > 1) {
				var modHeight = parseInt(match[1]);
				jQuery(element).find('.jm-module-in').css('height', modHeight + 'px');
			}
		});
	}
}

//jQuery 'Back to Top' script

jQuery(document).ready(function(){
	
	setModulesHeight();
	
    // hide #jm-back-top first
    jQuery("#jm-back-top").hide();
    // fade in #jm-back-top
    jQuery(function () {
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 100) {
                jQuery('#jm-back-top').fadeIn();
            } else {
                jQuery('#jm-back-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        jQuery('#jm-back-top a').click(function () {
            jQuery('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
});


//Document Text Resizer script (May 14th, 08'): By JavaScript Kit: http://www.javascriptkit.com

var documenttextsizer = {

	prevcontrol : '', //remember last control clicked on/ selected
	existingclasses : '',

	setpageclass : function(control, newclass) {
		if (this.prevcontrol != '')
			this.css(this.prevcontrol, 'selectedtoggler', 'remove')
		//de-select previous control, by removing 'selectedtoggler' from it
		document.documentElement.className = this.existingclasses + ' ' + newclass//apply new class to document
		this.css(control, 'selectedtoggler', 'add')//select current control
		this.setCookie('pagesetting', newclass, 5)//remember new class added to document for 5 days
		this.prevcontrol = control
	},

	css : function(el, targetclass, action) {
		var needle = new RegExp("(^|\\s+)" + targetclass + "($|\\s+)", "ig")
		if (action == "check")
			return needle.test(el.className)
		else if (action == "remove")
			el.className = el.className.replace(needle, "")
		else if (action == "add")
			el.className += " " + targetclass
	},

	getCookie : function(Name) {
		var re = new RegExp(Name + "=[^;]+", "i");
		//construct RE to search for target name/value pair
		if (document.cookie.match(re))//if cookie found
			return document.cookie.match(re)[0].split("=")[1]
		//return its value
		return null
	},

	setCookie : function(name, value, days) {
		if ( typeof days != "undefined") {//if set persistent cookie
			var expireDate = new Date()
			var expstring = expireDate.setDate(expireDate.getDate() + days)
			document.cookie = name + "=" + value + "; path=/; expires=" + expireDate.toGMTString()
		} else//else if this is a session only cookie
			document.cookie = name + "=" + value
	},

	setup : function(targetclass) {
		this.existingclasses = document.documentElement.className//store existing CSS classes on HTML element, if any
		var persistedsetting = this.getCookie('pagesetting')
		var alllinks = document.getElementsByTagName("a")
		for (var i = 0; i < alllinks.length; i++) {
			if (this.css(alllinks[i], targetclass, "check")) {
				if (alllinks[i].getAttribute("rel") == persistedsetting)//if this control's rel attribute matches persisted doc CSS class name
					this.setpageclass(alllinks[i], alllinks[i].getAttribute("rel"))
				//apply persisted class to document
				alllinks[i].onclick = function() {
					documenttextsizer.setpageclass(this, this.getAttribute("rel"))
					return false
				}
			}
		}
	}
}

//jQuery Off-Canvas
var scrollsize;

jQuery(function() {
    // Toggle Nav on Click
    jQuery('.toggle-nav').click(function() {
    	// Get scroll size on offcanvas open
    	if(!jQuery('body').hasClass('off-canvas')) scrollsize = jQuery(window).scrollTop();
        // Calling a function
        toggleNav();
    });
});

function toggleNav() {
	var y = jQuery(window).scrollTop();
    if (jQuery('body').hasClass('off-canvas')) {
        // Do things on Nav Close
        jQuery('body').removeClass('off-canvas');
        setTimeout(function() {
	        jQuery('.sticky-bar #jm-top-bar').removeAttr('style');
	        jQuery('html').removeClass('no-scroll').removeAttr('style');
	        jQuery(window).scrollTop(scrollsize);
        }, 300);
    } else {
        // Do things on Nav Open
        jQuery('body').addClass('off-canvas');
		jQuery('.sticky-bar #jm-top-bar').css({'position':'absolute','top':y});
        setTimeout(function() {
			jQuery('html').addClass('no-scroll').css('top',-y);
        }, 300);
    }
}

//search1-ms
jQuery(document).ready(function(){
  var search1 = jQuery('.search1-ms');
  if (search1.length > 0) {

    var searchModule = search1.find('.dj_cf_search');

    var searchAdvancedsuffix = jQuery('.search1-ms.advanced-ms');
    var searchCategorysuffix = jQuery('.search1-ms.category-ms');
    var searchAdvancedlink = false;
    
    if (searchAdvancedsuffix.length > 0) {
      searchModule.append('<span class="jm-advanced-link">' + window.advancedSearch + '</span>');
      searchModule.find('#form-search').append('<span class="jm-advanced-link arrow icon-remove"></span>');
      var searchAdvancedlink = true;
    }
    
    var searchModuleAdvanced = search1.find('.jm-advanced-link');
    
    if (searchModule.length > 0 && searchAdvancedlink == true) {

        searchModule.find('.search_radius').hide();
        searchModule.find('.search_regions').hide();
        searchModule.find('.search_ex_fields').hide();
        searchModule.find('.search_type').hide();
        searchModule.find('.search_time').hide();
        searchModule.find('.search_price').hide();
        searchModule.find('.search_only_images').hide();
        searchModule.find('.search_only_video').hide();
        
        if (searchCategorysuffix.length == 0) {
          searchModule.find('.search_cats').hide();
        }
      
        searchModuleAdvanced.click(function() {
          searchModule.find('.search_radius').slideToggle('fast');
          searchModule.find('.search_regions').slideToggle('fast');
          searchModule.find('.search_ex_fields').slideToggle('fast');
          searchModule.find('.search_type').slideToggle('fast');
          searchModule.find('.search_time').slideToggle('fast');
          searchModule.find('.search_price').slideToggle('fast');
          searchModule.find('.search_only_images').slideToggle('fast');
          searchModule.find('.search_only_video').slideToggle('fast');
          
          if (searchCategorysuffix.length == 0) {
            searchModule.find('.search_cats').slideToggle('fast');
          }
          searchModule.find('#form-search').toggleClass('open');
        });
      }
  }  
});

//search2-ms
jQuery(document).ready(function(){
  var search2 = jQuery('.search2-ms');
  if (search2.length > 0) {

    var searchModule = search2.find('.dj_cf_search');

    var searchAdvancedsuffix = jQuery('.search2-ms.advanced-ms');
    var searchCategorysuffix = jQuery('.search2-ms.category-ms');
    var searchAdvancedlink = false;
    
    if (searchAdvancedsuffix.length > 0) {
      searchModule.append('<span class="jm-advanced-link">' + window.advancedSearch + '<span class="icon-share"></span></span>');
      searchModule.find('#form-search').append('<span class="jm-advanced-link arrow icon-remove"></span>');
      var searchAdvancedlink = true;
    }
    
    var searchModuleAdvanced = search2.find('.jm-advanced-link');
    
    if (searchModule.length > 0 && searchAdvancedlink == true) {

        searchModule.find('.search_radius').hide();
        searchModule.find('.search_regions').hide();
        searchModule.find('.search_ex_fields').hide();
        searchModule.find('.search_type').hide();
        searchModule.find('.search_time').hide();
        searchModule.find('.search_price').hide();
        searchModule.find('.search_only_images').hide();
        searchModule.find('.search_only_video').hide();
        
        if (searchCategorysuffix.length == 0) {
          searchModule.find('.search_cats').hide();
        }
      
      	var b_click = 0;
      
        searchModuleAdvanced.click(function() {
        	
          if(b_click == 0) {
          	  b_click = 1;
	          searchModule.find('#form-search').addClass('open');
	          
	          searchModule.find('.search_radius').slideToggle(400);
	          searchModule.find('.search_regions').slideToggle(400);
	          searchModule.find('.search_ex_fields').slideToggle(400);
	          searchModule.find('.search_type').slideToggle(400);
	          searchModule.find('.search_time').slideToggle(400);
	          searchModule.find('.search_price').slideToggle(400);
	          searchModule.find('.search_only_images').slideToggle(400);
	          searchModule.find('.search_only_video').slideToggle(400);
	          
	          if (searchCategorysuffix.length == 0) {
	            searchModule.find('.search_cats').slideToggle(400);
	          }
          } else {
          	  b_click = 0;
			  searchModule.find('.search_radius').slideToggle(400);
	          searchModule.find('.search_regions').slideToggle(400);
	          searchModule.find('.search_ex_fields').slideToggle(400);
	          searchModule.find('.search_type').slideToggle(400);
	          searchModule.find('.search_time').slideToggle(400);
	          searchModule.find('.search_price').slideToggle(400);
	          searchModule.find('.search_only_images').slideToggle(400);
	          searchModule.find('.search_only_video').slideToggle(400);
	          
	          if (searchCategorysuffix.length == 0) {
	            searchModule.find('.search_cats').slideToggle(400);
	          }
	          searchModule.find('#form-search').removeClass('open');
		  }

		setTimeout(function() {
		  var headerBackgrounHeight = jQuery('#jm-header-bg').height();
	  	  var headerModuleHeight = jQuery('#jm-header-content').outerHeight(true);

          if(headerBackgrounHeight < headerModuleHeight) {
          	jQuery('body').addClass('header-high');
          }
		}, 410);
 
        });
      }
  }  
});

// Sticky Bar
jQuery(document).ready(function(){   
    var resizeTimer;

    function resizeFunction() {
        var body = jQuery('body');
		var allpage = jQuery('#jm-allpage');
		  
		if(body.hasClass('sticky-bar')) {
		  var bar = allpage.find('#jm-top-bar');
	      if (bar.length > 0) {
		      var offset = bar.outerHeight();
		      allpage.css('padding-top', (offset) + 'px');
	      }
	    }

    };

    jQuery(window).resize(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resizeFunction, 50);
    });
    resizeFunction();
});

// Topbar login
jQuery(document).ready(function(){  
	var topbar = jQuery('#jm-top-bar');
	var loginform = topbar.find('#login-form');
	loginform.append('<span class="jm-circle-link"></span>');
	if (loginform.length > 0) {

		var loginelements = loginform.find('#jm-login-wrap');
		
		loginelements.hide();	
		
		var onlylogin = topbar.find('#form-login-submit');    	
		var loginbutton = onlylogin.find('.btn-primary');	
		var b_click = 0;
		
		// Set the effect type
	    var effect = 'slide';
	
	    // Set the options for the effect type chosen
	    //var optionsDir = { direction: 'right' };
	
	    // Set the duration (default: 400 milliseconds)
	    var duration = 400;
		
		loginbutton.click(function(e) {
		
			if(b_click == 0) {
				e.preventDefault();
				b_click = 1;
				loginelements.toggle(effect, optionsDir, duration);
				loginform.toggleClass('open');
			}    	
		});
		loginform.find('.jm-circle-link').click(function() {
			loginelements.toggle(effect, optionsDir, duration);
			loginform.toggleClass('open');
			if(b_click == 0) {
				b_click = 1;
			} else {
				b_click = 0;
			}   
		});    

    }
});