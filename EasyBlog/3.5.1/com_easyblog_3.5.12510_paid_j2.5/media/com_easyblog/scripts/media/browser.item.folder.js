EasyBlog.module("media/browser.item.folder",function(e){var t=this;EasyBlog.require().view("media/browser.item.folder").done(function(){EasyBlog.Controller("Media.Browser.Item.Folder",{defaultOptions:{view:{body:"media/browser.item.folder"},"{folderItemGroup}":".folderItemGroup","{folderItem}":".browserItem","{instructions}":".instructions","{uploadButton}":".uploadButton"}},function(t){return{init:function(){t.element.empty(),t.view.body({item:t.item}).appendTo(t.element),t.place.acl().canUploadItem&&t.instructions().addClass("canUpload")},setLayout:function(){},"{uploadButton} click":function(e,n){n.stopPropagation(),t.browser.focusItem(t.item);var r=t.browser.uploader;r&&(r.showPopup(),r.showUploadForm())},folders:[],items:[],addItem:function(e){t.items.push(e),e.element.appendTo(t.folderItemGroup()),t.element.removeClass("empty");var n=t.item.panel;if(!n)return;if(!n.handler)return;n.handler.populate()},removeItem:function(e){t.folderItem().length<1&&t.element.addClass("empty")},populateFolderContents:function(){var n=[],r=!0;e.each(t.item.meta.contents,function(e,i){if(i.type=="folder")var s=t.browser.createFolder(i,t.item);else{var o=t.browser.createItem(i,t.item);o.handlerInitialization.done(function(){o.initialization.resolve()}),n.push(o.initialization),r=!1}}),r&&t.element.addClass("empty"),e.when.apply(null,n).done(function(){t.item.initialization.resolve()})}}}),t.resolve()})});