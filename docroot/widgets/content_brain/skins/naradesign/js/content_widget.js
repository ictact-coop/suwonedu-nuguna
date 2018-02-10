function content_widget_next(obj,list_per_page){
    var list = obj.find('>tbody>tr,>li');
	var visibles = list.filter(':visible');
	var offset  = list.index(visibles.eq(visibles.length-1))+1;
	
	if (offset+list_per_page > list.length) return false;

	list.hide().slice(offset, offset+list_per_page).show();
}

function content_widget_prev(obj,list_per_page){
    var list     = obj.find('>tbody>tr,>li');
	var visibles = list.filter(':visible');
	var end      = list.index(visibles.eq(0));
	
	if (end <= 0) return false;

	list.hide().slice(end - list_per_page, end).show();
}

function content_widget_tab_show(tab,list,i){
    tab.parents('ul.ncwTabContainer:first').children('li.active').removeClass('active');
    tab.parent('li').addClass('active');
}

jQuery(function(){
	jQuery('.ncwTabHr').each(function(){
		var p= jQuery(this);
		p.height(jQuery('.ncwListContainer',p).height() + 47);
	});
	jQuery('.ncwTabVr').each(function(){
		var p= jQuery(this);
		p.height(jQuery('.ncwListContainer',p).height() + 30);
	});
});