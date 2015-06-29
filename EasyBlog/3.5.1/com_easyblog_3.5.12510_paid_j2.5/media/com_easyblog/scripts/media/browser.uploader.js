EasyBlog.module("media/browser.uploader",function(e){var t=this;EasyBlog.require().library("plupload","ui/position").script("media/browser.uploader.item").view("media/browser.uploader","media/browser.uploader.item").language("COM_EASYBLOG_MM_UPLOADING","COM_EASYBLOG_MM_UPLOADING_STATE","COM_EASYBLOG_MM_UPLOADING_PENDING","COM_EASYBLOG_MM_UPLOAD_COMPLETE","COM_EASYBLOG_MM_UPLOAD_PREPARING","COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE","COM_EASYBLOG_MM_UPLOADING_LEFT").done(function(){EasyBlog.Controller("Media.Browser.Uploader",{defaultOptions:{view:{uploader:"media/browser.uploader",uploadItem:"media/browser.uploader.item"},extensions:{photos:["jpg","png","gif"],media:["3g2","3gp","aac","f4a","f4v","flv","m4a","m4v","mov","mp3","mp4"],files:["zip","rar","7z","pdf","doc","docx","ppt","pptx","xls","xlsx"]},"{uploadToggle}":".browserUploadButton","{uploadButton}":".browserUploadButton","{popup}":".browserUploader","{uploader}":".browserUploader","{uploadForm}":".uploadForm","{uploadPath}":".uploadPath","{uploadSize}":".uploadSize","{uploadTypes}":".uploadTypes","{uploadType}":".uploadType","{uploadFormButton}":".uploadFormButton","{uploadHistoryButton}":".uploadHistoryButton","{uploadForm}":".uploadForm","{uploadHistory}":".uploadHistory","{uploadItemGroup}":".uploadItemGroup","{uploadItem}":".uploadItem","{emptyUploadItem}":".uploadItem .emptyItem"}},function(t){return{init:function(){t.setTemplate(),t.uploader().implement("plupload",{settings:t.options.settings,"{uploadButton}":t.options["{uploadTypes}"]},function(){t.plupload=this.plupload,(t.plupload.runtime=="html4"||e.browser.msie)&&t.uploadItemGroup().addClass("indefinite-progress")}),t.setLayout(),t.uploadFormButton().click()},setTemplate:function(){t.uploadToggle().show(),t.view.uploader().appendTo(t.element),t.uploadSize().html(t.options.settings.max_file_size);var n=function(){var n=[];return e.each(t.options.settings.filters,function(t,r){n=n.concat(e.trimSeparators(r.extensions,",",!0).split(","))}),n}(),r={photos:[],media:[],files:[]};e.each(n,function(n,i){var s=!1;e.each(t.options.extensions,function(t,n){e.inArray(i,n)>-1&&(r[t].push(i),s=!0)}),s||r.files.push(i)}),e.each(r,function(e,n){var r=t.uploadType(".upload-type-"+e);n.length<1?r.remove():r.find(".extensionList").html(n.join(", "))})},"{self} setLayout":function(){t.setLayout()},"{self} folderActivate":function(e,n,r,i){t.setUploadPath(i)},setLayout:function(){t.popup().position({my:"center top",at:"center bottom",of:t.uploadToggle(),offset:"0px 18px"}),t.plupload&&t.plupload.refresh()},setUploadPath:function(e){var n=t.browser.options.directorySeparator,r=n,e=e||t.browser.currentFolder();e!==undefined&&(r=e.meta.path),t.uploadPath().html(r.replace(RegExp(n=="\\"?"\\\\":n),r==n?t.browser.baseFolder:t.browser.baseFolder+n))},showPopup:function(){t.uploadToggle().addClass("active");var e=t.popup();e.css({opacity:0}).addClass("active"),t.setLayout(),t.setUploadPath();var n=e.css("top");e.css({top:"-="+e.outerHeight(!0)+"px",opacity:1}).animate({top:n},{easing:"easeInCubic"})},hidePopup:function(){t.uploadToggle().removeClass("active");var e=t.popup();e.animate({top:"-="+e.outerHeight(!0)+"px"},{easing:"easeInCubic",complete:function(){e.css({opacity:0}).removeClass("active")}})},"{uploadToggle} click":function(e){e.hasClass("active")?t.hidePopup():t.showPopup()},showUploadForm:function(){t.uploadHistory().hide(),t.uploadHistoryButton().removeClass("active"),t.uploadForm().show(),t.uploadFormButton().addClass("active"),t.setLayout()},"{uploadFormButton} click":function(e){t.showUploadForm()},"{uploadHistoryButton} click":function(e){t.uploadForm().hide(),t.uploadFormButton().removeClass("active"),t.uploadHistory().show(),t.uploadHistoryButton().addClass("active"),t.setLayout()},getBrowserItem:function(e){return t.browser.items[e.id]},items:{},addUploadItem:function(n,r){t.uploadItemGroup().find(".emptyItem").remove();var i=t.items[n.id]=new EasyBlog.Controller.Media.Browser.Uploader.Item(t.view.uploadItem().prependTo(t.uploadItemGroup()),{controller:{id:n.id,file:n,parentFolder:r}});return i.setMessage(e.language("COM_EASYBLOG_MM_UPLOADING_PENDING")+i.getFilesize(" (",")")+"."),i},"{uploadItem} click":function(e){var n=e.data("item");t.uploadItem().removeClass("active"),e.addClass("active");if(!n.browserItem)return;if(n.browserItem._destroyed)return;t.browser.activateItem(n.browserItem),t.browser.scrollTo(n.browserItem)},"{uploadItem} dblclick":function(e,t){if(t.shiftKey)try{console.log(e.data("item"))}catch(n){}},"{uploader} BeforeUpload":function(n,r,i,s){var o=t.items[s.id];o.file=s,o.setMessage(e.language("COM_EASYBLOG_MM_UPLOAD_PREPARING"));var u=t.options.settings.url,a=t.place.id;i.settings.url=u+"&place="+a+"&path="+encodeURIComponent(o.parentFolder.meta.path)},"{uploader} FilesAdded":function(n,r,i,s){t.uploadHistoryButton().click(),s.reverse();var o=t.browser.currentFolder();e.each(s,function(e,n){if(t.items[n.id]!==undefined)return;t.addUploadItem(n,o)}),t.plupload.start()},"{uploader} UploadFile":function(n,r,i,s){var o=t.items[s.id];o.file=s,o.setState("uploading"),o.setMessage(e.language("COM_EASYBLOG_MM_UPLOADING_STATE"))},"{uploader} UploadProgress":function(n,r,i,s){var o=t.items[s.id];o.file=s,o.setProgress(s.percent),o.setMessage(e.language("COM_EASYBLOG_MM_UPLOADING")+(s.percent!==undefined?" "+s.percent+"%":"")+(s.loaded!==undefined&&!s.size!==undefined?s.size-s.loaded?" ("+e.plupload.formatSize(s.size-s.loaded)+" "+e.language("COM_EASYBLOG_MM_UPLOADING_LEFT")+")":"":""))},"{uploader} FileUploaded":function(n,r,i,s,o){var u=t.items[s.id];u.file=s,u.response=o;if(e.isPlainObject(o))if(e.isPlainObject(o.item)){u.setState("done"),u.setMessage(e.language("COM_EASYBLOG_MM_UPLOAD_COMPLETE"));var a=o.item;a.place=t.place.id,u.browserItem=t.browser.createItem(a,u.parentFolder),u.browserItem.setLayout(),t.media.setLayout();var f=t.media.dashboard;f&&f.media.addItem(a)}else u.setState("failed"),u.setMessage(o.message||e.language("COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE"));else u.setState("failed"),u.setMessage(e.language("COM_EASYBLOG_MM_SERVER_RETURNED_INVALID_RESPONSE"))},"{uploader} FileError":function(e,n,r,i,s){var o=t.items[i.id];o.file=i,o.response=s,o.setState("failed"),o.setMessage(s.message)},"{uploader} Error":function(e,n,r,i){if(i.file){var s=i.file,o=t.items[s.id];o===undefined&&(o=t.addUploadItem(s),t.uploadHistoryButton().click()),o.setState("failed"),o.setMessage(i.message)}}}}),t.resolve()})});