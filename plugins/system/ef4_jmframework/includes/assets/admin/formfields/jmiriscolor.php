<?php
	            		jQuery(document).on('JMFrameworkInit', function(){
								jQuery(this).iris({
									hide: true,
	    							palettes: true
								});
							});
		            		jQuery(document).on('click',function(event){
								jQuery('.jmirispicker').each(function() {
									if (event.target != this && typeof jQuery(this).iris != 'undefined') {
										jQuery(this).iris('hide');
									} else {
								});
							});
            			});
	            ");
            	$document->addScriptDeclaration("
            		jQuery(document).ready(function(){
						jQuery(document).trigger('JMFrameworkInit');
						});
            		");
            }
        // Initialize JavaScript field attributes.
        $html = array();