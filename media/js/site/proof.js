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
                });
            }
        }
    });

    var downloadManager = {

        modal:  {},
        modalBody:  {},
        modalBtnSubmit:  {},
        modalBtnClose:  {},

        init: function() {
            this.modal          = jQuery('#js-iproof-modal').remodal();
            this.modalBody      = jQuery('#js-iproof-modal-download-body');
            this.modalBtnSubmit = jQuery('#js-iproof-btn-modal-submit');
            this.modalBtnClose  = jQuery('#js-iproof-btn-modal-cancel');

            this.initModal();
            this.initFormSubmit();
            this.initButtonClose();
        },

        initModal: function() {

            var $this = this;

            // Set a "click" even for "Download" buttons.
            $fileList.on("click", ".js-ipfile-btn-download", function(event){
                event.preventDefault();

                $this.modal.open();

                var fileId = jQuery(this).data("file-id");

                jQuery.ajax({
                    url: "index.php?option=com_identityproof&view=download&format=raw&id="+parseInt(fileId),
                    type: "GET",
                    dataType: "html"
                }).done(function (response) {
                    if (response) {
                        $this.modalBody.html(response);
                    }
                });
            });
        },

        initButtonClose: function() {

            var $this = this;

            this.modalBtnClose.on("click", function() {
                $this.modal.close();
            });
        },

        initFormSubmit: function() {

            var $this = this;

            // Hide the modal when submit the form.
            jQuery(this.modalBody).on("submit", "#ipDownloadForm", function(){
                $this.modal.close();
            });

            // Submit the form when click on the button "Submit".
            this.modalBtnSubmit.on("click", function(event) {
                event.preventDefault();

                var form     = jQuery('#ipDownloadForm');
                var password = form.find("#jform_password").val();

                // If there is a password, submit the form.
                // Hide the model if there is no a password.
                if (password) {
                    form.submit();
                } else {
                    $this.modal.close();
                }
            });
        }
    };

    var noteManager = {

        modal:  {},
        modalBody:  {},
        modalBtnClose:  {},

        init: function() {
            this.modal          = jQuery('#js-iproof-modal-note').remodal();
            this.modalBody      = jQuery('#js-iproof-modal-body');
            this.modalBtnClose  = jQuery('#js-iproof-btn-note-close');

            this.initModal();
            this.initButtonClose();
        },

        initModal: function() {

            var $this = this;

            // Set a "click" even for "Notification" buttons.
            $fileList.on("click", ".js-iproof-btn-note", function(event){
                event.preventDefault();

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
                            $this.modalBody.text(response.data.note);
                            $this.modal.open();
                        }
                    });
                }
            });
        },

        initButtonClose: function() {

            var $this = this;

            this.modalBtnClose.on("click", function(event) {
                event.preventDefault();
                $this.modal.close();
            });
        }
    };

    downloadManager.init();
    noteManager.init();
});