!function(e){"object"==typeof module&&module.exports?(e.default=e,module.exports=e):"function"==typeof define&&define.amd?define("highcharts/modules/exporting",["highcharts"],function(t){return e(t),e.Highcharts=t,e}):e("undefined"!=typeof Highcharts?Highcharts:void 0)}(function(e){function t(e,t,n,i){e.hasOwnProperty(t)||(e[t]=i.apply(null,n))}t(e=e?e._modules:{},"Extensions/FullScreen.js",[e["Core/Chart/Chart.js"],e["Core/Globals.js"],e["Core/Utilities.js"]],function(e,t,n){var i=n.addEvent;return n=function(){function e(e){this.chart=e,this.isOpen=!1,e=e.renderTo,this.browserProps||("function"==typeof e.requestFullscreen?this.browserProps={fullscreenChange:"fullscreenchange",requestFullscreen:"requestFullscreen",exitFullscreen:"exitFullscreen"}:e.mozRequestFullScreen?this.browserProps={fullscreenChange:"mozfullscreenchange",requestFullscreen:"mozRequestFullScreen",exitFullscreen:"mozCancelFullScreen"}:e.webkitRequestFullScreen?this.browserProps={fullscreenChange:"webkitfullscreenchange",requestFullscreen:"webkitRequestFullScreen",exitFullscreen:"webkitExitFullscreen"}:e.msRequestFullscreen&&(this.browserProps={fullscreenChange:"MSFullscreenChange",requestFullscreen:"msRequestFullscreen",exitFullscreen:"msExitFullscreen"}))}return e.prototype.close=function(){var e=this.chart;this.isOpen&&this.browserProps&&e.container.ownerDocument instanceof Document&&e.container.ownerDocument[this.browserProps.exitFullscreen](),this.unbindFullscreenEvent&&this.unbindFullscreenEvent(),this.isOpen=!1,this.setButtonText()},e.prototype.open=function(){var e=this,t=e.chart;if(e.browserProps){e.unbindFullscreenEvent=i(t.container.ownerDocument,e.browserProps.fullscreenChange,function(){e.isOpen?(e.isOpen=!1,e.close()):(e.isOpen=!0,e.setButtonText())});var n=t.renderTo[e.browserProps.requestFullscreen]();n&&n.catch(function(){alert("Full screen is not supported inside a frame.")}),i(t,"destroy",e.unbindFullscreenEvent)}},e.prototype.setButtonText=function(){var e,t=this.chart,n=t.exportDivElements,i=t.options.exporting,o=null===(e=null==i?void 0:i.buttons)||void 0===e?void 0:e.contextButton.menuItems;e=t.options.lang,null!=i&&i.menuItemDefinitions&&null!=e&&e.exitFullscreen&&e.viewFullscreen&&o&&n&&n.length&&(n[o.indexOf("viewFullscreen")].innerHTML=this.isOpen?e.exitFullscreen:i.menuItemDefinitions.viewFullscreen.text||e.viewFullscreen)},e.prototype.toggle=function(){this.isOpen?this.close():this.open()},e}(),t.Fullscreen=n,i(e,"beforeRender",function(){this.fullscreen=new t.Fullscreen(this)}),t.Fullscreen}),t(e,"Mixins/Navigation.js",[],function(){return{initUpdate:function(e){e.navigation||(e.navigation={updates:[],update:function(e,t){this.updates.forEach(function(n){n.update.call(n.context,e,t)})}})},addUpdate:function(e,t){t.navigation||this.initUpdate(t),t.navigation.updates.push({update:e,context:t})}}}),t(e,"Extensions/Exporting.js",[e["Core/Chart/Chart.js"],e["Mixins/Navigation.js"],e["Core/Globals.js"],e["Core/Options.js"],e["Core/Renderer/SVG/SVGRenderer.js"],e["Core/Utilities.js"]],function(e,t,n,i,o,r){var s=n.doc,l=n.isTouchDevice,a=n.win;i=i.defaultOptions;var u=r.addEvent,c=r.css,p=r.createElement,h=r.discardElement,d=r.extend,f=r.find,g=r.fireEvent,m=r.isObject,x=r.merge,v=r.objectEach,y=r.pick,b=r.removeEvent,w=r.uniqueKey,E=a.navigator.userAgent,S=n.Renderer.prototype.symbols,C=/Edge\/|Trident\/|MSIE /.test(E),F=/firefox/i.test(E);d(i.lang,{viewFullscreen:"View in full screen",exitFullscreen:"Exit from full screen",printChart:"Print chart",downloadPNG:"Download PNG image",downloadJPEG:"Download JPEG image",downloadPDF:"Download PDF document",downloadSVG:"Download SVG vector image",contextButtonTitle:"Chart context menu"}),i.navigation||(i.navigation={}),x(!0,i.navigation,{buttonOptions:{theme:{},symbolSize:14,symbolX:12.5,symbolY:10.5,align:"right",buttonSpacing:3,height:22,verticalAlign:"top",width:24}}),x(!0,i.navigation,{menuStyle:{border:"1px solid #999999",background:"#ffffff",padding:"5px 0"},menuItemStyle:{padding:"0.5em 1em",color:"#333333",background:"none",fontSize:l?"14px":"11px",transition:"background 250ms, color 250ms"},menuItemHoverStyle:{background:"#335cad",color:"#ffffff"},buttonOptions:{symbolFill:"#666666",symbolStroke:"#666666",symbolStrokeWidth:3,theme:{padding:5}}}),i.exporting={type:"image/png",url:"https://export.highcharts.com/",printMaxWidth:780,scale:2,buttons:{contextButton:{className:"highcharts-contextbutton",menuClassName:"highcharts-contextmenu",symbol:"menu",titleKey:"contextButtonTitle",menuItems:"viewFullscreen printChart separator downloadPNG downloadJPEG downloadPDF downloadSVG".split(" ")}},menuItemDefinitions:{viewFullscreen:{textKey:"viewFullscreen",onclick:function(){this.fullscreen.toggle()}},printChart:{textKey:"printChart",onclick:function(){this.print()}},separator:{separator:!0},downloadPNG:{textKey:"downloadPNG",onclick:function(){this.exportChart()}},downloadJPEG:{textKey:"downloadJPEG",onclick:function(){this.exportChart({type:"image/jpeg"})}},downloadPDF:{textKey:"downloadPDF",onclick:function(){this.exportChart({type:"application/pdf"})}},downloadSVG:{textKey:"downloadSVG",onclick:function(){this.exportChart({type:"image/svg+xml"})}}}},n.post=function(e,t,n){var i=p("form",x({method:"post",action:e,enctype:"multipart/form-data"},n),{display:"none"},s.body);v(t,function(e,t){p("input",{type:"hidden",name:t,value:e},null,i)}),i.submit(),h(i)},n.isSafari&&n.win.matchMedia("print").addListener(function(e){n.printingChart&&(e.matches?n.printingChart.beforePrint():n.printingChart.afterPrint())}),d(e.prototype,{sanitizeSVG:function(e,t){var n=e.indexOf("</svg>")+6,i=e.substr(n);return e=e.substr(0,n),t&&t.exporting&&t.exporting.allowHTML&&i&&(i='<foreignObject x="0" y="0" width="'+t.chart.width+'" height="'+t.chart.height+'"><body xmlns="http://www.w3.org/1999/xhtml">'+i.replace(/(<(?:img|br).*?(?=>))>/g,"$1 />")+"</body></foreignObject>",e=e.replace("</svg>",i+"</svg>")),e=e.replace(/zIndex="[^"]+"/g,"").replace(/symbolName="[^"]+"/g,"").replace(/jQuery[0-9]+="[^"]+"/g,"").replace(/url\(("|&quot;)(.*?)("|&quot;);?\)/g,"url($2)").replace(/url\([^#]+#/g,"url(#").replace(/<svg /,'<svg xmlns:xlink="http://www.w3.org/1999/xlink" ').replace(/ (|NS[0-9]+:)href=/g," xlink:href=").replace(/\n/," ").replace(/(fill|stroke)="rgba\(([ 0-9]+,[ 0-9]+,[ 0-9]+),([ 0-9\.]+)\)"/g,'$1="rgb($2)" $1-opacity="$3"').replace(/&nbsp;/g," ").replace(/&shy;/g,"­"),this.ieSanitizeSVG&&(e=this.ieSanitizeSVG(e)),e},getChartHTML:function(){return this.styledMode&&this.inlineStyles(),this.container.innerHTML},getSVG:function(t){var n,i=x(this.options,t);i.plotOptions=x(this.userOptions.plotOptions,t&&t.plotOptions),i.time=x(this.userOptions.time,t&&t.time);var o=p("div",null,{position:"absolute",top:"-9999em",width:this.chartWidth+"px",height:this.chartHeight+"px"},s.body),r=this.renderTo.style.width,l=this.renderTo.style.height;r=i.exporting.sourceWidth||i.chart.width||/px$/.test(r)&&parseInt(r,10)||(i.isGantt?800:600),l=i.exporting.sourceHeight||i.chart.height||/px$/.test(l)&&parseInt(l,10)||400,d(i.chart,{animation:!1,renderTo:o,forExport:!0,renderer:"SVGRenderer",width:r,height:l}),i.exporting.enabled=!1,delete i.data,i.series=[],this.series.forEach(function(e){(n=x(e.userOptions,{animation:!1,enableMouseTracking:!1,showCheckbox:!1,visible:e.visible})).isInternal||i.series.push(n)}),this.axes.forEach(function(e){e.userOptions.internalKey||(e.userOptions.internalKey=w())});var a=new e(i,this.callback);return t&&["xAxis","yAxis","series"].forEach(function(e){var n={};t[e]&&(n[e]=t[e],a.update(n))}),this.axes.forEach(function(e){var t=f(a.axes,function(t){return t.options.internalKey===e.userOptions.internalKey}),n=e.getExtremes(),i=n.userMin;n=n.userMax,t&&(void 0!==i&&i!==t.min||void 0!==n&&n!==t.max)&&t.setExtremes(i,n,!0,!1)}),r=a.getChartHTML(),g(this,"getSVG",{chartCopy:a}),r=this.sanitizeSVG(r,i),i=null,a.destroy(),h(o),r},getSVGForExport:function(e,t){var n=this.options.exporting;return this.getSVG(x({chart:{borderRadius:0}},n.chartOptions,t,{exporting:{sourceWidth:e&&e.sourceWidth||n.sourceWidth,sourceHeight:e&&e.sourceHeight||n.sourceHeight}}))},getFilename:function(){var e=this.userOptions.title&&this.userOptions.title.text,t=this.options.exporting.filename;return t?t.replace(/\//g,"-"):("string"==typeof e&&(t=e.toLowerCase().replace(/<\/?[^>]+(>|$)/g,"").replace(/[\s_]+/g,"-").replace(/[^a-z0-9\-]/g,"").replace(/^[\-]+/g,"").replace(/[\-]+/g,"-").substr(0,24).replace(/[\-]+$/g,"")),(!t||5>t.length)&&(t="chart"),t)},exportChart:function(e,t){t=this.getSVGForExport(e,t),e=x(this.options.exporting,e),n.post(e.url,{filename:e.filename?e.filename.replace(/\//g,"-"):this.getFilename(),type:e.type,width:e.width||0,scale:e.scale,svg:t},e.formAttributes)},moveContainers:function(e){(this.fixedDiv?[this.fixedDiv,this.scrollingContainer]:[this.container]).forEach(function(t){e.appendChild(t)})},beforePrint:function(){var e=s.body,t=this.options.exporting.printMaxWidth,n={childNodes:e.childNodes,origDisplay:[],resetParams:void 0};this.isPrinting=!0,this.pointer.reset(null,0),g(this,"beforePrint"),t&&this.chartWidth>t&&(n.resetParams=[this.options.chart.width,void 0,!1],this.setSize(t,void 0,!1)),[].forEach.call(n.childNodes,function(e,t){1===e.nodeType&&(n.origDisplay[t]=e.style.display,e.style.display="none")}),this.moveContainers(e),this.printReverseInfo=n},afterPrint:function(){if(this.printReverseInfo){var e=this.printReverseInfo.childNodes,t=this.printReverseInfo.origDisplay,i=this.printReverseInfo.resetParams;this.moveContainers(this.renderTo),[].forEach.call(e,function(e,n){1===e.nodeType&&(e.style.display=t[n]||"")}),this.isPrinting=!1,i&&this.setSize.apply(this,i),delete this.printReverseInfo,delete n.printingChart,g(this,"afterPrint")}},print:function(){var e=this;e.isPrinting||(n.printingChart=e,n.isSafari||e.beforePrint(),setTimeout(function(){a.focus(),a.print(),n.isSafari||setTimeout(function(){e.afterPrint()},1e3)},1))},contextMenu:function(e,t,n,i,o,l,h){var f=this,x=f.options.navigation,v=f.chartWidth,y=f.chartHeight,b="cache-"+e,w=f[b],E=Math.max(o,l);if(!w){f.exportContextMenu=f[b]=w=p("div",{className:e},{position:"absolute",zIndex:1e3,padding:E+"px",pointerEvents:"auto"},f.fixedDiv||f.container);var S=p("ul",{className:"highcharts-menu"},{listStyle:"none",margin:0,padding:0},w);f.styledMode||c(S,d({MozBoxShadow:"3px 3px 10px #888",WebkitBoxShadow:"3px 3px 10px #888",boxShadow:"3px 3px 10px #888"},x.menuStyle)),w.hideMenu=function(){c(w,{display:"none"}),h&&h.setState(0),f.openMenu=!1,c(f.renderTo,{overflow:"hidden"}),r.clearTimeout(w.hideTimer),g(f,"exportMenuHidden")},f.exportEvents.push(u(w,"mouseleave",function(){w.hideTimer=a.setTimeout(w.hideMenu,500)}),u(w,"mouseenter",function(){r.clearTimeout(w.hideTimer)}),u(s,"mouseup",function(t){f.pointer.inClass(t.target,e)||w.hideMenu()}),u(w,"click",function(){f.openMenu&&w.hideMenu()})),t.forEach(function(e){if("string"==typeof e&&(e=f.options.exporting.menuItemDefinitions[e]),m(e,!0)){if(e.separator)var t=p("hr",null,null,S);else"viewData"===e.textKey&&f.isDataTableVisible&&(e.textKey="hideData"),t=p("li",{className:"highcharts-menu-item",onclick:function(t){t&&t.stopPropagation(),w.hideMenu(),e.onclick&&e.onclick.apply(f,arguments)},innerHTML:e.text||f.options.lang[e.textKey]},null,S),f.styledMode||(t.onmouseover=function(){c(this,x.menuItemHoverStyle)},t.onmouseout=function(){c(this,x.menuItemStyle)},c(t,d({cursor:"pointer"},x.menuItemStyle)));f.exportDivElements.push(t)}}),f.exportDivElements.push(S,w),f.exportMenuWidth=w.offsetWidth,f.exportMenuHeight=w.offsetHeight}t={display:"block"},n+f.exportMenuWidth>v?t.right=v-n-o-E+"px":t.left=n-E+"px",i+l+f.exportMenuHeight>y&&"top"!==h.alignOptions.verticalAlign?t.bottom=y-i-E+"px":t.top=i+l-E+"px",c(w,t),c(f.renderTo,{overflow:""}),f.openMenu=!0,g(f,"exportMenuShown")},addButton:function(e){var t=this,n=t.renderer,i=x(t.options.navigation.buttonOptions,e),o=i.onclick,r=i.menuItems,s=i.symbolSize||12;if(t.btnCount||(t.btnCount=0),t.exportDivElements||(t.exportDivElements=[],t.exportSVGElements=[]),!1!==i.enabled){var l,a=i.theme,u=a.states,c=u&&u.hover;u=u&&u.select,t.styledMode||(a.fill=y(a.fill,"#ffffff"),a.stroke=y(a.stroke,"none")),delete a.states,o?l=function(e){e&&e.stopPropagation(),o.call(t,e)}:r&&(l=function(e){e&&e.stopPropagation(),t.contextMenu(p.menuClassName,r,p.translateX,p.translateY,p.width,p.height,p),p.setState(2)}),i.text&&i.symbol?a.paddingLeft=y(a.paddingLeft,25):i.text||d(a,{width:i.width,height:i.height,padding:0}),t.styledMode||(a["stroke-linecap"]="round",a.fill=y(a.fill,"#ffffff"),a.stroke=y(a.stroke,"none"));var p=n.button(i.text,0,0,l,a,c,u).addClass(e.className).attr({title:y(t.options.lang[i._titleKey||i.titleKey],"")});if(p.menuClassName=e.menuClassName||"highcharts-menu-"+t.btnCount++,i.symbol){var h=n.symbol(i.symbol,i.symbolX-s/2,i.symbolY-s/2,s,s,{width:s,height:s}).addClass("highcharts-button-symbol").attr({zIndex:1}).add(p);t.styledMode||h.attr({stroke:i.symbolStroke,fill:i.symbolFill,"stroke-width":i.symbolStrokeWidth||1})}p.add(t.exportingGroup).align(d(i,{width:p.width,x:y(i.x,t.buttonOffset)}),!0,"spacingBox"),t.buttonOffset+=(p.width+i.buttonSpacing)*("right"===i.align?-1:1),t.exportSVGElements.push(p,h)}},destroyExport:function(e){var t=e?e.target:this;e=t.exportSVGElements;var n,i=t.exportDivElements,o=t.exportEvents;e&&(e.forEach(function(e,i){e&&(e.onclick=e.ontouchstart=null,n="cache-"+e.menuClassName,t[n]&&delete t[n],t.exportSVGElements[i]=e.destroy())}),e.length=0),t.exportingGroup&&(t.exportingGroup.destroy(),delete t.exportingGroup),i&&(i.forEach(function(e,n){r.clearTimeout(e.hideTimer),b(e,"mouseleave"),t.exportDivElements[n]=e.onmouseout=e.onmouseover=e.ontouchstart=e.onclick=null,h(e)}),i.length=0),o&&(o.forEach(function(e){e()}),o.length=0)}}),o.prototype.inlineToAttributes="fill stroke strokeLinecap strokeLinejoin strokeWidth textAnchor x y".split(" "),o.prototype.inlineBlacklist=[/-/,/^(clipPath|cssText|d|height|width)$/,/^font$/,/[lL]ogical(Width|Height)$/,/perspective/,/TapHighlightColor/,/^transition/,/^length$/],o.prototype.unstyledElements=["clipPath","defs","desc"],e.prototype.inlineStyles=function(){function e(e){return e.replace(/([A-Z])/g,function(e,t){return"-"+t.toLowerCase()})}var t,n=this.renderer,i=n.inlineToAttributes,o=n.inlineBlacklist,r=n.inlineWhitelist,l=n.unstyledElements,u={};n=s.createElement("iframe"),c(n,{width:"1px",height:"1px",visibility:"hidden"}),s.body.appendChild(n);var p=n.contentWindow.document;p.open(),p.write('<svg xmlns="http://www.w3.org/2000/svg"></svg>'),p.close(),function n(s){function c(t,n){if(h=d=!1,r){for(f=r.length;f--&&!d;)d=r[f].test(n);h=!d}for("transform"===n&&"none"===t&&(h=!0),f=o.length;f--&&!h;)h=o[f].test(n)||"function"==typeof t;h||y[n]===t&&"svg"!==s.nodeName||u[s.nodeName][n]===t||(i&&-1===i.indexOf(n)?g+=e(n)+":"+t+";":t&&s.setAttribute(e(n),t))}var h,d,f,g="";if(1===s.nodeType&&-1===l.indexOf(s.nodeName)){var m=a.getComputedStyle(s,null),y="svg"===s.nodeName?{}:a.getComputedStyle(s.parentNode,null);if(!u[s.nodeName]){t=p.getElementsByTagName("svg")[0];var b=p.createElementNS(s.namespaceURI,s.nodeName);t.appendChild(b),u[s.nodeName]=x(a.getComputedStyle(b,null)),"text"===s.nodeName&&delete u.text.fill,t.removeChild(b)}if(F||C)for(var w in m)c(m[w],w);else v(m,c);g&&(m=s.getAttribute("style"),s.setAttribute("style",(m?m+";":"")+g)),"svg"===s.nodeName&&s.setAttribute("stroke-width","1px"),"text"!==s.nodeName&&[].forEach.call(s.children||s.childNodes,n)}}(this.container.querySelector("svg")),t.parentNode.remove(),n.remove()},S.menu=function(e,t,n,i){return[["M",e,t+2.5],["L",e+n,t+2.5],["M",e,t+i/2+.5],["L",e+n,t+i/2+.5],["M",e,t+i-1.5],["L",e+n,t+i-1.5]]},S.menuball=function(e,t,n,i){return i=i/3-2,[].concat(this.circle(n-i,t,i,i),this.circle(n-i,t+i+4,i,i),this.circle(n-i,t+2*(i+4),i,i))},e.prototype.renderExporting=function(){var e=this,t=e.options.exporting,n=t.buttons,i=e.isDirtyExporting||!e.exportSVGElements;e.buttonOffset=0,e.isDirtyExporting&&e.destroyExport(),i&&!1!==t.enabled&&(e.exportEvents=[],e.exportingGroup=e.exportingGroup||e.renderer.g("exporting-group").attr({zIndex:3}).add(),v(n,function(t){e.addButton(t)}),e.isDirtyExporting=!1),u(e,"destroy",e.destroyExport)},u(e,"init",function(){var e=this;e.exporting={update:function(t,n){e.isDirtyExporting=!0,x(!0,e.options.exporting,t),y(n,!0)&&e.redraw()}},t.addUpdate(function(t,n){e.isDirtyExporting=!0,x(!0,e.options.navigation,t),y(n,!0)&&e.redraw()},e)}),e.prototype.callbacks.push(function(e){e.renderExporting(),u(e,"redraw",e.renderExporting)})}),t(e,"masters/modules/exporting.src.js",[],function(){})});