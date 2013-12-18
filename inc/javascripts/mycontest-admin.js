jQuery(document).ready(function($) {
    
    // Show contest settings once it is loaded
    $('#mycontest-container, #mycontest-loading').slideToggle("slow")

    // Create the tabs
    $("#tabbed-nav").hgsTabs({
        theme: "red",
        style: "underlined",
        responsive: true
    });

    if( $('#sortable').children('li').size() > 0){ $('#no-entries').hide(); } else { $('#no-entries').show(); }

    $( "#sortable" ).sortable({ 
        handle: ".sortable-handle", 
        cursor: "move",
        update: function(){
            // console.log("Changed order");
        }
    });

    $( "#sortable .toolbar" ).disableSelection();

    $('#s_date, #e_date').datepicker({
        constrainInput: true
    });

    if($('#regvoteonly').children('input').prop('checked')){
      $('#regvoteonlyhidden').show();
    }else{
      $('#regvoteonlyhidden').hide();
    }

    if($('#socialshare').children('input').prop('checked')){
      $('#socialsharehidden').show();
    }else{
      $('#socialsharehidden').hide();
    }

    $('#socialshare').on("click", function(e){
      if($(this).children('input').prop('checked')){
        $('#socialsharehidden').slideDown();
      }else{
        $('#socialsharehidden').slideUp();
      }
    });

    //Setup the delete dialog
    $( "#dialog-confirm-entry" ).dialog({
          resizable: false,
          draggable: false,
          autoOpen: false,
          modal: true,
          closeOnEscape: true
    });

    // Delete entry
    $('.delete-entry').live("click", function() { 
        var entry = $(this).parents('.entry'),
        entryid = entry.attr('id'),
        detach = entry.detach()

        $('.undo-delete-entry').remove()
        $('.myContest-undo-delete-entry').data('undoData', detach).show()

        entriesAmount()

        return false;  
    });

    $('.myContest-undo-delete-entry').live("click", function() { 
      var u = $(this).data('undoData')
      u.appendTo('#sortable')
      $('.myContest-undo-delete-entry').hide().data('undoData', "")
      entriesAmount()
    });

    function entriesAmount(){
      if( $('#sortable').children('li').size() > 0){ $('#no-entries').hide() } else { $('#no-entries').show() }
    }

    // Add new entry at the end
     $('.myContest-add-new-entry').click(function() {
        $('#no-entries').hide();

        var starterEntryHTML = $('#starter-entry').html();
        starterEntryHTML = str_replace( '{changeStarterID}', uniqid(), starterEntryHTML );

        $('html, body').animate({ scrollTop: $( starterEntryHTML ).appendTo('#sortable').offset().top }, 1000 );
        return false;
    });

     // Select image
    $('.custom_media_upload_button').live("click", function() {
        var send_attachment_bkp = wp.media.editor.send.attachment;

        var cmuButtonThis = $(this);

        wp.media.editor.send.attachment = function(props, attachment) {
            //console.log(attachment);
            cmuButtonThis.parents(".img-tools").siblings(".custom_media_url").val(attachment.url);
            cmuButtonThis.parents(".img-tools").siblings(".custom_media_image").attr('src', attachment.url);

            wp.media.editor.send.attachment = send_attachment_bkp;
        }

        wp.media.editor.open();

        $('.media-button-insert').html('Select');

        return false;
    });

    // Remove image
    $('.custom_clear_image_button').live("click", function() {

      var defaultImage = $(this).parents('.img-tools').siblings('.custom_default_image').val();

      $(this).parents(".img-tools").siblings('.custom_media_url').val('');
      $(this).parents(".img-tools").siblings('.custom_media_image').attr('src', defaultImage);
      return false;

    });

    $('.thumbnail, .clear-selection, .media-menu-item').live("click", function(){
        $('.media-button-insert').html('Select');
    });

});

function str_replace (search, replace, subject, count) {
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp).split(f[j]).join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}

function uniqid (prefix, more_entropy) {
  if (typeof prefix == 'undefined') {
    prefix = "";
  }

  var retId;
  var formatSeed = function (seed, reqWidth) {
    seed = parseInt(seed, 10).toString(16); // to hex str
    if (reqWidth < seed.length) { // so long we split
      return seed.slice(seed.length - reqWidth);
    }
    if (reqWidth > seed.length) { // so short we pad
      return Array(1 + (reqWidth - seed.length)).join('0') + seed;
    }
    return seed;
  };

  // BEGIN REDUNDANT
  if (!this.php_js) {
    this.php_js = {};
  }
  // END REDUNDANT
  if (!this.php_js.uniqidSeed) { // init seed with big random int
    this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
  }
  this.php_js.uniqidSeed++;

  retId = prefix; // start with prefix, add current milliseconds hex string
  retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
  retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
  if (more_entropy) {
    // for more entropy we add a float lower to 10
    retId += (Math.random() * 10).toFixed(8).toString();
  }

  return retId;
}

/*! jQuery UI effects - v1.10.0 - 2013-01-22
* http://jqueryui.com
* Includes: jquery.ui.effect.js, jquery.ui.effect-blind.js, jquery.ui.effect-bounce.js, jquery.ui.effect-clip.js, jquery.ui.effect-drop.js, jquery.ui.effect-explode.js, jquery.ui.effect-fade.js, jquery.ui.effect-fold.js, jquery.ui.effect-highlight.js, jquery.ui.effect-pulsate.js, jquery.ui.effect-scale.js, jquery.ui.effect-shake.js, jquery.ui.effect-slide.js, jquery.ui.effect-transfer.js
* Copyright (c) 2013 jQuery Foundation and other contributors Licensed MIT */

