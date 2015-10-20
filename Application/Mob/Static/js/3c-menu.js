$(document).ready(function(){
	$( ".menu_item" ).each( function( index ){
		$( this ).click( function(){
			
			var catTop,
				borderTop = $( this ).offset().top - 3,
				viewHeight = $( window ).height(),
				scrollTop = $( document ).scrollTop(),
				relaxHeight = viewHeight - ( borderTop - scrollTop );
			
			$( ".border" ).css( "top", borderTop ).show();
			$( ".cat-subcategory" ).show();
			$( ".shadow div" ).hide().eq( index ).show();
			
			var catHeight = $( ".cat-subcategory" ).height();
			if( catHeight <= relaxHeight ){
				catTop = borderTop;
			}else if( catHeight > relaxHeight && catHeight < viewHeight ){
				catTop = scrollTop + viewHeight - catHeight - 10;
			}else{
				catTop = scrollTop + 5;
			}
			$( ".cat-subcategory" ).css( "top", catTop );
			
			$( "span" ).show();
			$( this ).find( "span" ).hide();
		});

		$( ".mallCategory" ).click( function(){
			$( ".cat-subcategory" ).hide();
			$( ".border" ).hide();
			$( "span" ).show();
		});

	});
});