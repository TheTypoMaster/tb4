$(function() {

    $('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
$(function() {
    $(window).bind("load resize", function() {
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })
})

$(function() {
	$('.alert-dismissable').fadeOut(5000);
});

//Sets up icon selection
$(function() {

    function iconSelectFormat( state ) {
        if(!state.id) {
            return state.text;
        }

        return $('<span><img src="' + $(state.element).data('icon-url') + '" style="height:25px;width:25px;"/> ' + state.text + '</span>');
    }

    $(".icon-select").select2({
        templateResult: iconSelectFormat,
        templateSelection: iconSelectFormat
    });
});
