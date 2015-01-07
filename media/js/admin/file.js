jQuery(document).ready(function() {
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'file.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };

    jQuery("#js-iproof-btn-download").on("click", function(event){
        event.preventDefault();

        var fields = {
            task: "file.getFormToken",
            format: "raw"
        };

        jQuery.ajax({
            url: "index.php?option=com_identityproof",
            type: "get",
            data: fields,
            dataType: "text json"
        }).done(function (response) {

            if (response.success) {
                jQuery("#js-iproof-download-token").attr("name", response.data.token);
                jQuery("#js-iproof-download-form").submit();
            }

        });

    });
});