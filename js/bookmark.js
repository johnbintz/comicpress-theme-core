var cl = 31;

/* Below are our functions for this little script */

function bmhome() {
  if(document.getElementById) {
    document.getElementById('gtc').src = imgGotoOn;
    document.getElementById('rmc').src = imgClearOn;
  }
  createCookie("bm", comicPermalink, cl);        
}

function bm() {
  if(document.getElementById) {
    document.getElementById('gtc').src = imgGotoOn;
    document.getElementById('rmc').src = imgClearOn;
  }
  createCookie("bm", window.location, cl);
}

function bmc() {
  if(document.getElementById) {
    document.getElementById('gtc').src = imgGotoOff;
    document.getElementById('rmc').src = imgClearOff;
  }
  createCookie("bm","",-1);
}
  
function gto() {
  var g = readCookie('bm');
  if(g) {
    window.location = g;
  } 
}

/* The follow functions have been borrowed from Peter-Paul Koch. Please find them here: http://www.quirksmode.org */

function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  } else var expires = "";
  document.cookie = name+"="+value+expires+"; path="+comicDir;
}
function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function writeBookmarkWidget() {
  createCookie('t', 1);
  var c = readCookie('t');
  if (c && document.getElementById) {
    var l = readCookie('bm');
    var gt = imgGotoOff;
    var ct = imgClearOff;
    if (l) {
      gt = imgGotoOn;
      ct = imgClearOn;
    }
    document.write('<div id="bmh" style="width: 173px; margin: 15px 0 0 0; padding: 5px; position: absolute; color: #eee; font-size: 11px; background-color:#222; border: 1px solid #ccc; visibility: hidden;"><b>COMIC BOOKMARK</b><br />Click "Tag Page" to bookmark a comic page. When you return to the site, click "Goto Tag" to continue where you left off.</div>');
    if (isHome) {
      document.write('<a href="#" onClick="bmhome();return false;"><img src="'+imgTag+'" alt="Tag This Page" border="0"></a>');
      document.write('<a href="#" onClick="gto();return false;"><img src="'+gt+'" alt="Goto Tag" border="0" id="gtc"></a>');
      document.write('<a href="#" onClick="bmc();return false;"><img src="'+ct+'" alt="Clear Tag" border="0" id="rmc"></a>');
      document.write('<a href="#" onMouseOver="document.getElementById(\'bmh\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'bmh\').style.visibility=\'hidden\';" onClick="return false;"><img src="'+imgInfo+'" alt="" border="0"></a>');
    } else if (isSingle) {
      document.write('<a href="#" onClick="bm();return false;"><img src="'+imgTag+'" alt="Tag This Page" border="0"></a>');
      document.write('<a href="#" onClick="gto();return false;"><img src="'+gt+'" alt="Goto Tag" border="0" id="gtc"></a>');
      document.write('<a href="#" onClick="bmc();return false;"><img src="'+ct+'" alt="Clear Tag" border="0" id="rmc"></a>');
      document.write('<a href="#" onMouseOver="document.getElementById(\'bmh\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'bmh\').style.visibility=\'hidden\';" onClick="return false;"><img src="'+imgInfo+'" alt="" border="0"></a>');
    }
  }
}