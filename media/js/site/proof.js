jQuery(document).ready(function() {
    "use strict";

    // Style file input
    jQuery('#jform_file').fileinput({
        showPreview: false,
        showUpload: false,
        browseClass: "btn btn-default",
        browseLabel: Joomla.JText._('COM_IDENTITYPROOF_BROWSE'),
        removeClass: "btn btn-danger",
        removeLabel: Joomla.JText._('COM_IDENTITYPROOF_REMOVE'),
        layoutTemplates: {
            main1:
            "<div class=\'input-group {class}\'>\n" +
            "   <div class=\'input-group-btn\'>\n" +
            "       {browse}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>"
        }
    });

    // Get the file list jQuery element.
    var $fileList = jQuery("#js-ipfile-list");

    // Set a "click" events for "Remove" buttons.
    $fileList.on("click", ".js-ipfile-btn-remove", function (event) {
        event.preventDefault();

        if (confirm(Joomla.JText._('COM_IDENTITYPROOF_DELETE_QUESTION'))) {

            var fileId = parseInt(jQuery(this).data("file-id"));

            if (fileId > 0) {
                var fields = {
                    id: fileId,
                    format: "raw",
                    task: "file.remove"
                };

                jQuery.ajax({
                    url: "index.php?option=com_identityproof",
                    type: "post",
                    data: fields,
                    dataType: "text json"
                }).done(function (response) {

                        if (response.success) {
                            jQuery("#js-ipfile" + response.data.file_id).remove();
                            PrismUIHelper.displayMessageSuccess(response.title, response.text);
                        } else {
                            PrismUIHelper.displayMessageFailure(response.title, response.text);
                        }

                    }
                );
            }
        }
    });

    // Set a "click" even for "Download" buttons.
    $fileList.on("click", ".js-ipfile-btn-download", function(event){
        event.preventDefault();

        jQuery('#js-iproof-modal').modal('show');

        var fileId = jQuery(this).data("file-id");

        jQuery.ajax({
            url: "index.php?option=com_identityproof&view=download&format=raw&id="+parseInt(fileId),
            type: "GET",
            dataType: "html"
        }).done(function (response) {

                if (response) {
                    jQuery("#js-iproof-modal").find(".modal-body").html(response);
                }

            }
        );
    });

    // Hide the modal when click on the button "Cancel".
    jQuery('#js-iproof-btn-modal-cancel').on("click", function(event) {
        event.preventDefault();
        jQuery('#js-iproof-modal').modal('hide');
    });

    // Submit the form when click on the button "Submit".
    jQuery('#js-iproof-btn-modal-submit').on("click", function(event) {
        event.preventDefault();

        var password = jQuery("#jform_password").val();

        // If there is a password, submit the form.
        // Hide the model if there is no a password.
        if (password) {
            jQuery('#ipDownloadForm').submit();
        } else {
            jQuery('#js-iproof-modal').modal('hide');
        }

    });

    // Hide the modal when submit the form.
    jQuery('#js-iproof-modal').on("submit", "#ipDownloadForm", function(){
        jQuery('#js-iproof-modal').modal('hide');
    });

    // Set a "click" even for "Notification" buttons.
    $fileList.on("click", ".js-iproof-btn-note", function(event){
        event.preventDefault();

        jQuery('#js-iproof-modal-note').modal('show');

        var fileId = jQuery(this).data("file-id");

        if (fileId > 0) {
            jQuery.ajax({
                url: "index.php?option=com_identityproof&task=file.note&format=raw&id=" + parseInt(fileId),
                type: "get",
                dataType: "text json"
            }).done(function (response) {

                    if (!response.success) {
                        PrismUIHelper.displayMessageFailure(response.title, response.text);
                    } else {
                        jQuery("#js-iproof-modal-note").find(".modal-body").text(response.data.note);
                    }

                }
            );
        }
    });

    // Hide the modal when click on the button "Close".
    jQuery('#js-iproof-btn-modal-note-close').on("click", function(event) {
        event.preventDefault();
        jQuery('#js-iproof-modal-note').modal('hide');
    });
});