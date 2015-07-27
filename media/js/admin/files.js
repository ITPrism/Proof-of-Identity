jQuery(document).ready(function() {

    // Get the object of file list.
    var $fileList = jQuery("#filesList");

    $fileList.on("click", ".js-iproof-download-btn", function(event){
        event.preventDefault();

        jQuery("#js-iproof-download-id").val(jQuery(this).data("file-id"))

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

    // Set a "click" even for "Leave a Note" buttons.
    $fileList.on("click", ".js-iproof-note-btn", function(event){
        event.preventDefault();

        var fileId = jQuery(this).data("file-id");

        // Set file ID in the hidden field.
        jQuery("#js-iproof-note-file-id").val(fileId);

        jQuery.ajax({
            url: "index.php?option=com_identityproof&task=notification.getNotice&format=raw&id="+parseInt(fileId),
            type: "get",
            dataType: "text json"
        }).done(function (response) {

            if (response.success) {
                jQuery("#iproof_form_note").val(response.data.note);
                jQuery("#js-iproof-note-token").attr("name", response.data.token);
                jQuery('#js-iproof-modal').modal('show');
            } else {
                PrismUIHelper.displayMessageFailure(response.title, response.text);
            }

        });
    });

    // Hide the modal when click on the button "Cancel".
    jQuery('#js-iproof-btn-modal-cancel').on("click", function(event) {
        event.preventDefault();
        jQuery('#js-iproof-modal').modal('hide');
    });

    // Submit the form when click on the button "Submit".
    jQuery('#js-iproof-btn-modal-submit').on("click", function(event) {
        event.preventDefault();

        var fields = jQuery("#js-iproof-note-form").serialize();

        jQuery.ajax({
            url: "index.php?option=com_identityproof",
            type: "post",
            dataType: "text json",
            data: fields
        }).done(function (response) {

            jQuery('#js-iproof-modal').modal('hide');

            if (response.success) {
                PrismUIHelper.displayMessageSuccess(response.title, response.text);
            } else {
                PrismUIHelper.displayMessageFailure(response.title, response.text);
            }

        });

    });

});