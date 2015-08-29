
/**
 * @version $Id: album.js 21 2013-11-06 08:14:17Z szymon $
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools galleryGrid layout
 * @copyright Copyright (C) 2012 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

function startUpload(up,files) {
	
	//up.settings.buttons.start = false;
	up.start();
	//console.log(up);
}

function injectUploaded(up,file,info,site_url) {
	
	var response = JSON.decode(info.response); 
	if(response.error) {
		//console.log(file.status);
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		//up.removeFile(file);
		return false;
	}
	
	var html = '<img src="'+site_url+'/tmp/djupload/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+stripExt(file.name)+'">';
	html += '	<span class="delBtn"></span></div>';
	var item = new Element('div',{'class':'itemImage', html: html});
	initItemEvents(item);
	// add uploaded image to the list and make it sortable
	item.inject(document.id('itemImages'), 'bottom');
	this.album.addItems(item);
	
	return true;
}

function injectFrontUploaded(up,file,info,site_url) {
	
	var response = JSON.decode(info.response); 
	if(response.error) {
		//console.log(file.status);
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		//up.removeFile(file);
		return false;
	}
	
	var html = '<img src="'+site_url+'/tmp/djupload/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+stripExt(file.name)+'">';
	html += '	<span class="delBtn"></span></div>';
	var item = new Element('div',{'class':'itemImage', html: html});
	initItemEvents(item);
	// add uploaded image to the list and make it sortable
	item.inject(document.id('itemImages'), 'bottom');
	this.album.addItems(item);
	up.removeFile(file);
	
	return true;
}

function initItemEvents(item) {
	
	if(!item) return;
	item.getElement('.delBtn').addEvent('click',function(){
		item.set('tween',{duration:'short',transition:'expo:out'});
		item.tween('width',0);
		(function(){item.dispose();}).delay(250);
		this.deleted = item;
	});
	item.getElements('input').each(function(input){
		input.addEvent('focus',function(){
			item.addClass('active');
		});
		input.addEvent('blur',function(){
			item.removeClass('active');
		});
	});
}

function stripExt(filename) {
	
	var pattern = /\.[^.]+$/;
	return filename.replace(pattern, "");	
}

window.addEvent('domready', function(){

	this.album = new Sortables('itemImages',{
		clone: true,
		revert: {duration:'short',transition:'expo:out'},
		opacity: 0.3
	});
	
	$$('.itemImage').each(function(item){
		initItemEvents(item);
	});
});