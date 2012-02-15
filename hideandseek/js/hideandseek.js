

	$(document).ready( function() {
		
		$('.hs-opener').show();
		$('.hs-content').hide();
		$('.hs-closer').hide();

		$('.hs-hide').hide();
		$('.hs-show').show();
		
		$('.hs-opener').live( 'click', function() {
			$('.hs-opener-auto').fadeIn('fast');
			$('.hs-content-auto').slideUp('fast');
			$('.hs-closer-auto').hide();
			var id = this.getAttribute('id').substr(10);
			$(this).hide();
			$('#hs-teaser-open-'+id).hide();
			$('#hs-content-'+id).slideDown('fast');
			$('#hs-teaser-close-'+id).fadeIn('fast');
			$('#hs-closer-'+id).fadeIn('fast');
			eval( "initGoogleMap"+id+"();");
		})
				
		$('.hs-closer').live( 'click', function() {
			var id = this.getAttribute('id').substr(10);
			$('#hs-opener-'+id).fadeIn('fast');
			$('#hs-content-'+id).slideUp('fast');
			$(this).hide();
			$('#hs-teaser-open-'+id).fadeIn('fast');
			$('#hs-teaser-close-'+id).hide();
		})
	})
 
