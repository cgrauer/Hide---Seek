
	autonavTabSetup = function() {
		$('ul#ccm-autonav-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-autonav-tab-','');
				autonavShowPane(pane);
			}
		});		
	}
	
	autonavShowPane = function (pane){
		$('ul#ccm-autonav-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-autonav-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-autonavPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-autonavPane-'+pane).css('display','block');
		if(pane=='preview') reloadPreview(document.blockForm);
	}
	
	$(function() {	
		autonavTabSetup();		
	});

