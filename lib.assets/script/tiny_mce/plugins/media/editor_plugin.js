!function(){function e(e){return"string"==typeof e?e.replace(/[^0-9%]/g,""):e}function t(e){var t,a;if(e&&!e.splice){for(t=[],a=0;!0&&e[a];a++)t[a]=e[a];return t}return e}var a,r,i=tinymce.explode("id,name,width,height,style,align,class,hspace,vspace,bgcolor,type"),s=tinymce.makeMap(i.join(",")),o=tinymce.html.Node,c=tinymce.util.JSON;a=[["Flash","d27cdb6e-ae6d-11cf-96b8-444553540000","application/x-shockwave-flash","http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"],["ShockWave","166b1bca-3f9c-11cf-8075-444553540000","application/x-director","http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0"],["WindowsMedia","6bf52a52-394a-11d3-b153-00c04f79faa6,22d6f312-b0f6-11d0-94ab-0080c74c7e95,05589fa1-c356-11ce-bf01-00aa0055595a","application/x-mplayer2","http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701"],["QuickTime","02bf25d5-8c17-4b23-bc80-d3488abddc6b","video/quicktime","http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0"],["RealMedia","cfcdaa03-8be4-11cf-b84b-0020afbbccfa","audio/x-pn-realaudio-plugin","http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"],["Java","8ad9c840-044e-11d1-b3e9-00805f499d93","application/x-java-applet","http://java.sun.com/products/plugin/autodl/jinstall-1_5_0-windows-i586.cab#Version=1,5,0,0"],["Silverlight","dfeaf541-f3e1-4c24-acac-99c30715084a","application/x-silverlight-2"],["Iframe"],["Video"],["EmbeddedAudio"],["Audio"]],tinymce.create("tinymce.plugins.MediaPlugin",{init:function(e,t){function s(t){return t&&"IMG"===t.nodeName&&e.dom.hasClass(t,"mceItemMedia")}var o,n,d,m,l=this,p={};for(l.editor=e,l.url=t,r="",o=0;o<a.length;o++){for(d={name:m=a[o][0],clsids:tinymce.explode(a[o][1]||""),mimes:tinymce.explode(a[o][2]||""),codebase:a[o][3]},n=0;n<d.clsids.length;n++)p["clsid:"+d.clsids[n]]=d;for(n=0;n<d.mimes.length;n++)p[d.mimes[n]]=d;p["mceItem"+m]=d,p[m.toLowerCase()]=d,r+=(r?"|":"")+m}tinymce.each(e.getParam("media_types","video=mp4,m4v,ogv,webm;silverlight=xap;flash=swf,flv;shockwave=dcr;quicktime=mov,qt,mpg,mpeg;shockwave=dcr;windowsmedia=avi,wmv,wm,asf,asx,wmx,wvx;realmedia=rm,ra,ram;java=jar;audio=mp3,ogg").split(";"),function(e){var t,a,r;for(e=e.split(/=/),a=tinymce.explode(e[1].toLowerCase()),t=0;t<a.length;t++)(r=p[e[0].toLowerCase()])&&(p[a[t]]=r)}),r=new RegExp("write("+r+")\\(([^)]+)\\)"),l.lookup=p,e.onPreInit.add(function(){e.schema.addValidElements("object[id|style|width|height|classid|codebase|*],param[name|value],embed[id|style|width|height|type|src|*],video[*],audio[*],source[*]"),e.parser.addNodeFilter("object,embed,video,audio,script,iframe",function(e){for(var t=e.length;t--;)l.objectToImg(e[t])}),e.serializer.addNodeFilter("img",function(e,t,a){for(var r,i=e.length;i--;)-1!==((r=e[i]).attr("class")||"").indexOf("mceItemMedia")&&l.imgToObject(r,a)})}),e.onInit.add(function(){e.theme&&e.theme.onResolveName&&e.theme.onResolveName.add(function(t,a){"img"===a.name&&e.dom.hasClass(a.node,"mceItemMedia")&&(a.name="media")}),e&&e.plugins.contextmenu&&e.plugins.contextmenu.onContextMenu.add(function(e,t,a){"IMG"===a.nodeName&&-1!==a.className.indexOf("mceItemMedia")&&t.add({title:"media.edit",icon:"media",cmd:"mceMedia"})})}),e.addCommand("mceMedia",function(){var a,r;s(r=e.selection.getNode())&&(a=e.dom.getAttrib(r,"data-mce-json"))&&(a=c.parse(a),tinymce.each(i,function(t){var i=e.dom.getAttrib(r,t);i&&(a[t]=i)}),a.type=l.getType(r.className).name.toLowerCase()),a||(a={type:"flash",video:{sources:[]},params:{}}),e.windowManager.open({file:t+"/media.htm",width:430+parseInt(e.getLang("media.delta_width",0)),height:500+parseInt(e.getLang("media.delta_height",0)),inline:1},{plugin_url:t,data:a})}),e.addButton("media",{title:"media.desc",cmd:"mceMedia"}),e.onNodeChange.add(function(e,t,a){t.setActive("media",s(a))}),e.onEvent.add(function(e,t){"mouseup"==t.type&&(el=e.selection.getNode(),"IMG"==el.nodeName&&-1!==el.className.indexOf("mceItemMedia")&&setTimeout(function(){l.processresize(e,el)},50))})},convertUrl:function(e,t){var a=this,r=a.editor,i=r.settings,s=i.url_converter,o=i.url_converter_scope||a;return e?t?r.documentBaseURI.toAbsolute(e):s.call(o,e,"src","object"):e},getInfo:function(){return{longname:"Media",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/media",version:tinymce.majorVersion+"."+tinymce.minorVersion}},processresize:function(e,t){var a=parseInt(e.dom.getAttrib(t,"width")||0),r=parseInt(e.dom.getAttrib(t,"height")||0);""!=(e.dom.getAttrib(t,"style")||"")&&0==a&&0==r&&(r=parseInt(t.style.height||0),a=parseInt(t.style.width||0));var i=e.dom.getAttrib(t,"data-mce-json")||"",s=c.parse(i,"'");s.video.attrs.width=a+"",s.video.attrs.height=r+"",i=c.serialize(s,"'"),t.setAttribute("data-mce-json",i)},dataToImg:function(a,r){var i,s,o,n,d=this;d.editor.documentBaseURI;if(a.params.src=d.convertUrl(a.params.src,r),(s=a.video.attrs)&&(s.src=d.convertUrl(s.src,r)),s&&(s.poster=d.convertUrl(s.poster,r)),i=t(a.video.sources))for(n=0;n<i.length;n++)i[n].src=d.convertUrl(i[n].src,r);return o=d.editor.dom.create("img",{id:a.id,style:a.style,align:a.align,hspace:a.hspace,vspace:a.vspace,src:d.editor.theme.url+"/img/trans.gif",class:"mceItemMedia mceItem"+d.getType(a.type).name,"data-mce-json":c.serialize(a,"'")}),o.width=a.width=e(a.width||("audio"==a.type?"300":"320")),o.height=a.height=e(a.height||("audio"==a.type?"32":"240")),o},dataToHtml:function(e,t){var a=this.editor.serializer.serialize(this.dataToImg(e,t),{forced_root_block:"",force_absolute:t});return console.log(a),a},htmlToData:function(e){var t,a,r;return r={type:"flash",video:{sources:[]},params:{}},t=this.editor.parser.parse(e),(a=t.getAll("img")[0])&&((r=c.parse(a.attr("data-mce-json"))).type=this.getType(a.attr("class")).name.toLowerCase(),tinymce.each(i,function(e){var t=a.attr(e);t&&(r[e]=t)})),r},getType:function(e){var t,a,r;for(a=tinymce.explode(e," "),t=0;t<a.length;t++)if(r=this.lookup[a[t]])return r},imgToObject:function(a,r){function s(e,t){var a,r,i,s,o;(o=M.getParam("flash_video_player_url",j.convertUrl(j.url+"/moxieplayer.swf")))&&(a=M.documentBaseURI,h.params.src=o,M.getParam("flash_video_player_absvideourl",!0)&&(e=a.toAbsolute(e||"",!0),t=a.toAbsolute(t||"",!0)),i="",r=M.getParam("flash_video_player_flashvars",{url:"$url",poster:"$poster"}),tinymce.each(r,function(a,r){(a=(a=a.replace(/\$url/,e||"")).replace(/\$poster/,t||"")).length>0&&(i+=(i?"&":"")+r+"="+escape(a))}),i.length&&(h.params.flashvars=i),s=M.getParam("flash_video_player_params",{allowfullscreen:!0,allowscriptaccess:!0}),tinymce.each(s,function(e,t){h.params[t]=""+e}))}var n,d,m,l,p,h,u,g,v,f,w,y,b,x,_,I,j=this,M=j.editor;if(h=a.attr("data-mce-json")){if(h=c.parse(h),f=this.getType(a.attr("class")),(_=a.attr("data-mce-style"))||(_=a.attr("style"))&&(_=M.dom.serializeStyle(M.dom.parseStyle(_,"img"))),h.width=a.attr("width")||h.width,h.height=a.attr("height")||h.height,"Iframe"===f.name){b=new o("iframe",1),tinymce.each(i,function(e){var t=a.attr(e);"class"==e&&t&&(t=t.replace(/mceItem.+ ?/g,"")),t&&t.length>0&&b.attr(e,t)});for(l in h.params)b.attr(l,h.params[l]);return b.attr({style:_,src:h.params.src}),void a.replace(b)}if(this.editor.settings.media_use_script)return b=new o("script",1).attr("type","text/javascript"),p=new o("#text",3),p.value="write"+f.name+"("+c.serialize(tinymce.extend(h.params,{width:a.attr("width"),height:a.attr("height")}))+");",b.append(p),void a.replace(b);if("Video"===f.name&&h.video.sources[0]){for(n=new o("video",1).attr(tinymce.extend({id:a.attr("id"),width:e(a.attr("width")),height:e(a.attr("height")),style:_},h.video.attrs)),h.video.attrs&&(x=h.video.attrs.poster),g=h.video.sources=t(h.video.sources),w=0;w<g.length;w++)/\.mp4$/.test(g[w].src)&&(y=g[w].src);for(g[0].type||(n.attr("src",g[0].src),g.splice(0,1)),w=0;w<g.length;w++)(u=new o("source",1).attr(g[w])).shortEnded=!0,n.append(u);y?(s(y,x),f=j.getType("flash")):h.params.src=""}if("Audio"===f.name&&h.video.sources[0]){for(I=new o("audio",1).attr(tinymce.extend({id:a.attr("id"),width:e(a.attr("width")),height:e(a.attr("height")),style:_},h.video.attrs)),h.video.attrs&&(x=h.video.attrs.poster),(g=h.video.sources=t(h.video.sources))[0].type||(I.attr("src",g[0].src),g.splice(0,1)),w=0;w<g.length;w++)(u=new o("source",1).attr(g[w])).shortEnded=!0,I.append(u);h.params.src=""}if("EmbeddedAudio"===f.name){(m=new o("embed",1)).shortEnded=!0,m.attr({id:a.attr("id"),width:e(a.attr("width")),height:e(a.attr("height")),style:_,type:a.attr("type")});for(l in h.params)m.attr(l,h.params[l]);tinymce.each(i,function(e){h[e]&&"type"!=e&&m.attr(e,h[e])}),h.params.src=""}if(h.params.src){/\.flv$/i.test(h.params.src)&&s(h.params.src,""),r&&r.force_absolute&&(h.params.src=M.documentBaseURI.toAbsolute(h.params.src)),d=new o("object",1).attr({id:a.attr("id"),width:e(a.attr("width")),height:e(a.attr("height")),style:_}),tinymce.each(i,function(e){var t=h[e];"class"==e&&t&&(t=t.replace(/mceItem.+ ?/g,"")),t&&"type"!=e&&d.attr(e,t)});for(l in h.params)(v=new o("param",1)).shortEnded=!0,p=h.params[l],"src"===l&&"WindowsMedia"===f.name&&(l="url"),v.attr({name:l,value:p}),d.append(v);if(this.editor.getParam("media_strict",!0))d.attr({data:h.params.src,type:f.mimes[0]});else{d.attr({classid:"clsid:"+f.clsids[0],codebase:f.codebase}),(m=new o("embed",1)).shortEnded=!0,m.attr({id:a.attr("id"),width:e(a.attr("width")),height:e(a.attr("height")),style:_,type:f.mimes[0]});for(l in h.params)m.attr(l,h.params[l]);tinymce.each(i,function(e){h[e]&&"type"!=e&&m.attr(e,h[e])}),d.append(m)}h.object_html&&((p=new o("#text",3)).raw=!0,p.value=h.object_html,d.append(p)),n&&n.append(d)}n&&h.video_html&&((p=new o("#text",3)).raw=!0,p.value=h.video_html,n.append(p)),I&&h.video_html&&((p=new o("#text",3)).raw=!0,p.value=h.video_html,I.append(p));var A=n||I||d||m;A?a.replace(A):a.remove()}},objectToImg:function(t){function a(e){return new tinymce.html.Serializer({inner:!0,validate:!1}).serialize(e)}function n(e,t){return P[(e.attr(t)||"").toLowerCase()]}var d,m,l,p,h,u,g,v,f,w,y,b,x,_,I,j,M,A,T,k,C,N,z,E,P=this.lookup,U=this.editor.settings.url_converter,L=this.editor.settings.url_converter_scope;if(t.parent){if("script"===t.name){if(t.firstChild&&(T=r.exec(t.firstChild.value)),!T)return;A=T[1],v=(M={video:{},params:c.parse(T[2])}).params.width,f=M.params.height}if(M=M||{video:{},params:{}},(h=new o("img",1)).attr({src:this.editor.theme.url+"/img/trans.gif"}),"video"===(u=t.name)||"audio"==u){l=t,d=t.getAll("object")[0],m=t.getAll("embed")[0],v=l.attr("width"),f=l.attr("height"),g=l.attr("id"),M.video={attrs:{},sources:[]},k=M.video.attrs;for(u in l.attributes.map)k[u]=l.attributes.map[u];for((I=t.attr("src"))&&M.video.sources.push({src:U.call(L,I,"src",t.name)}),j=l.getAll("source"),y=0;y<j.length;y++)I=j[y].remove(),M.video.sources.push({src:U.call(L,I.attr("src"),"src","source"),type:I.attr("type"),media:I.attr("media")});k.poster&&(k.poster=U.call(L,k.poster,"poster",t.name))}if("object"===t.name&&(d=t,m=t.getAll("embed")[0]),"embed"===t.name&&(m=t),"iframe"===t.name&&(p=t,A="Iframe"),d){for(v=v||d.attr("width"),f=f||d.attr("height"),w=w||d.attr("style"),g=g||d.attr("id"),C=C||d.attr("hspace"),N=N||d.attr("vspace"),z=z||d.attr("align"),E=E||d.attr("bgcolor"),M.name=d.attr("name"),_=d.getAll("param"),y=0;y<_.length;y++)u=(x=_[y]).remove().attr("name"),s[u]||(M.params[u]=x.attr("value"));M.params.src=M.params.src||d.attr("data")}if(m){v=v||m.attr("width"),f=f||m.attr("height"),w=w||m.attr("style"),g=g||m.attr("id"),C=C||m.attr("hspace"),N=N||m.attr("vspace"),z=z||m.attr("align"),E=E||m.attr("bgcolor");for(u in m.attributes.map)s[u]||M.params[u]||(M.params[u]=m.attributes.map[u])}if(p){v=e(p.attr("width")),f=e(p.attr("height")),w=w||p.attr("style"),g=p.attr("id"),C=p.attr("hspace"),N=p.attr("vspace"),z=p.attr("align"),E=p.attr("bgcolor"),tinymce.each(i,function(e){h.attr(e,p.attr(e))});for(u in p.attributes.map)s[u]||M.params[u]||(M.params[u]=p.attributes.map[u])}M.params.movie&&(M.params.src=M.params.src||M.params.movie,delete M.params.movie),M.params.src&&(M.params.src=U.call(L,M.params.src,"src","object")),l&&("video"===t.name?A=P.video.name:"audio"===t.name&&(A=P.audio.name)),d&&!A&&(A=(n(d,"clsid")||n(d,"classid")||n(d,"type")||{}).name),m&&!A&&(A=(n(m,"type")||function(e){var t=e.replace(/^.*\.([^.]+)$/,"$1");return P[t.toLowerCase()||""]}(M.params.src)||{}).name),m&&"EmbeddedAudio"==A&&(M.params.type=m.attr("type")),t.replace(h),m&&m.remove(),d&&(b=a(d.remove()))&&(M.object_html=b),l&&(b=a(l.remove()))&&(M.video_html=b),M.hspace=C,M.vspace=N,M.align=z,M.bgcolor=E,h.attr({id:g,class:"mceItemMedia mceItem"+(A||"Flash"),style:w,width:v||("audio"==t.name?"300":"320"),height:f||("audio"==t.name?"32":"240"),hspace:C,vspace:N,align:z,bgcolor:E,"data-mce-json":c.serialize(M,"'")})}}}),tinymce.PluginManager.add("media",tinymce.plugins.MediaPlugin)}();