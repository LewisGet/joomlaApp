EasyBlog.module("dashboard/media",function(e){var t=this;EasyBlog.require().done(function(){EasyBlog.Controller("Dashboard.Media",{defaultOptions:{url:EasyBlog.baseUrl+"&view=media&tmpl=component&e_name=write_content"}},function(t){return{init:function(){EasyBlog.dashboard.registerPlugin("media",t)},items:{},addItem:function(n){t.items[n.place]=[n].concat(t.items[n.place]),e.each(t.managers,function(e,t){t.addItem(n)})},removeItem:function(n){var r=t.items[n.place];r=e.grep(r,function(e){return e.path!==n.path}),e.each(t.managers,function(e,t){t.removeItem(n)})},managers:[],registerManager:function(e){t.managers.push(e)},loadManager:function(n){t.modal=e(document.createElement("iframe")).appendTo("body").css({position:"fixed",width:"100%",height:"100%",top:0,left:0,opacity:0,zIndex:999999}).one("load",function(){return t.showManager(),n&&n()}).attr({frameborder:0,allowTransparency:"true",src:t.options.url})},showManager:function(e){if(t.modal==undefined)return t.loadManager(e);t.modal.css({left:0}).animate({opacity:1},{complete:function(){t.maxi&&t.maxi.setLayout(),e&&e()}})},hideManager:function(){t.modal.animate({opacity:0},{complete:function(){t.modal.css({left:t.modal.width()*-1})}})}}}),t.resolve()})});