(function(){var e=function(e){var t=this;e.require().script("mvc/class").done(function(){var n=function(){var t=e.isArray,n=function(e){return typeof e=="object"&&e!==null&&e},r=e.makeArray,i=e.each,s=function(n,r,i){return n instanceof e.Observe?o([n],i._namespace):t(n)?n=new e.Observe.List(n):n=new e.Observe(n),n.bind("change"+i._namespace,function(t,s){var o=e.makeArray(arguments),t=o.shift();r==="*"?o[0]=i.indexOf(n)+"."+o[0]:o[0]=r+"."+o[0],e.event.trigger(t,o,i)}),n},o=function(e,t){var n;for(var r=0;r<e.length;r++)n=e[r],n&&n.unbind&&n.unbind("change"+t)},u=0,a=null,f=function(){if(!a)return a=[],!0},l=function(t,n,r){if(t._init)return;if(!a)return e.event.trigger(n,r,t,!0);a.push({t:t,ev:n,args:r})},c=0,h=function(){var t=a.length,n=a.slice(0),r;a=null,c++;for(var i=0;i<t;i++)r=n[i],e.event.trigger({type:r.ev,batchNum:c},r.args,r.t)},p=function(e,t,r){return e.each(function(e,i){r[e]=n(i)&&typeof i[t]=="function"?i[t]():i}),r};e.Class(e.globalNamespace+".Observe",{init:function(e){this._data={},this._namespace=".observe"+ ++u,this._init=!0,this.attrs(e),delete this._init},attr:function(e,t){return t===undefined?this._get(e):(this._set(e,t),this)},each:function(){return i.apply(null,[this.__get()].concat(r(arguments)))},removeAttr:function(e){var n=t(e)?e:e.split("."),r=n.shift(),i=this._data[r];return n.length?i.removeAttr(n):(delete this._data[r],l(this,"change",[r,"remove",undefined,i]),i)},_get:function(e){var n=t(e)?e:(""+e).split("."),r=this.__get(n.shift());return n.length?r?r._get(n):undefined:r},__get:function(e){return e?this._data[e]:this._data},_set:function(e,r){var i=t(e)?e:(""+e).split("."),u=i.shift(),a=this.__get(u);if(n(a)&&i.length)a._set(i,r);else{if(!!i.length)throw"jQuery.Observe: set a property on an object that does not exist";if(r!==a){var f=this.__get().hasOwnProperty(u)?"set":"add";this.__set(u,n(r)?s(r,u,this):r),l(this,"change",[u,f,r,a]),a&&o([a],this._namespace)}}},__set:function(e,t){this._data[e]=t,e in this.constructor.prototype||(this[e]=t)},bind:function(t,n){return e.fn.bind.apply(e([this]),arguments),this},unbind:function(t,n){return e.fn.unbind.apply(e([this]),arguments),this},serialize:function(){return p(this,"serialize",{})},attrs:function(t,r){if(t===undefined)return p(this,"attrs",{});t=e.extend(!0,{},t);var i,s=f();for(i in this._data){var o=this._data[i],u=t[i];if(u===undefined){r&&this.removeAttr(i);continue}n(o)&&n(u)?o.attrs(u,r):o!=u&&this._set(i,u),delete t[i]}for(var i in t)u=t[i],this._set(i,u);s&&h()}});var d=e.Observe(e.globalNamespace+".Observe.List",{init:function(t,n){this.length=0,this._namespace=".list"+ ++u,this._init=!0,this.bind("change",this.proxy("_changes")),this.push.apply(this,r(t||[])),e.extend(this,n),this.comparator&&this.sort(),delete this._init},_changes:function(e,t,n,r,i){if(this.comparator&&/^\d+./.test(t)){var s=+/^\d+/.exec(t)[0],o=this[s],u=this.sortedIndex(o);if(u!==s){[].splice.call(this,s,1),[].splice.call(this,u,0,o),l(this,"move",[o,u,s]),e.stopImmediatePropagation(),l(this,"change",[t.replace(/^\d+/,u),n,r,i]);return}}t.indexOf(".")===-1&&(n==="add"?l(this,n,[r,+t]):n==="remove"&&l(this,n,[i,+t]))},sortedIndex:function(e){var t=e.attr(this.comparator),n=0,r;for(var r=0;r<this.length;r++){if(e===this[r]){n=-1;continue}if(t<=this[r].attr(this.comparator))return r+n}return r+n},__get:function(e){return e?this[e]:this},__set:function(e,t){this[e]=t},serialize:function(){return p(this,"serialize",[])},splice:function(e,t){var i=r(arguments),u;for(u=2;u<i.length;u++){var a=i[u];n(a)&&(i[u]=s(a,"*",this))}t===undefined&&(t=i[1]=this.length-e);var f=[].splice.apply(this,i);return t>0&&(l(this,"change",[""+e,"remove",undefined,f]),o(f,this._namespace)),i.length>2&&l(this,"change",[""+e,"add",i.slice(2),f]),f},attrs:function(e,t){if(e===undefined)return p(this,"attrs",[]);e=e.slice(0);var r=Math.min(e.length,this.length),i=f();for(var s=0;s<r;s++){var o=this[s],u=e[s];n(o)&&n(u)?o.attrs(u,t):o!=u&&this._set(s,u)}e.length>this.length?this.push(e.slice(this.length)):e.length<this.length&&t&&this.splice(e.length),i&&h()},sort:function(e,t){var n=this.comparator,r=n?[function(e,t){return e=e[n],t=t[n],e===t?0:e<t?-1:1}]:[],i=[].sort.apply(this,r);!t&&l(this,"reset")}}),v=function(t){return t[0]&&e.isArray(t[0])?t[0]:r(t)};i({push:"length",unshift:0},function(e,t){d.prototype[e]=function(){var r=v(arguments),i=t?this.length:0;for(var o=0;o<r.length;o++){var u=r[o];n(u)&&(r[o]=s(u,"*",this))}if(r.length==1&&this.comparator){var a=this.sortedIndex(r[0]);return this.splice(a,0,r[0]),this.length}var f=[][e].apply(this,r);return this.comparator&&r.length>1?(this.sort(null,!0),l(this,"reset",[r])):l(this,"change",[""+i,"add",r,undefined]),f}}),i({pop:"length",shift:0},function(e,t){d.prototype[e]=function(){var n=v(arguments),r=t&&this.length?this.length-1:0,i=[][e].apply(this,n);return l(this,"change",[""+r,"remove",undefined,[i]]),i&&i.unbind&&i.unbind("change"+this._namespace),i}}),d.prototype.indexOf=[].indexOf||function(t){return e.inArray(t,this)},e.O=function(n,r){return t(n)||n instanceof e.Observe.List?new e.Observe.List(n,r):new e.Observe(n,r)}};n(),t.resolveWith(n)})};dispatch("mvc/lang.observe").containing(e).to("Foundry/2.1 Modules")})();