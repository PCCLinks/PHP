/*! jquery.fixedHeaderTable. The jQuery fixedHeaderTable plugin
*
* Copyright (c) 2011 Mark Malek
* http://fixedheadertable.com
*
* Licensed under MIT
* http://www.opensource.org/licenses/mit-license.php
* 
* http://docs.jquery.com/Plugins/Authoring
* jQuery authoring guidelines
*
* Launch  : October 2009
* Version : 1.2.2
* Released: May 9th, 2011
*
* 
* all CSS sizing (width,height) is done in pixels (px)
*/
(function(a){a.fn.fixedHeaderTable=function(f){var e={width:"100%",height:"100%",borderCollapse:true,themeClass:"fht-default",autoShow:true,loader:false,footer:false,cloneHeadToFoot:false,cloneHeaderToFooter:false,autoResize:false,create:null};var c={};var b={init:function(g){c=a.extend({},e,g);return this.each(function(){var i=a(this),h=this;if(d._isTable(i)){b.setup.apply(this,Array.prototype.slice.call(arguments,1));a.isFunction(c.create)&&c.create.call(this)}else{a.error("Invalid table mark-up")}})},setup:function(v){var t=a(this),u=this,m=t.find("thead"),p=t.find("tfoot"),i=t.find("tbody"),k,o,s,j,h,r,n=0;c.scrollbarOffset=d._getScrollbarWidth();c.themeClassName=c.themeClass;if(c.width.search("%")>-1){var q=t.parent().width()-c.scrollbarOffset}else{var q=c.width-c.scrollbarOffset}t.css({width:q});if(!t.closest(".fht-table-wrapper").length){t.addClass("fht-table");t.wrap('<div class="fht-table-wrapper"></div>')}k=t.closest(".fht-table-wrapper");k.css({width:c.width,height:c.height}).addClass(c.themeClassName);if(!t.hasClass("fht-table-init")){t.wrap('<div class="fht-tbody"></div>')}j=t.closest(".fht-tbody");var g=d._getTableProps(t);d._setupClone(j,g.tbody);if(!t.hasClass("fht-table-init")){o=a('<div class="fht-thead"><table class="fht-table"></table></div>').prependTo(k);m.clone().appendTo(o.find("table"))}else{o=k.find("div.fht-thead")}d._setupClone(o,g.thead);t.css({"margin-top":-m.outerHeight(true)-g.border});if(c.footer==true){d._setupTableFooter(t,u,g);if(!p.length){p=k.find("div.fht-tfoot table")}n=p.outerHeight(true)}var l=k.height()-m.outerHeight(true)-n-g.border;j.css({height:l});if(!c.autoShow){k.hide()}t.addClass("fht-table-init");if(typeof(c.altClass)!=="undefined"){t.find("tbody tr:odd").addClass(c.altClass)}d._bindScroll(j);return u},resize:function(h){var i=a(this),g=this;return g},show:function(j,i,g){var l=a(this),h=this,k=l.closest(".fht-table-wrapper");if(typeof(j)!=="undefined"&&typeof(j)==="number"){k.show(j,function(){a.isFunction(g)&&g.call(this)});return h}else{if(typeof(j)!=="undefined"&&typeof(j)==="string"&&typeof(i)!=="undefined"&&typeof(i)==="number"){k.show(j,i,function(){a.isFunction(g)&&g.call(this)});return h}}l.closest(".fht-table-wrapper").show();a.isFunction(g)&&g.call(this);return h},hide:function(j,i,g){var l=a(this),h=this,k=l.closest(".fht-table-wrapper");if(typeof(j)!=="undefined"&&typeof(j)==="number"){k.hide(j,function(){a.isFunction(g)&&g.call(this)});return h}else{if(typeof(j)!=="undefined"&&typeof(j)==="string"&&typeof(i)!=="undefined"&&typeof(i)==="number"){k.hide(j,i,function(){a.isFunction(g)&&g.call(this)});return h}}l.closest(".fht-table-wrapper").hide();a.isFunction(g)&&g.call(this);return h},destroy:function(){var i=a(this),g=this,h=i.closest(".fht-table-wrapper");i.insertBefore(h).removeAttr("style").append(h.find("tfoot")).removeClass("fht-table fht-table-init").find(".fht-cell").remove();h.remove();return g}};var d={_isTable:function(k){var j=k,h=j.is("table"),i=j.find("thead").length>0,g=j.find("tbody").length>0;if(h&&i&&g){return true}return false},_bindScroll:function(j){var i=j,h=i.siblings(".fht-thead"),g=i.siblings(".fht-tfoot");i.bind("scroll",function(){h.find("table").css({"margin-left":-this.scrollLeft});if(c.cloneHeadToFoot){g.find("table").css({"margin-left":-this.scrollLeft})}})},_setupTableFooter:function(g,i,h){var m=g,o=i,j=m.closest(".fht-table-wrapper"),l=m.find("tfoot"),n=j.find("div.fht-tfoot");if(!n.length){n=a('<div class="fht-tfoot"><table class="fht-table"></table></div>').appendTo(j)}switch(true){case !l.length&&c.cloneHeadToFoot==true&&c.footer==true:var k=j.find("div.fht-thead");n.empty();k.find("table").clone().appendTo(n);break;case l.length&&c.cloneHeadToFoot==false&&c.footer==true:n.find("table").append(l).css({"margin-top":-h.border});d._setupClone(n,h.tfoot);break}},_getTableProps:function(h){var g={thead:{},tbody:{},tfoot:{},border:0};g.border=(h.find("th:first-child").outerWidth()-h.find("th:first-child").innerWidth())/2;h.find("thead tr:first-child th").each(function(i){g.thead[i]=a(this).width()+g.border});h.find("tfoot tr:first-child td").each(function(i){g.tfoot[i]=a(this).width()+g.border});h.find("tbody tr:first-child td").each(function(i){g.tbody[i]=a(this).width()+g.border});return g},_setupClone:function(k,j){var i=k,g=(i.find("thead").length)?"thead th":(i.find("tfoot").length)?"tfoot td":"tbody td",h;i.find(g).each(function(l){h=(a(this).find("div.fht-cell").length)?a(this).find("div.fht-cell"):a('<div class="fht-cell"></div>').appendTo(a(this));h.css({width:parseInt(j[l])});if(!a(this).closest(".fht-tbody").length&&a(this).is(":last-child")){var m=((a(this).innerWidth()-a(this).width())/2)+c.scrollbarOffset;a(this).css({"padding-right":m+"px"})}})},_getScrollbarWidth:function(){var h=0;if(!h){if(a.browser.msie){var j=a('<textarea cols="10" rows="2"></textarea>').css({position:"absolute",top:-1000,left:-1000}).appendTo("body"),i=a('<textarea cols="10" rows="2" style="overflow: hidden;"></textarea>').css({position:"absolute",top:-1000,left:-1000}).appendTo("body");h=j.width()-i.width()+2;j.add(i).remove()}else{var g=a("<div />").css({width:100,height:100,overflow:"auto",position:"absolute",top:-1000,left:-1000}).prependTo("body").append("<div />").find("div").css({width:"100%",height:200});h=100-g.width();g.parent().remove()}}return h}};if(b[f]){return b[f].apply(this,Array.prototype.slice.call(arguments,1))}else{if(typeof f==="object"||!f){return b.init.apply(this,arguments)}else{a.error('Method "'+f+'" does not exist in fixedHeaderTable plugin!')}}}})(jQuery);