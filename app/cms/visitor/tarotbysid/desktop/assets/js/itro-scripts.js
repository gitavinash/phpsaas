var itro_is_preview;var itro_cookie_expiration;var itro_age_restriction;function itro_enter_anim(auto_margin)
{if(document.cookie.indexOf("popup_cookie")==-1||itro_is_preview===true)
{if(auto_margin=='yes'){itro_margin_refresh(50);}
jQuery("#itro_popup").css('display','none');jQuery("#itro_popup").css('opacity',1);jQuery("#itro_popup").css('visibility','visible');jQuery("#itro_opaco").fadeIn(function()
{jQuery("#itro_popup").fadeIn();if(itro_age_restriction===false)
{itro_set_cookie("popup_cookie","one_time_popup",itro_cookie_expiration);}
if(auto_margin=='yes'){setInterval(function(){itro_margin_refresh();},500);}});}else{jQuery("#itro_popup").css('display','none');}}
function itro_exit_anim(){jQuery('#itro_popup').fadeOut(function(){jQuery('#popup_content').remove();jQuery('#itro_opaco').fadeOut();});}
function itro_margin_refresh(speed){if(typeof(speed)==undefined){speed=200;}
if(typeof(window.innerWidth)=='number')
{browserWidth=window.innerWidth;browserHeight=window.innerHeight;}else if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight))
{browserWidth=document.documentElement.clientWidth;browserHeight=document.documentElement.clientHeight;}else if(document.body&&(document.body.clientWidth||document.body.clientHeight))
{browserWidth=document.body.clientWidth;browserHeight=document.body.clientHeight;}
popupHeight=document.getElementById('itro_popup').offsetHeight;parentOffset=jQuery('#itro_popup').position().top;docOffset=jQuery('#itro_popup').offset().top;desTopWindowMargin=Math.round((browserHeight-popupHeight)/2);desTopWindowMargin=desTopWindowMargin<0?0:desTopWindowMargin;popupTopMargin=desTopWindowMargin-docOffset+parentOffset;if(jQuery('#itro_popup').css('position')=='absolute'){if(Math.abs(popupTopMargin-parentOffset)>2){jQuery('#itro_popup').animate({top:popupTopMargin},speed);}}else{if(Math.abs(popupTopMargin-parentOffset)>2){jQuery('#itro_popup').animate({top:desTopWindowMargin},speed);}}}
function itro_invert_color(hexTripletColor){var color=hexTripletColor;color=color.substring(1);color=parseInt(color,16);color=0xFFFFFF^color;color=color.toString(16);color="#"+color;return color;}
function itro_rgb2hex(rgbString){var parts=rgbString.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);delete(parts[0]);for(var i=1;i<=3;++i){parts[i]=parseInt(parts[i]).toString(16);if(parts[i].length==1)
parts[i]='0'+parts[i];}
return '#'+parts.join('').toUpperCase();}
function popup_delay(auto_margin){delay--;if(delay<=0)
{clearInterval(interval_id_delay);itro_enter_anim(auto_margin);}}
function popTimer(){if(popTime>0){document.getElementById("timer").innerHTML=popTime;popTime--;}else{clearInterval(interval_id);itro_exit_anim();}}
function itro_set_cookie(c_name,value,exhours){var exdate=new Date();exdate.setTime(exdate.getTime()+(exhours*3600*1000));var c_value=escape(value)+((exhours==null)?"":"; expires="+exdate.toUTCString());document.cookie=c_name+"="+c_value+"; path=/";}