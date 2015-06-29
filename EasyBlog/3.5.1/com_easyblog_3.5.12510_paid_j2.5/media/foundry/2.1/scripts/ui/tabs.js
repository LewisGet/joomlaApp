(function(){var e=function(e){var t=this,n=e;e.require().script("ui/core","ui/widget").stylesheet("ui/tabs").done(function(){var e=function(){(function(e,t){function i(){return++n}var n=0,r=/#.*$/,s=function(e){return e=e.cloneNode(!1),e.hash.length>1&&e.href.replace(r,"")===location.href.replace(r,"")};e.widget("ui.tabs",{version:"1.9pre",options:{active:null,collapsible:!1,event:"click",fx:null,activate:null,beforeActivate:null,beforeLoad:null,load:null},_create:function(){var t=this,n=t.options,r=n.active;t.running=!1,t.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all"),t._processTabs();if(r===null){location.hash&&t.anchors.each(function(e,t){if(t.hash===location.hash)return r=e,!1}),r===null&&(r=t.lis.filter(".ui-tabs-active").index());if(r===null||r===-1)r=t.lis.length?0:!1}r!==!1&&(r=this.lis.eq(r).index(),r===-1&&(r=n.collapsible?!1:0)),n.active=r,!n.collapsible&&n.active===!1&&this.anchors.length&&(n.active=0),e.isArray(n.disabled)&&(n.disabled=e.unique(n.disabled.concat(e.map(this.lis.filter(".ui-state-disabled"),function(e,n){return t.lis.index(e)}))).sort()),this._setupFx(n.fx),this._refresh(),this.panels.hide(),this.lis.removeClass("ui-tabs-active ui-state-active");if(n.active!==!1&&this.anchors.length){this.active=this._findActive(n.active);var i=t._getPanelForTab(this.active);i.show(),this.lis.eq(n.active).addClass("ui-tabs-active ui-state-active"),this.load(n.active)}else this.active=e()},_getCreateEventData:function(){return{tab:this.active,panel:this.active.length?this._getPanelForTab(this.active):e()}},_setOption:function(e,t){if(e=="active"){this._activate(t);return}if(e==="disabled"){this._setupDisabled(t);return}this._super(e,t),e==="collapsible"&&!t&&this.options.active===!1&&this._activate(0),e==="event"&&this._setupEvents(t),e==="fx"&&this._setupFx(t)},_tabId:function(t){return e(t).attr("aria-controls")||"ui-tabs-"+i()},_sanitizeSelector:function(e){return e?e.replace(/[!"$%&'()*+,.\/:;<=>?@[\]^`{|}~]/g,"\\$&"):""},refresh:function(){var t=this,n=this.options,r=this.list.children(":has(a[href])");n.disabled=e.map(r.filter(".ui-state-disabled"),function(e){return r.index(e)}),this._processTabs(),this._refresh(),this.panels.not(this._getPanelForTab(this.active)).hide();if(n.active===!1||!this.anchors.length)n.active=!1,this.active=e();else if(this.active.length&&!e.contains(this.list[0],this.active[0])){var i=n.active-1;this._activate(i<0?0:i)}else n.active=this.anchors.index(this.active)},_refresh:function(){var e=this.options;this.element.toggleClass("ui-tabs-collapsible",e.collapsible),this.list.addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"),this.lis.addClass("ui-state-default ui-corner-top"),this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom"),this._setupDisabled(e.disabled),this._setupEvents(e.event),this.lis.unbind(".tabs"),this._focusable(this.lis),this._hoverable(this.lis)},_processTabs:function(){var t=this;this.list=this._getList(),this.lis=e(" > li:has(a[href])",this.list),this.anchors=this.lis.map(function(){return e("a",this)[0]}),this.panels=e([]),this.anchors.each(function(n,r){var i,o;if(s(r))i=r.hash,o=t.element.find(t._sanitizeSelector(i));else{var u=t._tabId(r);i="#"+u,o=t.element.find(i),o.length||(o=t._createPanel(u),o.insertAfter(t.panels[n-1]||t.list))}o.length&&(t.panels=t.panels.add(o)),e(r).attr("aria-controls",i.substring(1))})},_getList:function(){return this.element.find("ol,ul").eq(0)},_createPanel:function(t){return e("<div></div>").attr("id",t).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").data("destroy.tabs",!0)},_setupDisabled:function(t){e.isArray(t)&&(t.length?t.length===this.anchors.length&&(t=!0):t=!1);for(var n=0,r;r=this.lis[n];n++)e(r).toggleClass("ui-state-disabled",t===!0||e.inArray(n,t)!==-1);this.options.disabled=t},_setupFx:function(t){t&&(e.isArray(t)?(this.hideFx=t[0],this.showFx=t[1]):this.hideFx=this.showFx=t)},_resetStyle:function(t,n){!e.support.opacity&&n.opacity&&t[0].style.removeAttribute("filter")},_setupEvents:function(t){this.anchors.unbind(".tabs"),t&&this.anchors.bind(t.split(" ").join(".tabs ")+".tabs",e.proxy(this,"_eventHandler")),this.anchors.bind("click.tabs",function(e){e.preventDefault()})},_eventHandler:function(t){var n=this,r=n.options,i=n.active,s=e(t.currentTarget),o=s[0]===i[0],u=o&&r.collapsible,a=u?e():n._getPanelForTab(s),f=i.length?n._getPanelForTab(i):e(),l=s.closest("li"),c={oldTab:i,oldPanel:f,newTab:u?e():s,newPanel:a};t.preventDefault();if(l.hasClass("ui-state-disabled")||l.hasClass("ui-tabs-loading")||n.running||o&&!r.collapsible||n._trigger("beforeActivate",t,c)===!1){s[0].blur();return}r.active=u?!1:n.anchors.index(s),n.active=o?e():s,n.xhr&&n.xhr.abort();if(!f.length&&!a.length)throw"jQuery UI Tabs: Mismatching fragment identifier.";a.length&&(n.load(n.anchors.index(s),t),s[0].blur()),n._toggle(t,c)},_toggle:function(t,n){function u(){r.running=!1,r._trigger("activate",t,n)}function a(){n.newTab.closest("li").addClass("ui-tabs-active ui-state-active"),s.length&&r.showFx?s.animate(r.showFx,r.showFx.duration||"normal",function(){r._resetStyle(e(this),r.showFx),u()}):(s.show(),u())}var r=this,i=r.options,s=n.newPanel,o=n.oldPanel;r.running=!0,o.length&&r.hideFx?o.animate(r.hideFx,r.hideFx.duration||"normal",function(){n.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"),r._resetStyle(e(this),r.hideFx),a()}):(n.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active"),o.hide(),a())},_activate:function(t){var n=this._findActive(t)[0];if(n===this.active[0])return;n=n||this.active[0],this._eventHandler({target:n,currentTarget:n,preventDefault:e.noop})},_findActive:function(t){return typeof t=="number"?this.anchors.eq(t):typeof t=="string"?this.anchors.filter("[href$='"+t+"']"):e()},_getIndex:function(e){return typeof e=="string"&&(e=this.anchors.index(this.anchors.filter("[href$="+e+"]"))),e},_destroy:function(){var t=this.options;return this.xhr&&this.xhr.abort(),this.element.removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible"),this.list.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"),this.anchors.unbind(".tabs").removeData("href.tabs").removeData("load.tabs"),this.lis.unbind(".tabs").add(this.panels).each(function(){e.data(this,"destroy.tabs")?e(this).remove():e(this).removeClass(["ui-state-default","ui-corner-top","ui-tabs-active","ui-state-active","ui-state-disabled","ui-tabs-panel","ui-widget-content","ui-corner-bottom"].join(" "))}),this},enable:function(n){var r=this.options.disabled;if(r===!1)return;n===t?r=!1:(n=this._getIndex(n),e.isArray(r)?r=e.map(r,function(e){return e!==n?e:null}):r=e.map(this.lis,function(e,t){return t!==n?t:null})),this._setupDisabled(r)},disable:function(n){var r=this.options.disabled;if(r===!0)return;if(n===t)r=!0;else{n=this._getIndex(n);if(e.inArray(n,r)!==-1)return;e.isArray(r)?r=e.merge([n],r).sort():r=[n]}this._setupDisabled(r)},load:function(t,n){t=this._getIndex(t);var r=this,i=this.options,o=this.anchors.eq(t),u=r._getPanelForTab(o),a={tab:o,panel:u};if(s(o[0]))return;return this.xhr=e.ajax({url:o.attr("href"),beforeSend:function(t,i){return r._trigger("beforeLoad",n,e.extend({jqXHR:t,ajaxSettings:i},a))}}),this.xhr&&(this.lis.eq(t).addClass("ui-tabs-loading"),this.xhr.success(function(e){setTimeout(function(){u.html(e),r._trigger("load",n,a)},1)}).complete(function(e,n){setTimeout(function(){n==="abort"&&r.panels.stop(!1,!0),r.lis.eq(t).removeClass("ui-tabs-loading"),e===r.xhr&&delete r.xhr},1)})),this},_getPanelForTab:function(t){var n=e(t).attr("aria-controls");return this.element.find(this._sanitizeSelector("#"+n))}});if(e.uiBackCompat!==!1){e.ui.tabs.prototype._ui=function(e,t){return{tab:e,panel:t,index:this.anchors.index(e)}},e.widget("ui.tabs",e.ui.tabs,{url:function(e,t){this.anchors.eq(e).attr("href",t)}}),e.widget("ui.tabs",e.ui.tabs,{options:{ajaxOptions:null,cache:!1},_create:function(){this._super();var t=this;this.element.bind("tabsbeforeload.tabs",function(n,r){if(e.data(r.tab[0],"cache.tabs")){n.preventDefault();return}e.extend(r.ajaxSettings,t.options.ajaxOptions,{error:function(e,n,i){try{t.options.ajaxOptions.error(e,n,r.tab.closest("li").index(),r.tab[0])}catch(i){}}}),r.jqXHR.success(function(){t.options.cache&&e.data(r.tab[0],"cache.tabs",!0)})})},_setOption:function(e,t){e==="cache"&&t===!1&&this.anchors.removeData("cache.tabs"),this._super(e,t)},_destroy:function(){this.anchors.removeData("cache.tabs"),this._super()},url:function(e,t){this.anchors.eq(e).removeData("cache.tabs"),this._superApply(arguments)}}),e.widget("ui.tabs",e.ui.tabs,{abort:function(){this.xhr&&this.xhr.abort()}}),e.widget("ui.tabs",e.ui.tabs,{options:{spinner:"<em>Loading&#8230;</em>"},_create:function(){this._super(),this._bind({tabsbeforeload:function(e,t){if(!this.options.spinner)return;var n=t.tab.find("span"),r=n.html();n.html(this.options.spinner),t.jqXHR.complete(function(){n.html(r)})}})}}),e.widget("ui.tabs",e.ui.tabs,{options:{enable:null,disable:null},enable:function(t){var n=this.options,r;if(t&&n.disabled===!0||e.isArray(n.disabled)&&e.inArray(t,n.disabled)!==-1)r=!0;this._superApply(arguments),r&&this._trigger("enable",null,this._ui(this.anchors[t],this.panels[t]))},disable:function(t){var n=this.options,r;if(t&&n.disabled===!1||e.isArray(n.disabled)&&e.inArray(t,n.disabled)===-1)r=!0;this._superApply(arguments),r&&this._trigger("disable",null,this._ui(this.anchors[t],this.panels[t]))}}),e.widget("ui.tabs",e.ui.tabs,{options:{add:null,remove:null,tabTemplate:"<li><a href='#{href}'><span>#{label}</span></a></li>"},add:function(n,r,i){i===t&&(i=this.anchors.length);var s=this.options,o=e(s.tabTemplate.replace(/#\{href\}/g,n).replace(/#\{label\}/g,r)),u=n.indexOf("#")?this._tabId(o.find("a")[0]):n.replace("#","");o.addClass("ui-state-default ui-corner-top").data("destroy.tabs",!0),o.find("a").attr("aria-controls",u);var a=i>=this.lis.length,f=this.element.find("#"+u);return f.length||(f=this._createPanel(u),a?i>0?f.insertAfter(this.panels.eq(-1)):f.appendTo(this.element):f.insertBefore(this.panels[i])),f.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").hide(),a?o.appendTo(this.list):o.insertBefore(this.lis[i]),s.disabled=e.map(s.disabled,function(e){return e<i?e:++e}),this.refresh(),this.lis.length===1&&s.active===!1&&this.option("active",0),this._trigger("add",null,this._ui(this.anchors[i],this.panels[i])),this},remove:function(t){t=this._getIndex(t);var n=this.options,r=this.lis.eq(t).remove(),i=this._getPanelForTab(r.find("a[aria-controls]")).remove();return r.hasClass("ui-tabs-active")&&this.anchors.length>2&&this._activate(t+(t+1<this.anchors.length?1:-1)),n.disabled=e.map(e.grep(n.disabled,function(e){return e!==t}),function(e){return e<t?e:--e}),this.refresh(),this._trigger("remove",null,this._ui(r.find("a")[0],i[0])),this}}),e.widget("ui.tabs",e.ui.tabs,{length:function(){return this.anchors.length}}),e.widget("ui.tabs",e.ui.tabs,{options:{idPrefix:"ui-tabs-"},_tabId:function(t){return e(t).attr("aria-controls")||t.title&&t.title.replace(/\s/g,"_").replace(/[^\w\u00c0-\uFFFF-]/g,"")||this.options.idPrefix+i()}}),e.widget("ui.tabs",e.ui.tabs,{options:{panelTemplate:"<div></div>"},_createPanel:function(t){return e(this.options.panelTemplate).attr("id",t).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").data("destroy.tabs",!0)}}),e.widget("ui.tabs",e.ui.tabs,{_create:function(){var e=this.options;e.active===null&&e.selected!==t&&(e.active=e.selected===-1?!1:e.selected),this._super(),e.selected=e.active,e.selected===!1&&(e.selected=-1)},_setOption:function(e,t){if(e!=="selected")return this._super(e,t);var n=this.options;this._super("active",t===-1?!1:t),n.selected=n.active,n.selected===!1&&(n.selected=-1)},_eventHandler:function(e){this._superApply(arguments),this.options.selected=this.options.active,this.options.selected===!1&&(this.options.selected=-1)}}),e.widget("ui.tabs",e.ui.tabs,{options:{show:null,select:null},_create:function(){this._super(),this.options.active!==!1&&this._trigger("show",null,this._ui(this.active[0],this._getPanelForTab(this.active)[0]))},_trigger:function(e,t,n){var r=this._superApply(arguments);return r?(e==="beforeActivate"&&n.newTab.length?r=this._super("select",t,{tab:n.newTab[0],panel:n.newPanel[0],index:n.newTab.closest("li").index()}):e==="activate"&&n.newTab.length&&(r=this._super("show",t,{tab:n.newTab[0],panel:n.newPanel[0],index:n.newTab.closest("li").index()})),r):!1}}),e.widget("ui.tabs",e.ui.tabs,{select:function(e){e=this._getIndex(e);if(e===-1){if(!this.options.collapsible||this.options.selected===-1)return;e=this.options.selected}this.anchors.eq(e).trigger(this.options.event+".tabs")}});var o=0;function u(){return++o}e.widget("ui.tabs",e.ui.tabs,{options:{cookie:null},_create:function(){var e=this.options,t;e.active==null&&e.cookie&&(t=parseInt(this._cookie(),10),t===-1&&(t=!1),e.active=t),this._super()},_cookie:function(t){var n=[this.cookie||(this.cookie=this.options.cookie.name||"ui-tabs-"+u())];return arguments.length&&(n.push(t===!1?-1:t),n.push(this.options.cookie)),e.cookie.apply(null,n)},_refresh:function(){this._super(),this.options.cookie&&this._cookie(this.options.active,this.options.cookie)},_eventHandler:function(e){this._superApply(arguments),this.options.cookie&&this._cookie(this.options.active,this.options.cookie)},_destroy:function(){this._super(),this.options.cookie&&this._cookie(null,this.options.cookie)}}),e.widget("ui.tabs",e.ui.tabs,{_trigger:function(t,n,r){var i=e.extend({},r);return t==="load"&&(i.panel=i.panel[0],i.tab=i.tab[0]),this._super(t,n,i)}})}})(n)};e(),t.resolveWith(e)})};dispatch("ui/tabs").containing(e).to("Foundry/2.1 Modules")})();