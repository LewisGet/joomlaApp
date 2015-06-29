// module: start
EasyBlog.module("dashboard/media.mini.uploader", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
    "plupload"
)
.done(function(){

// controller: start
EasyBlog.Controller("Dashboard.Media.Mini.Uploader",

	{
		defaultOptions: {
            "{uploader}": ".uploader",
			"{uploadButton}": ".uploadButton",
            "{uploadErrorFiles}": ".uploadErrorFiles",
            "{hideUploadMessage}": ".hideUploadMessage"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

            self.uploadButton().show();

            self.uploader()
                .implement(
                    "plupload",
                    {
                        settings: self.options.settings,
                        "{uploadButton}" : self.options["{uploadButton}"]
                    },
                    function() {

                        self.plupload = this.plupload;
                    }
                );
		},

        setLayout: function() {

            self.plupload.refresh();
        },

        trimFilename: function(filename) {

            if (filename.length > 64) {
                filename = filename.slice(0, 64);
            }

            return filename;
        },

        "{uploader} FilesAdded": function(el, event, uploader, files) {

            self.started = true;

            self.plupload.start();

            self.element.addClass("uploading");
        },

        "{uploader} UploadFile": function(el, event, uploader, file) {

            var filename = file.name;

            self.browser.setTitle("Uploading " + self.trimFilename(file.name) + "...");
        },

        "{uploader} UploadProgress": function(el, event, uploader, file) {

            self.browser.setTitle(
                "Uploading " + self.trimFilename(file.name) +
                ((file.percent!==undefined) ? " " + file.percent + "%" : "") +
                ((file.loaded!==undefined && !file.size!==undefined) ?
                    ((file.size - file.loaded) ?
                        " (" + $.plupload.formatSize(file.size - file.loaded) + " left)" : ""
                    ) : ""
                )
            );
        },

        "{uploader} FileUploaded": function(el, event, uploader, file, response) {

            if ($.isPlainObject(response)) {

                if ($.isPlainObject(response.item)) {

                    // Hack: Add place property to each meta
                    var meta = response.item;
                    response.item.place = self.options.settings.place;

                    EasyBlog.dashboard.media.addItem(meta);

                    return;
                }
            }

            // Add to error list
            self.errors[file.id] = file;
        },

        "{uploader} UploadComplete": function() {

            self.started = false;

            self.browser.setTitle("");

            self.element.removeClass("uploading");

            self.showUploadErrors();
        },

        errors: {},

        showUploadErrors: function() {

            if ($.isEmptyObject(self.errors)) return;

            var uploadErrorFiles = self.uploadErrorFiles().empty();

            $.each(self.errors, function(id, file) {

                $("<li>")
                    .html(file.name)
                    .appendTo(uploadErrorFiles);
            });

            self.browser.showMessage("uploadError", true);

            self.errors = {};
        },

        "{hideUploadMessage} click": function() {

            self.browser.hideMessage();
        },

        "{uploader} FileError": function(el, event, uploader, file, response) {

            self.errors[file.id] = file;
        },

        "{uploader} Error": function(el, event, uploader, error) {

            // File based error
            if (error.file) {

                self.errors[error.file.id] = error.file;

                // When selecting one file which results in the triggering
                // of this error event, show upload message also.
                if (!self.started) {

                    setTimeout(function(){

                        if (!self.started) {

                            self.showUploadErrors();
                        }

                    }, 300);
                }
            }

        }
	}}

);

// controller: end

module.resolve();

});
// require: end

});
// module: end
