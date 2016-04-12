jQuery(document).ready(function() {

    jQuery('#adminForm').on('click', '.js-socialprofiles', function(event){
        event.preventDefault();

        var fields = {
            id: jQuery(this).data('id'),
            view: 'socialprofiles',
            format: 'raw'
        };

        jQuery.ajax({
            url: "index.php?option=com_identityproof",
            method: "GET",
            data: fields,
            dataType: "html",
            cache: false
        })
        .done(function(html) {
            jQuery("#js-socialprofiles-body").html(html);

            jQuery('#js-modal-socialprofiles').modal('show');
        });

    });

});