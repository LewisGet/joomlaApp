EasyBlog.module("dashboard/media.mini.uploader",function(e){var t=this;EasyBlog.require().library("plupload").done(function(){EasyBlog.Controller("Dashboard.Media.Mini.Uploader",{defaultOptions:{"{uploader}":".uploader","{uploadButton}":".uploadButton","{uploadErrorFiles}":".uploadErrorFiles","{hideUploadMessage}":".hideUploadMessage"}},function(t){return{init:function(){t.uploadButton().show(),t.uploader().implement("plupload",{settings:t.options.settings,"{uploadButton}":t.options["{uploadButton}"]},function(){t.plupload=this.plupload})},setLayout:function(){t.plupload.refresh()},trimFilename:function(e){return e.length>64&&(e=e.slice(0,64)),e},"{uploader} FilesAdded":function(e,n,r,i){t.started=!0,t.plupload.start(),t.element.addClass("uploading")},"{uploader} UploadFile":function(e,n,r,i){var s=i.name;t.browser.setTitle("Uploading "+t.trimFilename(i.name)+"...")},"{uploader} UploadProgress":function(n,r,i,s){t.browser.setTitle("Uploading "+t.trimFilename(s.name)+(s.percent!==undefined?" "+s.percent+"%":"")+(s.loaded!==undefined&&!s.size!==undefined?s.size-s.loaded?" ("+e.plupload.formatSize(s.size-s.loaded)+" left)":"":""))},"{uploader} FileUploaded":function(n,r,i,s,o){if(e.isPlainObject(o)&&e.isPlainObject(o.item)){var u=o.item;o.item.place=t.options.settings.place,EasyBlog.dashboard.media.addItem(u);return}t.errors[s.id]=s},"{uploader} UploadComplete":function(){t.started=!1,t.browser.setTitle(""),t.element.removeClass("uploading"),t.showUploadErrors()},errors:{},showUploadErrors:function(){if(e.isEmptyObject(t.errors))return;var n=t.uploadErrorFiles().empty();e.each(t.errors,function(t,r){e("<li>").html(r.name).appendTo(n)}),t.browser.showMessage("uploadError",!0),t.errors={}},"{hideUploadMessage} click":function(){t.browser.hideMessage()},"{uploader} FileError":function(e,n,r,i,s){t.errors[i.id]=i},"{uploader} Error":function(e,n,r,i){i.file&&(t.errors[i.file.id]=i.file,t.started||setTimeout(function(){t.started||t.showUploadErrors()},300))}}}),t.resolve()})});