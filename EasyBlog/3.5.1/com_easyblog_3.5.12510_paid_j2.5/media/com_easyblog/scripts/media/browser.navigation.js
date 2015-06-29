EasyBlog.module("media/browser.navigation",function(e){var t=this;EasyBlog.require().view("media/browser.navigation.item","media/browser.navigation.itemgroup","media/browser.navigation.item.create").done(function(){EasyBlog.Controller("Media.Browser.Navigation",{defaultOptions:{view:{item:"media/browser.navigation.item",itemGroup:"media/browser.navigation.itemgroup",itemCreate:"media/browser.navigation.item.create"},path:"","{itemGroup}":".navigationItemGroup","{item}":".navigationItem","{createFolderItem}":".navigationItem.createFolder","{createFolderForm}":".createFolderForm","{createFolderInput}":".createFolderInput","{createFolderButton}":".createFolderButton","{createFolderToggle}":".createFolderToggle","{cancelCreateFolderButton}":".cancelCreateFolderButton"}},function(t){return{init:function(){t.browser.navigation=t},setPathway:function(n){t.element.empty();if(n===undefined)return;t.currentPath=n;var r=t.browser.options.directorySeparator,i=n.split(r).splice(1),s=8,o=i.length-(i.length%s||s),u;t.view.item({title:t.browser.baseFolder||r}).addClass("base").data("path",r).appendTo(t.element),n!==r&&e.each(i,function(e,n){var a=r+i.slice(0,e+1).join(r),f=t.view.item({title:n}).data("path",a);if(e>=o){f.appendTo(t.element);return}e%s==0&&(u=t.view.itemGroup().appendTo(t.element)),f.appendTo(u)}),t.place.acl().canCreateFolder&&t.view.itemCreate().appendTo(t.element)},"{itemGroup} mouseover":function(e){clearTimeout(e.data("delayCollapse")),e.addClass("expand")},"{itemGroup} mouseout":function(e){e.data("delayCollapse",setTimeout(function(){e.removeClass("expand")},1e3))},"{item} click":function(e){if(e.hasClass("createFolder"))return;var n=e.data("path"),r=t.browser.itemByPath[n];t.browser.currentItem(r),t.browser.scrollTo(r)},showFolderCreationForm:function(){t.createFolderItem().addClass("edit"),t.createFolderInput().focus()[0].select()},hideFolderCreationForm:function(){t.createFolderItem().removeClass("edit")},"{createFolderToggle} click":function(e){t.showFolderCreationForm()},"{cancelCreateFolderButton} click":function(e){t.hideFolderCreationForm()},"{createFolderButton} click":function(){var n=e.trim(t.createFolderInput().val());if(n=="")return;EasyBlog.ajax("site.views.media.createFolder",{place:t.place.id,path:e.uri(t.currentPath).toPath("./"+n)+""},{beforeSend:function(){t.createFolderForm().addClass("busy"),t.createFolderInput().attr("disabled",!0)},success:function(e){var n=t.browser.itemByPath[t.currentPath],r=t.browser.createFolder(e,n);t.browser.focusItem(r),t.hideFolderCreationForm()},fail:function(e){t.createFolderForm().addClass("warning"),setTimeout(function(){t.createFolderInput().focus()},1)},complete:function(){t.createFolderForm().removeClass("busy"),t.createFolderInput().attr("disabled",!1)}})},"{createFolderInput} keyup":function(e,n){t.createFolderForm().removeClass("warning"),n.keyCode==13&&t.createFolderButton().trigger("click")}}}),t.resolve()})});