jQuery.effects||function(e,t){var n="ui-effects-";e.effects={effect:{}},function(e,t){function h(e,t,n){var r=u[t.type]||{};return e==null?n||!t.def?null:t.def:(e=r.floor?~~e:parseFloat(e),isNaN(e)?t.def:r.mod?(e+r.mod)%r.mod:0>e?0:r.max<e?r.max:e)}function p(t){var n=s(),r=n._rgba=[];return t=t.toLowerCase(),c(i,function(e,i){var s,u=i.re.exec(t),a=u&&i.parse(u),f=i.space||"rgba";if(a)return s=n[f](a),n[o[f].cache]=s[o[f].cache],r=n._rgba=s._rgba,!1}),r.length?(r.join()==="0,0,0,0"&&e.extend(r,l.transparent),n):l[t]}function d(e,t,n){return n=(n+1)%1,n*6<1?e+(t-e)*n*6:n*2<1?t:n*3<2?e+(t-e)*(2/3-n)*6:e}var n="backgroundColor borderBottomColor borderLeftColor borderRightColor borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor",r=/^([\-+])=\s*(\d+\.?\d*)/,i=[{re:/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function(e){return[e[1],e[2],e[3],e[4]]}},{re:/rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function(e){return[e[1]*2.55,e[2]*2.55,e[3]*2.55,e[4]]}},{re:/#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,parse:function(e){return[parseInt(e[1],16),parseInt(e[2],16),parseInt(e[3],16)]}},{re:/#([a-f0-9])([a-f0-9])([a-f0-9])/,parse:function(e){return[parseInt(e[1]+e[1],16),parseInt(e[2]+e[2],16),parseInt(e[3]+e[3],16)]}},{re:/hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,space:"hsla",parse:function(e){return[e[1],e[2]/100,e[3]/100,e[4]]}}],s=e.Color=function(t,n,r,i){return new e.Color.fn.parse(t,n,r,i)},o={rgba:{props:{red:{idx:0,type:"byte"},green:{idx:1,type:"byte"},blue:{idx:2,type:"byte"}}},hsla:{props:{hue:{idx:0,type:"degrees"},saturation:{idx:1,type:"percent"},lightness:{idx:2,type:"percent"}}}},u={"byte":{floor:!0,max:255},percent:{max:1},degrees:{mod:360,floor:!0}},a=s.support={},f=e("<p>")[0],l,c=e.each;f.style.cssText="background-color:rgba(1,1,1,.5)",a.rgba=f.style.backgroundColor.indexOf("rgba")>-1,c(o,function(e,t){t.cache="_"+e,t.props.alpha={idx:3,type:"percent",def:1}}),s.fn=e.extend(s.prototype,{parse:function(n,r,i,u){if(n===t)return this._rgba=[null,null,null,null],this;if(n.jquery||n.nodeType)n=e(n).css(r),r=t;var a=this,f=e.type(n),d=this._rgba=[];r!==t&&(n=[n,r,i,u],f="array");if(f==="string")return this.parse(p(n)||l._default);if(f==="array")return c(o.rgba.props,function(e,t){d[t.idx]=h(n[t.idx],t)}),this;if(f==="object")return n instanceof s?c(o,function(e,t){n[t.cache]&&(a[t.cache]=n[t.cache].slice())}):c(o,function(t,r){var i=r.cache;c(r.props,function(e,t){if(!a[i]&&r.to){if(e==="alpha"||n[e]==null)return;a[i]=r.to(a._rgba)}a[i][t.idx]=h(n[e],t,!0)}),a[i]&&e.inArray(null,a[i].slice(0,3))<0&&(a[i][3]=1,r.from&&(a._rgba=r.from(a[i])))}),this},is:function(e){var t=s(e),n=!0,r=this;return c(o,function(e,i){var s,o=t[i.cache];return o&&(s=r[i.cache]||i.to&&i.to(r._rgba)||[],c(i.props,function(e,t){if(o[t.idx]!=null)return n=o[t.idx]===s[t.idx],n})),n}),n},_space:function(){var e=[],t=this;return c(o,function(n,r){t[r.cache]&&e.push(n)}),e.pop()},transition:function(e,t){var n=s(e),r=n._space(),i=o[r],a=this.alpha()===0?s("transparent"):this,f=a[i.cache]||i.to(a._rgba),l=f.slice();return n=n[i.cache],c(i.props,function(e,r){var i=r.idx,s=f[i],o=n[i],a=u[r.type]||{};if(o===null)return;s===null?l[i]=o:(a.mod&&(o-s>a.mod/2?s+=a.mod:s-o>a.mod/2&&(s-=a.mod)),l[i]=h((o-s)*t+s,r))}),this[r](l)},blend:function(t){if(this._rgba[3]===1)return this;var n=this._rgba.slice(),r=n.pop(),i=s(t)._rgba;return s(e.map(n,function(e,t){return(1-r)*i[t]+r*e}))},toRgbaString:function(){var t="rgba(",n=e.map(this._rgba,function(e,t){return e==null?t>2?1:0:e});return n[3]===1&&(n.pop(),t="rgb("),t+n.join()+")"},toHslaString:function(){var t="hsla(",n=e.map(this.hsla(),function(e,t){return e==null&&(e=t>2?1:0),t&&t<3&&(e=Math.round(e*100)+"%"),e});return n[3]===1&&(n.pop(),t="hsl("),t+n.join()+")"},toHexString:function(t){var n=this._rgba.slice(),r=n.pop();return t&&n.push(~~(r*255)),"#"+e.map(n,function(e){return e=(e||0).toString(16),e.length===1?"0"+e:e}).join("")},toString:function(){return this._rgba[3]===0?"transparent":this.toRgbaString()}}),s.fn.parse.prototype=s.fn,o.hsla.to=function(e){if(e[0]==null||e[1]==null||e[2]==null)return[null,null,null,e[3]];var t=e[0]/255,n=e[1]/255,r=e[2]/255,i=e[3],s=Math.max(t,n,r),o=Math.min(t,n,r),u=s-o,a=s+o,f=a*.5,l,c;return o===s?l=0:t===s?l=60*(n-r)/u+360:n===s?l=60*(r-t)/u+120:l=60*(t-n)/u+240,u===0?c=0:f<=.5?c=u/a:c=u/(2-a),[Math.round(l)%360,c,f,i==null?1:i]},o.hsla.from=function(e){if(e[0]==null||e[1]==null||e[2]==null)return[null,null,null,e[3]];var t=e[0]/360,n=e[1],r=e[2],i=e[3],s=r<=.5?r*(1+n):r+n-r*n,o=2*r-s;return[Math.round(d(o,s,t+1/3)*255),Math.round(d(o,s,t)*255),Math.round(d(o,s,t-1/3)*255),i]},c(o,function(n,i){var o=i.props,u=i.cache,a=i.to,f=i.from;s.fn[n]=function(n){a&&!this[u]&&(this[u]=a(this._rgba));if(n===t)return this[u].slice();var r,i=e.type(n),l=i==="array"||i==="object"?n:arguments,p=this[u].slice();return c(o,function(e,t){var n=l[i==="object"?e:t.idx];n==null&&(n=p[t.idx]),p[t.idx]=h(n,t)}),f?(r=s(f(p)),r[u]=p,r):s(p)},c(o,function(t,i){if(s.fn[t])return;s.fn[t]=function(s){var o=e.type(s),u=t==="alpha"?this._hsla?"hsla":"rgba":n,a=this[u](),f=a[i.idx],l;return o==="undefined"?f:(o==="function"&&(s=s.call(this,f),o=e.type(s)),s==null&&i.empty?this:(o==="string"&&(l=r.exec(s),l&&(s=f+parseFloat(l[2])*(l[1]==="+"?1:-1))),a[i.idx]=s,this[u](a)))}})}),s.hook=function(t){var n=t.split(" ");c(n,function(t,n){e.cssHooks[n]={set:function(t,r){var i,o,u="";if(r!=="transparent"&&(e.type(r)!=="string"||(i=p(r)))){r=s(i||r);if(!a.rgba&&r._rgba[3]!==1){o=n==="backgroundColor"?t.parentNode:t;while((u===""||u==="transparent")&&o&&o.style)try{u=e.css(o,"backgroundColor"),o=o.parentNode}catch(f){}r=r.blend(u&&u!=="transparent"?u:"_default")}r=r.toRgbaString()}try{t.style[n]=r}catch(f){}}},e.fx.step[n]=function(t){t.colorInit||(t.start=s(t.elem,n),t.end=s(t.end),t.colorInit=!0),e.cssHooks[n].set(t.elem,t.start.transition(t.end,t.pos))}})},s.hook(n),e.cssHooks.borderColor={expand:function(e){var t={};return c(["Top","Right","Bottom","Left"],function(n,r){t["border"+r+"Color"]=e}),t}},l=e.Color.names={aqua:"#00ffff",black:"#000000",blue:"#0000ff",fuchsia:"#ff00ff",gray:"#808080",green:"#008000",lime:"#00ff00",maroon:"#800000",navy:"#000080",olive:"#808000",purple:"#800080",red:"#ff0000",silver:"#c0c0c0",teal:"#008080",white:"#ffffff",yellow:"#ffff00",transparent:[null,null,null,0],_default:"#ffffff"}}(jQuery),function(){function i(t){var n,r,i=t.ownerDocument.defaultView?t.ownerDocument.defaultView.getComputedStyle(t,null):t.currentStyle,s={};if(i&&i.length&&i[0]&&i[i[0]]){r=i.length;while(r--)n=i[r],typeof i[n]=="string"&&(s[e.camelCase(n)]=i[n])}else for(n in i)typeof i[n]=="string"&&(s[n]=i[n]);return s}function s(t,n){var i={},s,o;for(s in n)o=n[s],t[s]!==o&&!r[s]&&(e.fx.step[s]||!isNaN(parseFloat(o)))&&(i[s]=o);return i}var n=["add","remove","toggle"],r={border:1,borderBottom:1,borderColor:1,borderLeft:1,borderRight:1,borderTop:1,borderWidth:1,margin:1,padding:1};e.each(["borderLeftStyle","borderRightStyle","borderBottomStyle","borderTopStyle"],function(t,n){e.fx.step[n]=function(e){if(e.end!=="none"&&!e.setAttr||e.pos===1&&!e.setAttr)jQuery.style(e.elem,n,e.end),e.setAttr=!0}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(e==null?this.prevObject:this.prevObject.filter(e))}),e.effects.animateClass=function(t,r,o,u){var a=e.speed(r,o,u);return this.queue(function(){var r=e(this),o=r.attr("class")||"",u,f=a.children?r.find("*").addBack():r;f=f.map(function(){var t=e(this);return{el:t,start:i(this)}}),u=function(){e.each(n,function(e,n){t[n]&&r[n+"Class"](t[n])})},u(),f=f.map(function(){return this.end=i(this.el[0]),this.diff=s(this.start,this.end),this}),r.attr("class",o),f=f.map(function(){var t=this,n=e.Deferred(),r=e.extend({},a,{queue:!1,complete:function(){n.resolve(t)}});return this.el.animate(this.diff,r),n.promise()}),e.when.apply(e,f.get()).done(function(){u(),e.each(arguments,function(){var t=this.el;e.each(this.diff,function(e){t.css(e,"")})}),a.complete.call(r[0])})})},e.fn.extend({_addClass:e.fn.addClass,addClass:function(t,n,r,i){return n?e.effects.animateClass.call(this,{add:t},n,r,i):this._addClass(t)},_removeClass:e.fn.removeClass,removeClass:function(t,n,r,i){return n?e.effects.animateClass.call(this,{remove:t},n,r,i):this._removeClass(t)},_toggleClass:e.fn.toggleClass,toggleClass:function(n,r,i,s,o){return typeof r=="boolean"||r===t?i?e.effects.animateClass.call(this,r?{add:n}:{remove:n},i,s,o):this._toggleClass(n,r):e.effects.animateClass.call(this,{toggle:n},r,i,s)},switchClass:function(t,n,r,i,s){return e.effects.animateClass.call(this,{add:n,remove:t},r,i,s)}})}(),function(){function r(t,n,r,i){e.isPlainObject(t)&&(n=t,t=t.effect),t={effect:t},n==null&&(n={}),e.isFunction(n)&&(i=n,r=null,n={});if(typeof n=="number"||e.fx.speeds[n])i=r,r=n,n={};return e.isFunction(r)&&(i=r,r=null),n&&e.extend(t,n),r=r||n.duration,t.duration=e.fx.off?0:typeof r=="number"?r:r in e.fx.speeds?e.fx.speeds[r]:e.fx.speeds._default,t.complete=i||n.complete,t}function i(t){return!t||typeof t=="number"||e.fx.speeds[t]?!0:typeof t=="string"&&!e.effects.effect[t]}e.extend(e.effects,{version:"1.10.0",save:function(e,t){for(var r=0;r<t.length;r++)t[r]!==null&&e.data(n+t[r],e[0].style[t[r]])},restore:function(e,r){var i,s;for(s=0;s<r.length;s++)r[s]!==null&&(i=e.data(n+r[s]),i===t&&(i=""),e.css(r[s],i))},setMode:function(e,t){return t==="toggle"&&(t=e.is(":hidden")?"show":"hide"),t},getBaseline:function(e,t){var n,r;switch(e[0]){case"top":n=0;break;case"middle":n=.5;break;case"bottom":n=1;break;default:n=e[0]/t.height}switch(e[1]){case"left":r=0;break;case"center":r=.5;break;case"right":r=1;break;default:r=e[1]/t.width}return{x:r,y:n}},createWrapper:function(t){if(t.parent().is(".ui-effects-wrapper"))return t.parent();var n={width:t.outerWidth(!0),height:t.outerHeight(!0),"float":t.css("float")},r=e("<div></div>").addClass("ui-effects-wrapper").css({fontSize:"100%",background:"transparent",border:"none",margin:0,padding:0}),i={width:t.width(),height:t.height()},s=document.activeElement;try{s.id}catch(o){s=document.body}return t.wrap(r),(t[0]===s||e.contains(t[0],s))&&e(s).focus(),r=t.parent(),t.css("position")==="static"?(r.css({position:"relative"}),t.css({position:"relative"})):(e.extend(n,{position:t.css("position"),zIndex:t.css("my-index")}),e.each(["top","left","bottom","right"],function(e,r){n[r]=t.css(r),isNaN(parseInt(n[r],10))&&(n[r]="auto")}),t.css({position:"relative",top:0,left:0,right:"auto",bottom:"auto"})),t.css(i),r.css(n).show()},removeWrapper:function(t){var n=document.activeElement;return t.parent().is(".ui-effects-wrapper")&&(t.parent().replaceWith(t),(t[0]===n||e.contains(t[0],n))&&e(n).focus()),t},setTransition:function(t,n,r,i){return i=i||{},e.each(n,function(e,n){var s=t.cssUnit(n);s[0]>0&&(i[n]=s[0]*r+s[1])}),i}}),e.fn.extend({effect:function(){function o(n){function u(){e.isFunction(i)&&i.call(r[0]),e.isFunction(n)&&n()}var r=e(this),i=t.complete,o=t.mode;(r.is(":hidden")?o==="hide":o==="show")?u():s.call(r[0],t,u)}var t=r.apply(this,arguments),n=t.mode,i=t.queue,s=e.effects.effect[t.effect];return e.fx.off||!s?n?this[n](t.duration,t.complete):this.each(function(){t.complete&&t.complete.call(this)}):i===!1?this.each(o):this.queue(i||"fx",o)},_show:e.fn.show,show:function(e){if(i(e))return this._show.apply(this,arguments);var t=r.apply(this,arguments);return t.mode="show",this.effect.call(this,t)},_hide:e.fn.hide,hide:function(e){if(i(e))return this._hide.apply(this,arguments);var t=r.apply(this,arguments);return t.mode="hide",this.effect.call(this,t)},__toggle:e.fn.toggle,toggle:function(t){if(i(t)||typeof t=="boolean"||e.isFunction(t))return this.__toggle.apply(this,arguments);var n=r.apply(this,arguments);return n.mode="toggle",this.effect.call(this,n)},cssUnit:function(t){var n=this.css(t),r=[];return e.each(["em","px","%","pt"],function(e,t){n.indexOf(t)>0&&(r=[parseFloat(n),t])}),r}})}(),function(){var t={};e.each(["Quad","Cubic","Quart","Quint","Expo"],function(e,n){t[n]=function(t){return Math.pow(t,e+2)}}),e.extend(t,{Sine:function(e){return 1-Math.cos(e*Math.PI/2)},Circ:function(e){return 1-Math.sqrt(1-e*e)},Elastic:function(e){return e===0||e===1?e:-Math.pow(2,8*(e-1))*Math.sin(((e-1)*80-7.5)*Math.PI/15)},Back:function(e){return e*e*(3*e-2)},Bounce:function(e){var t,n=4;while(e<((t=Math.pow(2,--n))-1)/11);return 1/Math.pow(4,3-n)-7.5625*Math.pow((t*3-2)/22-e,2)}}),e.each(t,function(t,n){e.easing["easeIn"+t]=n,e.easing["easeOut"+t]=function(e){return 1-n(1-e)},e.easing["easeInOut"+t]=function(e){return e<.5?n(e*2)/2:1-n(e*-2+2)/2}})}()}(jQuery);(function(e,t){var n=/up|down|vertical/,r=/up|left|vertical|horizontal/;e.effects.effect.blind=function(t,i){var s=e(this),o=["position","top","bottom","left","right","height","width"],u=e.effects.setMode(s,t.mode||"hide"),a=t.direction||"up",f=n.test(a),l=f?"height":"width",c=f?"top":"left",h=r.test(a),p={},d=u==="show",v,m,g;s.parent().is(".ui-effects-wrapper")?e.effects.save(s.parent(),o):e.effects.save(s,o),s.show(),v=e.effects.createWrapper(s).css({overflow:"hidden"}),m=v[l](),g=parseFloat(v.css(c))||0,p[l]=d?m:0,h||(s.css(f?"bottom":"right",0).css(f?"top":"left","auto").css({position:"absolute"}),p[c]=d?g:m+g),d&&(v.css(l,0),h||v.css(c,g+m)),v.animate(p,{duration:t.duration,easing:t.easing,queue:!1,complete:function(){u==="hide"&&s.hide(),e.effects.restore(s,o),e.effects.removeWrapper(s),i()}})}})(jQuery);(function(e,t){e.effects.effect.bounce=function(t,n){var r=e(this),i=["position","top","bottom","left","right","height","width"],s=e.effects.setMode(r,t.mode||"effect"),o=s==="hide",u=s==="show",a=t.direction||"up",f=t.distance,l=t.times||5,c=l*2+(u||o?1:0),h=t.duration/c,p=t.easing,d=a==="up"||a==="down"?"top":"left",v=a==="up"||a==="left",m,g,y,b=r.queue(),w=b.length;(u||o)&&i.push("opacity"),e.effects.save(r,i),r.show(),e.effects.createWrapper(r),f||(f=r[d==="top"?"outerHeight":"outerWidth"]()/3),u&&(y={opacity:1},y[d]=0,r.css("opacity",0).css(d,v?-f*2:f*2).animate(y,h,p)),o&&(f/=Math.pow(2,l-1)),y={},y[d]=0;for(m=0;m<l;m++)g={},g[d]=(v?"-=":"+=")+f,r.animate(g,h,p).animate(y,h,p),f=o?f*2:f/2;o&&(g={opacity:0},g[d]=(v?"-=":"+=")+f,r.animate(g,h,p)),r.queue(function(){o&&r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()}),w>1&&b.splice.apply(b,[1,0].concat(b.splice(w,c+1))),r.dequeue()}})(jQuery);(function(e,t){e.effects.effect.clip=function(t,n){var r=e(this),i=["position","top","bottom","left","right","height","width"],s=e.effects.setMode(r,t.mode||"hide"),o=s==="show",u=t.direction||"vertical",a=u==="vertical",f=a?"height":"width",l=a?"top":"left",c={},h,p,d;e.effects.save(r,i),r.show(),h=e.effects.createWrapper(r).css({overflow:"hidden"}),p=r[0].tagName==="IMG"?h:r,d=p[f](),o&&(p.css(f,0),p.css(l,d/2)),c[f]=o?d:0,c[l]=o?0:d/2,p.animate(c,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){o||r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()}})}})(jQuery);(function(e,t){e.effects.effect.drop=function(t,n){var r=e(this),i=["position","top","bottom","left","right","opacity","height","width"],s=e.effects.setMode(r,t.mode||"hide"),o=s==="show",u=t.direction||"left",a=u==="up"||u==="down"?"top":"left",f=u==="up"||u==="left"?"pos":"neg",l={opacity:o?1:0},c;e.effects.save(r,i),r.show(),e.effects.createWrapper(r),c=t.distance||r[a==="top"?"outerHeight":"outerWidth"](!0)/2,o&&r.css("opacity",0).css(a,f==="pos"?-c:c),l[a]=(o?f==="pos"?"+=":"-=":f==="pos"?"-=":"+=")+c,r.animate(l,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){s==="hide"&&r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()}})}})(jQuery);(function(e,t){e.effects.effect.explode=function(t,n){function y(){c.push(this),c.length===r*i&&b()}function b(){s.css({visibility:"visible"}),e(c).remove(),u||s.hide(),n()}var r=t.pieces?Math.round(Math.sqrt(t.pieces)):3,i=r,s=e(this),o=e.effects.setMode(s,t.mode||"hide"),u=o==="show",a=s.show().css("visibility","hidden").offset(),f=Math.ceil(s.outerWidth()/i),l=Math.ceil(s.outerHeight()/r),c=[],h,p,d,v,m,g;for(h=0;h<r;h++){v=a.top+h*l,g=h-(r-1)/2;for(p=0;p<i;p++)d=a.left+p*f,m=p-(i-1)/2,s.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-p*f,top:-h*l}).parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:f,height:l,left:d+(u?m*f:0),top:v+(u?g*l:0),opacity:u?0:1}).animate({left:d+(u?0:m*f),top:v+(u?0:g*l),opacity:u?1:0},t.duration||500,t.easing,y)}}})(jQuery);(function(e,t){e.effects.effect.fade=function(t,n){var r=e(this),i=e.effects.setMode(r,t.mode||"toggle");r.animate({opacity:i},{queue:!1,duration:t.duration,easing:t.easing,complete:n})}})(jQuery);(function(e,t){e.effects.effect.fold=function(t,n){var r=e(this),i=["position","top","bottom","left","right","height","width"],s=e.effects.setMode(r,t.mode||"hide"),o=s==="show",u=s==="hide",a=t.size||15,f=/([0-9]+)%/.exec(a),l=!!t.horizFirst,c=o!==l,h=c?["width","height"]:["height","width"],p=t.duration/2,d,v,m={},g={};e.effects.save(r,i),r.show(),d=e.effects.createWrapper(r).css({overflow:"hidden"}),v=c?[d.width(),d.height()]:[d.height(),d.width()],f&&(a=parseInt(f[1],10)/100*v[u?0:1]),o&&d.css(l?{height:0,width:a}:{height:a,width:0}),m[h[0]]=o?v[0]:a,g[h[1]]=o?v[1]:0,d.animate(m,p,t.easing).animate(g,p,t.easing,function(){u&&r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()})}})(jQuery);(function(e,t){e.effects.effect.highlight=function(t,n){var r=e(this),i=["backgroundImage","backgroundColor","opacity"],s=e.effects.setMode(r,t.mode||"show"),o={backgroundColor:r.css("backgroundColor")};s==="hide"&&(o.opacity=0),e.effects.save(r,i),r.show().css({backgroundImage:"none",backgroundColor:t.color||"#ffff99"}).animate(o,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){s==="hide"&&r.hide(),e.effects.restore(r,i),n()}})}})(jQuery);(function(e,t){e.effects.effect.pulsate=function(t,n){var r=e(this),i=e.effects.setMode(r,t.mode||"show"),s=i==="show",o=i==="hide",u=s||i==="hide",a=(t.times||5)*2+(u?1:0),f=t.duration/a,l=0,c=r.queue(),h=c.length,p;if(s||!r.is(":visible"))r.css("opacity",0).show(),l=1;for(p=1;p<a;p++)r.animate({opacity:l},f,t.easing),l=1-l;r.animate({opacity:l},f,t.easing),r.queue(function(){o&&r.hide(),n()}),h>1&&c.splice.apply(c,[1,0].concat(c.splice(h,a+1))),r.dequeue()}})(jQuery);(function(e,t){e.effects.effect.puff=function(t,n){var r=e(this),i=e.effects.setMode(r,t.mode||"hide"),s=i==="hide",o=parseInt(t.percent,10)||150,u=o/100,a={height:r.height(),width:r.width(),outerHeight:r.outerHeight(),outerWidth:r.outerWidth()};e.extend(t,{effect:"scale",queue:!1,fade:!0,mode:i,complete:n,percent:s?o:100,from:s?a:{height:a.height*u,width:a.width*u,outerHeight:a.outerHeight*u,outerWidth:a.outerWidth*u}}),r.effect(t)},e.effects.effect.scale=function(t,n){var r=e(this),i=e.extend(!0,{},t),s=e.effects.setMode(r,t.mode||"effect"),o=parseInt(t.percent,10)||(parseInt(t.percent,10)===0?0:s==="hide"?0:100),u=t.direction||"both",a=t.origin,f={height:r.height(),width:r.width(),outerHeight:r.outerHeight(),outerWidth:r.outerWidth()},l={y:u!=="horizontal"?o/100:1,x:u!=="vertical"?o/100:1};i.effect="size",i.queue=!1,i.complete=n,s!=="effect"&&(i.origin=a||["middle","center"],i.restore=!0),i.from=t.from||(s==="show"?{height:0,width:0,outerHeight:0,outerWidth:0}:f),i.to={height:f.height*l.y,width:f.width*l.x,outerHeight:f.outerHeight*l.y,outerWidth:f.outerWidth*l.x},i.fade&&(s==="show"&&(i.from.opacity=0,i.to.opacity=1),s==="hide"&&(i.from.opacity=1,i.to.opacity=0)),r.effect(i)},e.effects.effect.size=function(t,n){var r,i,s,o=e(this),u=["position","top","bottom","left","right","width","height","overflow","opacity"],a=["position","top","bottom","left","right","overflow","opacity"],f=["width","height","overflow"],l=["fontSize"],c=["borderTopWidth","borderBottomWidth","paddingTop","paddingBottom"],h=["borderLeftWidth","borderRightWidth","paddingLeft","paddingRight"],p=e.effects.setMode(o,t.mode||"effect"),d=t.restore||p!=="effect",v=t.scale||"both",m=t.origin||["middle","center"],g=o.css("position"),y=d?u:a,b={height:0,width:0,outerHeight:0,outerWidth:0};p==="show"&&o.show(),r={height:o.height(),width:o.width(),outerHeight:o.outerHeight(),outerWidth:o.outerWidth()},t.mode==="toggle"&&p==="show"?(o.from=t.to||b,o.to=t.from||r):(o.from=t.from||(p==="show"?b:r),o.to=t.to||(p==="hide"?b:r)),s={from:{y:o.from.height/r.height,x:o.from.width/r.width},to:{y:o.to.height/r.height,x:o.to.width/r.width}};if(v==="box"||v==="both")s.from.y!==s.to.y&&(y=y.concat(c),o.from=e.effects.setTransition(o,c,s.from.y,o.from),o.to=e.effects.setTransition(o,c,s.to.y,o.to)),s.from.x!==s.to.x&&(y=y.concat(h),o.from=e.effects.setTransition(o,h,s.from.x,o.from),o.to=e.effects.setTransition(o,h,s.to.x,o.to));(v==="content"||v==="both")&&s.from.y!==s.to.y&&(y=y.concat(l).concat(f),o.from=e.effects.setTransition(o,l,s.from.y,o.from),o.to=e.effects.setTransition(o,l,s.to.y,o.to)),e.effects.save(o,y),o.show(),e.effects.createWrapper(o),o.css("overflow","hidden").css(o.from),m&&(i=e.effects.getBaseline(m,r),o.from.top=(r.outerHeight-o.outerHeight())*i.y,o.from.left=(r.outerWidth-o.outerWidth())*i.x,o.to.top=(r.outerHeight-o.to.outerHeight)*i.y,o.to.left=(r.outerWidth-o.to.outerWidth)*i.x),o.css(o.from);if(v==="content"||v==="both")c=c.concat(["marginTop","marginBottom"]).concat(l),h=h.concat(["marginLeft","marginRight"]),f=u.concat(c).concat(h),o.find("*[width]").each(function(){var n=e(this),r={height:n.height(),width:n.width(),outerHeight:n.outerHeight(),outerWidth:n.outerWidth()};d&&e.effects.save(n,f),n.from={height:r.height*s.from.y,width:r.width*s.from.x,outerHeight:r.outerHeight*s.from.y,outerWidth:r.outerWidth*s.from.x},n.to={height:r.height*s.to.y,width:r.width*s.to.x,outerHeight:r.height*s.to.y,outerWidth:r.width*s.to.x},s.from.y!==s.to.y&&(n.from=e.effects.setTransition(n,c,s.from.y,n.from),n.to=e.effects.setTransition(n,c,s.to.y,n.to)),s.from.x!==s.to.x&&(n.from=e.effects.setTransition(n,h,s.from.x,n.from),n.to=e.effects.setTransition(n,h,s.to.x,n.to)),n.css(n.from),n.animate(n.to,t.duration,t.easing,function(){d&&e.effects.restore(n,f)})});o.animate(o.to,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){o.to.opacity===0&&o.css("opacity",o.from.opacity),p==="hide"&&o.hide(),e.effects.restore(o,y),d||(g==="static"?o.css({position:"relative",top:o.to.top,left:o.to.left}):e.each(["top","left"],function(e,t){o.css(t,function(t,n){var r=parseInt(n,10),i=e?o.to.left:o.to.top;return n==="auto"?i+"px":r+i+"px"})})),e.effects.removeWrapper(o),n()}})}})(jQuery);(function(e,t){e.effects.effect.shake=function(t,n){var r=e(this),i=["position","top","bottom","left","right","height","width"],s=e.effects.setMode(r,t.mode||"effect"),o=t.direction||"left",u=t.distance||20,a=t.times||3,f=a*2+1,l=Math.round(t.duration/f),c=o==="up"||o==="down"?"top":"left",h=o==="up"||o==="left",p={},d={},v={},m,g=r.queue(),y=g.length;e.effects.save(r,i),r.show(),e.effects.createWrapper(r),p[c]=(h?"-=":"+=")+u,d[c]=(h?"+=":"-=")+u*2,v[c]=(h?"-=":"+=")+u*2,r.animate(p,l,t.easing);for(m=1;m<a;m++)r.animate(d,l,t.easing).animate(v,l,t.easing);r.animate(d,l,t.easing).animate(p,l/2,t.easing).queue(function(){s==="hide"&&r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()}),y>1&&g.splice.apply(g,[1,0].concat(g.splice(y,f+1))),r.dequeue()}})(jQuery);(function(e,t){e.effects.effect.slide=function(t,n){var r=e(this),i=["position","top","bottom","left","right","width","height"],s=e.effects.setMode(r,t.mode||"show"),o=s==="show",u=t.direction||"left",a=u==="up"||u==="down"?"top":"left",f=u==="up"||u==="left",l,c={};e.effects.save(r,i),r.show(),l=t.distance||r[a==="top"?"outerHeight":"outerWidth"](!0),e.effects.createWrapper(r).css({overflow:"hidden"}),o&&r.css(a,f?isNaN(l)?"-"+l:-l:l),c[a]=(o?f?"+=":"-=":f?"-=":"+=")+l,r.animate(c,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){s==="hide"&&r.hide(),e.effects.restore(r,i),e.effects.removeWrapper(r),n()}})}})(jQuery);(function(e,t){e.effects.effect.transfer=function(t,n){var r=e(this),i=e(t.to),s=i.css("position")==="fixed",o=e("body"),u=s?o.scrollTop():0,a=s?o.scrollLeft():0,f=i.offset(),l={top:f.top-u,left:f.left-a,height:i.innerHeight(),width:i.innerWidth()},c=r.offset(),h=e("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(t.className).css({top:c.top-u,left:c.left-a,height:r.innerHeight(),width:r.innerWidth(),position:s?"fixed":"absolute"}).animate(l,t.duration,t.easing,function(){h.remove(),n()})}})(jQuery);

/* 
* @name         myTabs
* @descripton   tabbed content area
* @version      1.6.2
* @Copyright    Copyright (c) 2013 Higher Ground Studio
*/
(function ($, window, document, undefined) {
    if (!window.console) window.console = {};
    if (!window.console.log) window.console.log = function () { };

    $.fn.extend({
        hasClasses: function (selectors) {
            var _base = this;
            for (i in selectors) {
                if ($(_base).hasClass(selectors[i]))
                    return true;
            }
            return false;
        }
    });

    $.hgs = {};
    $.hgs.core = {};
    // $.hgs.core.console = {
    //     log: function (message) {
    //         if ($("#console").length != 0) {
    //             $("<div/>")
    //             .css({ marginTop: -24 })
    //             .html(message)
    //             .prependTo("#console")
    //             .animate({ marginTop: 0 }, 300)
    //             .animate({ backgroundColor: "#ffffff" }, 800);
    //         }
    //         else {
    //             if (console) {
    //                 console.log(message);
    //             }
    //         }
    //     }
    // };

    $.hgs.core.keyCodes = {
        tab: 9,
        enter: 13,
        esc: 27,

        space: 32,
        pageup: 33,
        pagedown: 34,
        end: 35,
        home: 36,

        left: 37,
        up: 38,
        right: 39,
        down: 40
    };

    $.hgs.core.debug = {
        startTime: new Date(),
        log: function (msg) {
            if (console) {
                console.log(msg);
            }
        },
        start: function () {
            this.startTime = +new Date();
            this.log("start: " + this.startTime);
        },
        stop: function () {
            var _end = +new Date();
            var _diff = _end - this.startTime;

            this.log("end: " + _end);
            this.log("diff: " + _diff);

            var Seconds_from_T1_to_T2 = _diff / 1000;
            var Seconds_Between_Dates = Math.abs(Seconds_from_T1_to_T2);

            //this.log("diff s: " + Seconds_Between_Dates);
        }
    };

    $.hgs.core.plugins = {
        easing: function (_base) {
            var _exist = false;
            if (_base) {
                if (_base.settings) {
                    //set up a default value for easing
                    var _defEasing = 'swing';

                    // check for the existence of the easing plugin
                    if ($.easing.def) {
                        _exist = true;
                    }
                    else {
                        if (_base.settings.animation.easing != 'swing' && _base.settings.animation.easing != 'linear') {
                            _base.settings.animation.easing = _defEasing;
                        }
                    }
                }
            }
            return _exist;
        }
    };

    $.hgs.core.browser = {
        init: function () {
            this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
            this.version = this.searchVersion(navigator.userAgent)
                           || this.searchVersion(navigator.appVersion)
                           || "an unknown version";

            //$.hgs.core.console.log("init: " + this.browser + " : " + this.version);
            if (this.browser === "Explorer") {

                var _el = $("html");
                var version = parseInt(this.version);

                if (version === 6) {
                    _el.addClass("ie ie7");
                }
                else if (version === 7) {
                    _el.addClass("ie ie7");
                }
                else if (version === 8) {
                    _el.addClass("ie ie8");
                }
                else if (version === 9) {
                    _el.addClass("ie ie9");
                }
            }
        },
        searchString: function (data) {
            for (var i = 0; i < data.length; i++) {
                var dataString = data[i].string;
                var dataProp = data[i].prop;
                this.versionSearchString = data[i].versionSearch || data[i].identity;
                if (dataString) {
                    if (dataString.indexOf(data[i].subString) != -1)
                        return data[i].identity;
                }
                else if (dataProp)
                    return data[i].identity;
            }
        },
        searchVersion: function (dataString) {
            var index = dataString.indexOf(this.versionSearchString);
            if (index == -1)
                return;
            return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
        },
        dataBrowser: [
            {
                string: navigator.userAgent,
                subString: "Chrome",
                identity: "Chrome"
            }, {
                string: navigator.vendor,
                subString: "Apple",
                identity: "Safari",
                versionSearch: "Version"
            }, {
                prop: window.opera,
                identity: "Opera"
            }, {
                string: navigator.userAgent,
                subString: "Firefox",
                identity: "Firefox"
            }, {
                string: navigator.userAgent,
                subString: "MSIE",
                identity: "Explorer",
                versionSearch: "MSIE"
            }
        ]
    };

    $.hgs.core.hashHelper = {
        all: function () {
            var hashArray = [];
            var hash = document.location.hash;

            if (!this.hasHash()) {
                return hashArray;
            }

            hash = hash.substring(1).split('&');

            for (var i = 0; i < hash.length; i++) {
                var match = hash[i].split('=');
                //if (match.length != 2 || match[0] in hashArray) return undefined;
                if (match.length != 2 || match[0] in hashArray) {
                    match[1] = "none";
                }
                hashArray[match[0]] = match[1];
            }

            return hashArray;
        },
        get: function (key) {
            var all = this.all();

            if (typeof all === 'undefined' || typeof all.length < 0) {
                //self.log("get: undefined or null all");
                return null;
            }
            else {
                if (typeof all[key] !== 'undefined' && all[key] !== null) {
                    //self.log("get: exist key");
                    return all[key];
                }
                else {
                    //self.log("get: undefined or null key" + key);
                    return null;
                }
            }

        },
        set: function (key, value) {
            var all = this.all();
            var hash = [];

            all[key] = value;
            for (var key in all) {
                hash.push(key + '=' + all[key]);
            }
            document.location.hash = hash.join('&');
        },
        hasHash: function () {
            var hash = document.location.hash;
            if (hash.length > 0) {
                return true;
            }
            else {
                return false;
            }
        }
    };



    $.hgs.core.browser.init();

})(jQuery, window, document);


;(function ($, window, document, undefined) {    
    if (window.hgs == null) {
        window.hgs = {};
    }
    var HgsTabs = function (elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options;
        this.metadata = (this.$elem.data("options")) ? this.$elem.data("options") : {};
        this.attrdata = (this.$elem.data()) ? this.$elem.data() : {};
        this.tabID;
        this.$tabGroup;
        this.$tabs;
        this.$container;
        this.$contents;
        this.autoplayIntervalId;
        this.currentTab;
        this.BrowserDetection = $.hgs.core.browser;
        this.Hash = $.hgs.core.hashHelper;
    };

    var hgs = {
        pluginName: "hgsTabs",
        elementSpacer: "<span class='my-tab-spacer' style='clear: both;display: block;'></span>",        
        commaRegExp: /,/g,
        space: " ",
        classes: {
            prefix: "my-",
            wrapper: "my-tabs",
            tabGroup: "my-tabs-nav",
            tab: "my-tab",
            first: "my-first",
            last: "my-last",
            active: "my-active",
            link: "my-link",
            container: "my-container",
            content: "my-content",
            shadows: "my-shadows",
            rounded: "my-rounded",
            themes: {
                gray: "gray",
                black: "black",
                blue: "blue",
                crystal: "crystal",
                green: "green",
                silver: "silver",
                red: "red",
                orange: "orange",
                deepblue: "deepblue",
                white: "white"
            },
            styles: {
                normal: "normal",
                underlined: "underlined",
                simple: "simple"
            },
            orientations: {
                vertical: "vertical",
                horizontal: "horizontal"
            },
            sizes: {
                mini: "mini",
                small: "small",
                medium: "medium",
                large: "large",
                xlarge: "xlarge",
                xxlarge: "xxlarge"
            },
            positions: {
                topLeft: "top-left",
                topCenter: "top-center",
                topRight: "top-right",
                topCompact: "top-compact",
                bottomLeft: "bottom-left",
                bottomCenter: "bottom-center",
                bottomRight: "bottom-right",
                bottomCompact: "bottom-compact"
            }
        }
    };

    HgsTabs.prototype = {
        defaults: {
            animation: { duration: 200, effects: "fadeIn", easing: "swing" },
            autoplay: { interval: 0 },           
            defaultTab: "tab1",
            event: "click",
            hashAttribute: "data-link",            
            position: hgs.classes.positions.topLeft,
            orientation: hgs.classes.orientations.horizontal,            
            rounded: true,
            shadows: true,
            tabWidth: 150,
            tabHeight: 51,
            theme: hgs.classes.themes.silver,
            urlBased: false,
            select: function (tab, content) { },
            size: hgs.classes.sizes.medium,
            style: hgs.classes.styles.normal
        },
        init: function () {
            var _base = this;
            
           
            _base.settings = $.extend(true,{}, _base.defaults, _base.options, _base.metadata, _base.attrdata);

            methods.updateClasses(_base);
            methods.bindEvents(_base);

            // check if url based is enabled
            if (_base.settings.urlBased === true) {
                if (document.location.hash) {
                    var tab = _base.Hash.get(_base.tabID);
                    if (tab != null) {
                        methods.showTab(_base, tab);
                    }
                    else {
                        methods.showTab(_base, _base.settings.defaultTab);
                    }
                }
                else {
                    methods.showTab(_base, _base.settings.defaultTab);
                }

                // bind the event hashchange, using jquery-hashchange-plugin
                if (typeof ($(window).hashchange) != "undefined") {
                    $(window).hashchange(function () {
                        //methods.log("even: hashchange (plugin)");
                        //window.hgs.debug.start();
                        var _newTab = _base.Hash.get(_base.tabID);                        
                        if (_base.currentTab.attr(_base.settings.hashAttribute) !== _newTab) {
                            methods.showTab(_base, _newTab);
                        }
                        //window.hgs.debug.stop();
                    });
                }
                else {
                    // Bind the event hashchange, using jquery event binding, not supported (IE6, IE7) 
                    $(window).bind('hashchange', function () {
                        //methods.log("even: hashchange (native)");

                        //window.hgs.debug.start();
                        var _newTab = _base.Hash.get(_base.tabID);
                        if (_base.currentTab.attr(_base.settings.hashAttribute) !== _newTab) {
                            methods.showTab(_base, _newTab);
                        }
                        //window.hgs.debug.stop();
                    });
                }
            }
            else {
                methods.showTab(_base, _base.settings.defaultTab);
            }

            methods.initAutoPlay(_base);

            return this;
        },
        setOptions: function (_option) {
            var _base = this;
           
            _base.settings = $.extend(true,_base.settings, _option);

            methods.updateClasses(_base);
            methods.initAutoPlay(_base);            
            return _base;
        },
        add: function (_t, _c) {          
            var _base = this;
            var _insertedTab = methods.create(_t, _c);

            _insertedTab.tab
                .appendTo(_base.$tabGroup)
                .hide()
                .fadeIn(500);

            _insertedTab.content
                .appendTo(_base.$container);

            methods.updateClasses(_base);
            methods.bindEvent(_base, _insertedTab.tab);
            return _base;
        },
        remove: function (_i) {            
            var _base = this;
            var _index = (_i - 1);
            var _tabToRemove = _base.$tabs.eq(_index);
            var _contentToRmove = _base.$contents.eq(_index);

            _contentToRmove.remove();
            _tabToRemove.fadeOut(500, function () {
                $(this).remove();
                methods.updateClasses(_base);
            });

            return _base;
        },
        select: function (_i) {
            var _base = this;
            methods.changeHash(_base, _base.$elem.find("> ul > li").eq(_i - 1).attr(_base.settings.hashAttribute));
            return _base;
        },
        first: function () {
            var _base = this;
            _base.select(methods.getFirst());
            return _base;
        },
        prev: function () {
            var _base = this;
            var currentIndex = parseInt(_base.currentTab.index()) + 1;

            if (currentIndex <= methods.getFirst(_base)) {
                _base.select(methods.getLast(_base));
            }
            else {
                _base.select(currentIndex - 1);
                methods.log("prev tab : " + (currentIndex - 1));
            }
            return _base;
        },
        next: function (_base) {
            _base = (_base) ? _base : this;
            var _currentIndex = parseInt(_base.currentTab.index()) + 1;
            var _count = parseInt(_base.$tabGroup.children("li").size());

            if (_currentIndex >= _count) {
                _base.select(methods.getFirst());
            }
            else {
                _base.select(_currentIndex + 1);
                methods.log("next tab : " + (_currentIndex + 1));
            }
            return _base;
        },
        last: function () {
            var _base = this;
            _base.select(methods.getLast(_base));
            return _base;
        },
        play: function (interval) {
            var _base = this;
            if (interval == null || interval < 0) {
                interval = 2000;
            }
            _base.settings.autoplay.interval = interval;
            _base.stop();
            _base.autoplayIntervalId = setInterval(function () { _base.next(_base); }, _base.settings.autoplay.interval);

            return _base;
        },
        stop: function (_base) {
            _base = (_base) ? _base : this;
            clearInterval(_base.autoplayIntervalId);
            return _base;
        }
    };

    var methods = {
        log: function (msg) {
            if (console) {
                console.log(msg);
            }
        },
        isEmpty: function (_str) {
            return (!_str || 0 === _str.length);
        },
        updateClasses: function (_base) {
            _base.tabID = _base.$elem.attr("id");
            _base.$tabGroup = _base.$elem.find("> ul").addClass(hgs.classes.tabGroup);
            _base.$tabs = _base.$tabGroup.find("> li");
            _base.$container = _base.$elem.find("> div");
            _base.$contents = _base.$container.find("> div");

            //update container and content classes 
            _base.$container.addClass(hgs.classes.container);
            _base.$contents.addClass(hgs.classes.content);

            //update li classes 
            _base.$tabs.each(function (index, item) {
                $(item)
                    .removeClass(hgs.classes.first)
                    .removeClass(hgs.classes.last)
                    .attr(_base.settings.hashAttribute, "tab" + (index + 1))
                    .addClass(hgs.classes.tab)
                    .find("a")
                    .addClass(hgs.classes.link);
            });

            //update first and last
            _base.$tabs.filter(hgs.classes.first + ":not(:first-child)").removeClass(hgs.classes.first);
            _base.$tabs.filter(hgs.classes.last + ":not(:last-child)").removeClass(hgs.classes.last);
            _base.$tabs.filter("li:first-child").addClass(hgs.classes.first);
            _base.$tabs.filter("li:last-child").addClass(hgs.classes.last);

            var _styles = methods.toArray(hgs.classes.styles);
            var _themes = methods.toArray(hgs.classes.themes);
            var _sizes = methods.toArray(hgs.classes.sizes);
            var _positions = methods.toArray(hgs.classes.positions);
            
            _base.$elem
                .removeClass(hgs.classes.wrapper)
                .removeClass(hgs.classes.orientations.vertical)
                .removeClass(hgs.classes.orientations.horizontal)
                .removeClass(hgs.classes.rounded)
                .removeClass(hgs.classes.shadows)
                .removeClass(_styles.join().replace(hgs.commaRegExp, hgs.space))
                .removeClass(_positions.join().replace(hgs.commaRegExp, hgs.space))
                .removeClass(_sizes.join().replace(hgs.commaRegExp, hgs.space))
                .addClass(_base.settings.style)
                .addClass(_base.settings.size);

           

            // check theme
            if (!methods.isEmpty(_base.settings.theme)) {
                _base.$elem
                    .removeClass(_themes.join().replace(hgs.commaRegExp, hgs.space))
                    .addClass(_base.settings.theme);
            }
            else {
                if (!_base.$elem.hasClasses(_themes)) {
                    _base.$elem.addClass(hgs.classes.themes.silver);
                }
            }

            //rounded
            if (_base.settings.rounded === true) {
                _base.$elem.addClass(hgs.classes.rounded);
            }

            if (_base.settings.shadows === true) {
                _base.$elem.addClass(hgs.classes.shadows);
            }

            methods.checkPosition(_base);
        },
        checkPosition: function (_base) {
            _base.$container.appendTo(_base.$elem);
            _base.$tabGroup.prependTo(_base.$elem);
            _base.$elem.find("> span.my-tab-spacer").remove();
            _base.$elem.addClass(hgs.classes.wrapper);
            
            
            if (_base.settings.orientation === hgs.classes.orientations.vertical) {
                _base.$elem.addClass(hgs.classes.orientations.vertical);

                var _height = _base.settings.tabHeight;

                switch (_base.settings.size) {
                    case hgs.classes.sizes.mini: _height = 33; break;
                    case hgs.classes.sizes.small: _height = 39; break;
                    case hgs.classes.sizes.medium: _height = 45; break;
                    case hgs.classes.sizes.large: _height = 51; break;
                    case hgs.classes.sizes.xlarge: _height = 57; break;
                    case hgs.classes.sizes.xxlarge: _height = 63; break;                    
                    default: _height = 45;
                }

                var _tabCount = parseInt(_base.$tabGroup.children("li").size());
                var _containerSize = _height * _tabCount - 1;

                _base.$container.css({
                    'min-height': _containerSize,
                    'padding': 0,
                    'margin-top': 0,
                    'margin-bottom': 0
                });
                if (_base.settings.position !== hgs.classes.positions.topRight) {
                    _base.settings.position = hgs.classes.positions.topLeft;
                }
            }
            else {
                _base.settings.orientation = hgs.classes.orientations.horizontal;
                _base.$elem.addClass(hgs.classes.orientations.horizontal);
                if (_base.settings.position === hgs.classes.positions.bottomLeft
                    || _base.settings.position === hgs.classes.positions.bottomCenter
                    || _base.settings.position === hgs.classes.positions.bottomRight
                    || _base.settings.position === hgs.classes.positions.bottomCompact) {
                    _base.$tabGroup.appendTo(_base.$elem);
                    $(hgs.elementSpacer).appendTo(_base.$elem);
                    _base.$container.prependTo(_base.$elem);
                }
            }
            if (_base.settings.position === hgs.classes.positions.topCompact || _base.settings.position === hgs.classes.positions.bottomCompact) {
                var count = parseInt(_base.$tabGroup.children("li").size());
                var groupWidth = _base.settings.tabWidth * count;
                   
              

                switch (_base.BrowserDetection.browser) {                        
                    case "Firefox":   break;

                    case "Explorer": 
                        switch (_base.BrowserDetection.version) {
                            case 7: groupWidth = groupWidth + 1; break;                                                            
                            default:       
                        }
                    break;

                    default: groupWidth = groupWidth + 1;
                }

                _base.$elem.css("width", groupWidth + "px");
                _base.$tabs.each(function (index, item) {
                    $(item).css("width", _base.settings.tabWidth + "px");
                });
            }
            else {
                _base.$elem.css("width", "");
                _base.$tabs.each(function (index, item) {
                    $(item).css("width", "");
                });
            }

            _base.$elem.addClass(_base.settings.position);
        },
        bindEvents: function (_base) {
            _base.$tabs.each(function () {
                methods.bindEvent(_base, $(this));
            });
        },
        bindEvent: function (_base, _tab) {
            _tab.on(_base.settings.event, function () {
                _base.stop();               
                methods.changeHash(_base, _tab.attr(_base.settings.hashAttribute));                
            });
        },
        showTab: function (_base, tab) {
            if (tab != null) {
                _base.$tabs.removeClass(hgs.classes.active);
                _base.currentTab = _base.$tabs.filter("li[" + _base.settings.hashAttribute + "=" + tab + "]");
                _base.currentTab.addClass(hgs.classes.active);
               
                // get current tab index
                var index = _base.$tabs.index(_base.currentTab);

                // hide all content divs and show current one                
                if (_base.settings.animation !== false && _base.settings.animation != null) {
                    if (_base.settings.animation.effects === "fadeIn") {
                        _base.$contents.removeClass(hgs.classes.active).hide().eq(index).addClass(hgs.classes.active).fadeIn(_base.settings.animation.duration, _base.settings.animation.easing);
                    }
                    else if (_base.settings.animation.effects === "slideDown") {
                        _base.$contents.removeClass(hgs.classes.active).slideUp(200).eq(index).addClass(hgs.classes.active).slideDown(_base.settings.animation.duration, _base.settings.animation.easing);
                    }
                    else if (_base.settings.animation.effects === "slideToggle") {
                        _base.$contents.removeClass(hgs.classes.active).hide().eq(index).addClass(hgs.classes.active).slideToggle(_base.settings.animation.duration, _base.settings.animation.easing);
                    }
                    else if (_base.settings.animation.effects === "fadeToggle") {
                        _base.$contents.removeClass(hgs.classes.active).hide().eq(index).addClass(hgs.classes.active).fadeToggle(_base.settings.animation.duration, _base.settings.animation.easing);
                    }
                    else if (_base.settings.animation.effects === "slideUp") {
                        _base.$contents.removeClass(hgs.classes.active).slideUp(200).eq(index).addClass(hgs.classes.active).slideDown(_base.settings.animation.duration, _base.settings.animation.easing);
                    }
                }
                else {
                    _base.$contents.removeClass(hgs.classes.active).hide().eq(index).addClass(hgs.classes.active).show();
                }

                if (typeof _base.settings.select == 'function') {
                    _base.settings.select.call(this, _base.currentTab, _base.$contents.eq(index));
                }
            }
        },
        initAutoPlay: function (_base) {
            if (_base.settings.autoplay !== false && _base.settings.autoplay != null) {
                if (_base.settings.autoplay.interval > 0) {
                    _base.stop();
                    _base.autoplayIntervalId = setInterval(function () { _base.next(_base); }, _base.settings.autoplay.interval);
                } else {
                    _base.stop();
                }
            }
            else {
                _base.stop();
            }
        },
        changeHash: function (_base, tab) {
            if (_base.settings.urlBased === true) {
                if (typeof ($(window).hashchange) != "undefined") {                    
                    //window.hgs.debug.start();
                    _base.Hash.set(_base.tabID, tab);
                    //window.hgs.debug.stop();
                }
                else {
                    methods.log("browser: " + _base.BrowserDetection.browser + " version: " + _base.BrowserDetection.version);
                    if (_base.BrowserDetection.browser === "Explorer" && _base.BrowserDetection.version <= 7) {
                        //IE and browsers that don't support hashchange
                        methods.log("IE");
                        methods.showTab(_base, tab);
                    }
                    else {
                        //modern browsers                        
                        _base.Hash.set(_base.tabID, tab);
                    }
                }
            }
            else {
                methods.showTab(_base, tab);
            }
        },
        getFirst: function (_base) {
            return 1;
        },
        getLast: function (_base) {
            return parseInt(_base.$tabGroup.children("li").size());
        },
        create: function (_t, _c) {
            var _tab = $("<li><a>" + _t + "</a></li>");
            var _content = $("<div>" + _c + "</div>");

            return { tab: _tab, content: _content };
        },
        toArray: function (_object) {
            return $.map(_object, function (value, key) {
                return value;
            });
        }
    };

    HgsTabs.defaults = HgsTabs.prototype.defaults;

    $.fn.hgsTabs = function (options) {
        return this.each(function () {
            if (undefined == $(this).data(hgs.pluginName)) {
                var hgsTabs = new HgsTabs(this, options).init();
                $(this).data(hgs.pluginName, hgsTabs);
            }
        });
    };

    window.hgs.tabs = HgsTabs;
  
    $(document).ready(function () {
        $("[data-role='my-tabs']").each(function (index, item) {
            if (!$(item).hgsTabs()) {
                $(item).hgsTabs();
            }
        });
    });
})(jQuery, window, document);