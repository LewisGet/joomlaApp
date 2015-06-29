// module: start
EasyBlog.module("media/browser.uploader", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
    "plupload",
    "ui/position"
)
.script(
    "media/browser.uploader.item"
)
.view(
    "media/browser.uploader",
    "media/browser.uploader.item"
)
.language(
    'COM_EASYBLOG_MM_UPLOADING',
    'COM_EASYBLOG_MM_UPLOADING_STATE',
    'COM_EASYBLOG_MM_UPLOADING_PENDING',
    'COM_EASYBLOG_MM_UPLOAD_COMPLETE',
    'COM_EASYBLOG_MM_UPLOAD_PREPARING',
    'COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE',
    'COM_EASYBLOG_MM_UPLOADING_LEFT'
)
.done(function(){

// controller: start
EasyBlog.Controller("Media.Browser.Uploader",

	{
		defaultOptions: {

            view: {
                uploader: "media/browser.uploader",
                uploadItem: "media/browser.uploader.item"
            },

            extensions: {
                photos: ["jpg","png","gif"],
                media: ["3g2", "3gp", "aac", "f4a", "f4v", "flv", "m4a", "m4v", "mov", "mp3", "mp4"],
                files: ["zip", "rar", "7z", "pdf", "doc", "docx", "ppt", "pptx", "xls", "xlsx"]
            },

            "{uploadToggle}": ".browserUploadButton",
            "{uploadButton}" : ".browserUploadButton",

            "{popup}"   : ".browserUploader",
            "{uploader}": ".browserUploader",

            "{uploadForm}": ".uploadForm",
            "{uploadPath}": ".uploadPath",
            "{uploadSize}": ".uploadSize",
            "{uploadTypes}": ".uploadTypes",
            "{uploadType}": ".uploadType",

            "{uploadFormButton}": ".uploadFormButton",
            "{uploadHistoryButton}": ".uploadHistoryButton",

            "{uploadForm}": ".uploadForm",
            "{uploadHistory}": ".uploadHistory",

            "{uploadItemGroup}": ".uploadItemGroup",
            "{uploadItem}": ".uploadItem",
            "{emptyUploadItem}" : ".uploadItem .emptyItem"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

            self.setTemplate();

            self.uploader()
                .implement(
                    "plupload",
                    {
                        settings: self.options.settings,
                        "{uploadButton}" : self.options["{uploadTypes}"]
                    },
                    function() {

                        self.plupload = this.plupload;

                        if (self.plupload.runtime=="html4" || $.browser.msie) {

                            self.uploadItemGroup().addClass("indefinite-progress");
                        }
                    }
                );

            self.setLayout();

            self.uploadFormButton()
                .click();
		},

        setTemplate: function() {

            self.uploadToggle().show();

            // Create upload dialog
            self.view.uploader()
                .appendTo(self.element);

            self.uploadSize()
                .html(self.options.settings.max_file_size);


            var allowedExtensions = (function(){

                var extensions = [];

                $.each(self.options.settings.filters, function(i, filter) {

                    extensions = extensions.concat($.trimSeparators(filter.extensions,",",true).split(","));
                });

                return extensions;
            })();

            var extensionMap = {photos: [], media: [], files: []};

            $.each(allowedExtensions, function(i, allowedExtension) {

                var match = false;

                $.each(self.options.extensions, function(type, extensions) {

                    if ($.inArray(allowedExtension, extensions) > -1) {

                        extensionMap[type].push(allowedExtension);

                        match = true;
                    };

                });

                if (!match) {

                    extensionMap["files"].push(allowedExtension);
                }
            });

            $.each(extensionMap, function(type, extensions) {

                var uploadType = self.uploadType(".upload-type-" + type);

                if (extensions.length < 1) {

                    uploadType.remove();

                } else {

                    uploadType.find(".extensionList").html(extensions.join(", "));
                }
            });
        },

        "{self} setLayout": function() {

            self.setLayout();
        },

        "{self} folderActivate": function(el, event, browser, folder) {

            self.setUploadPath(folder);
        },

        setLayout: function() {

            self.popup()
                .position({
                    my: "center top",
                    at: "center bottom",
                    of: self.uploadToggle(),
                    offset: "0px 18px"
                });

            if (self.plupload) {
                self.plupload.refresh();
            }
        },

        setUploadPath: function(folder) {

            var DS = self.browser.options.directorySeparator,

                uploadPath = DS,

                folder = folder || self.browser.currentFolder();

            if (folder!==undefined) {

                uploadPath = folder.meta.path;
            }

            self.uploadPath()
                .html(

                    // Replace the first slash with base folder name
                    uploadPath.replace(

                        new RegExp( (DS=="\\") ? "\\\\" : DS ),

                        (uploadPath==DS) ? self.browser.baseFolder : self.browser.baseFolder + DS)
                );
        },

        showPopup: function() {

            self.uploadToggle()
                .addClass("active");

            var popup = self.popup();

            // Make popup invisible
            popup
                .css({opacity: 0})
                .addClass("active");

            // Reposition popup
            self.setLayout();

            self.setUploadPath();

            var finalTop = popup.css("top");

            popup
                .css({
                    top: "-=" + popup.outerHeight(true) + "px",
                    opacity: 1
                })
                .animate(
                {
                    top: finalTop
                },
                {
                    easing: "easeInCubic"
                });
        },

        hidePopup: function() {

            self.uploadToggle()
                .removeClass("active");

            var popup = self.popup();

            popup
                .animate(
                {
                    top: "-=" + popup.outerHeight(true) + "px"
                },
                {
                    easing: "easeInCubic",
                    complete: function() {

                        popup.css({
                            opacity: 0
                        })
                        .removeClass("active");
                    }
                });
        },

        "{uploadToggle} click": function(el) {

            if (el.hasClass("active")) {

                self.hidePopup();

            } else {

                self.showPopup();
            }
        },

        showUploadForm: function() {

            self.uploadHistory()
                .hide();

            self.uploadHistoryButton()
                .removeClass("active");

            self.uploadForm()
                .show();

            self.uploadFormButton()
                .addClass("active");

            self.setLayout();
        },

        "{uploadFormButton} click": function(el) {

            self.showUploadForm();
        },

        "{uploadHistoryButton} click": function(el) {

            self.uploadForm()
                .hide();

            self.uploadFormButton()
                .removeClass("active");

            self.uploadHistory()
                .show();

            self.uploadHistoryButton()
                .addClass("active");

            self.setLayout();
        },

        getBrowserItem: function(file) {

            return self.browser.items[file.id];
        },

        items: {},

        addUploadItem: function(file, parentFolder) {

        	self.uploadItemGroup().find( '.emptyItem' ).remove();

            var item = self.items[file.id] =
                new EasyBlog.Controller.Media.Browser.Uploader.Item(
                    self.view.uploadItem()
                        .prependTo(self.uploadItemGroup()),
                    {
                        controller: {
                            id: file.id,
                            file: file,
                            parentFolder: parentFolder
                        }
                    }
                );

            item.setMessage( $.language( "COM_EASYBLOG_MM_UPLOADING_PENDING" ) + item.getFilesize(" (", ")") + ".");

            return item;
        },

        "{uploadItem} click": function(el) {

            var item = el.data("item");

            self.uploadItem().removeClass("active");

            el.addClass("active");

            if (!item.browserItem) return;

            if (item.browserItem._destroyed) return;

            self.browser.activateItem(item.browserItem);

            self.browser.scrollTo(item.browserItem);
        },

        "{uploadItem} dblclick": function(el, event) {

            if (event.shiftKey) {
                try { console.log(el.data("item")); } catch(e) {}
            }
        },

        "{uploader} BeforeUpload": function(el, event, uploader, file) {

            var item = self.items[file.id];

            item.file = file;

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_PREPARING' ) );

            var uploadUrl = self.options.settings.url,
                placeName = self.place.id;

            uploader.settings.url = uploadUrl + "&place=" + placeName + "&path=" + encodeURIComponent(item.parentFolder.meta.path);
        },

        "{uploader} FilesAdded": function(el, event, uploader, files) {

            // Show upload history tab
            self.uploadHistoryButton()
                .click();

            files.reverse();

            var parentFolder = self.browser.currentFolder();

            $.each(files, function(i, file) {

                // The item may have been created before, e.g.
                // when plupload error event gets triggered first.
                if (self.items[file.id]!==undefined) return;

                self.addUploadItem(file, parentFolder);
            });

            self.plupload.start();
        },

        "{uploader} UploadFile": function(el, event, uploader, file) {

            var item = self.items[file.id];

            item.file = file;

            item.setState("uploading");

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOADING_STATE' ) );
        },

        "{uploader} UploadProgress": function(el, event, uploader, file) {

            var item = self.items[file.id];

            item.file = file;

            item.setProgress(file.percent);

            item.setMessage(
                $.language( 'COM_EASYBLOG_MM_UPLOADING' )  +
                ((file.percent!==undefined) ? " " + file.percent + "%" : "") +
                ((file.loaded!==undefined && !file.size!==undefined) ?
                    ((file.size - file.loaded) ?
                        " (" + $.plupload.formatSize(file.size - file.loaded) + " " + $.language( 'COM_EASYBLOG_MM_UPLOADING_LEFT' ) + ")" : ""
                    ) : ""
                )
            );
        },

        "{uploader} FileUploaded": function(el, event, uploader, file, response) {

            var item = self.items[file.id];

            item.file = file;

            item.response = response;

            if ($.isPlainObject(response)) {

                if ($.isPlainObject(response.item)) {

                    item.setState("done");
                    item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_COMPLETE' ) );

                    var meta = response.item;

                    // Hack: Add place property to each meta
                    meta.place = self.place.id;

                    item.browserItem = self.browser.createItem(meta, item.parentFolder);

                    item.browserItem.setLayout();

                    self.media.setLayout();

                    // Also add item to other manager instances
                    var dashboard = self.media.dashboard;

                    if (dashboard) {
                        dashboard.media.addItem(meta);
                    }

                } else {

                    item.setState("failed");
                    item.setMessage(response.message || $.language( 'COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE' ) );
                };

            } else {

                item.setState("failed");
                item.setMessage( $.language( 'COM_EASYBLOG_MM_SERVER_RETURNED_INVALID_RESPONSE' ) );
            }
        },

        "{uploader} FileError": function(el, event, uploader, file, response) {

            var item = self.items[file.id];

            item.file = file;

            item.response = response;

            item.setState("failed");

            item.setMessage(response.message);
        },

        "{uploader} Error": function(el, event, uploader, error) {

            // File based error
            if (error.file) {

                var file = error.file;

                var item = self.items[file.id];

                if (item===undefined) {

                    item = self.addUploadItem(file);

                    self.uploadHistoryButton()
                        .click();
                }

                item.setState("failed");

                item.setMessage(error.message);
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
