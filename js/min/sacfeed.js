(function(a){a.build="0.0.1";a.packages={sf:["sf"],sacfeed:"sacfeed Detect Poly UI UI.Ele UI.Nav UI.Section Ext".split(" "),"Ext.GA":["Ext.GA"]}})(window.sacfeed);(function(a,b,c){if(!b.modules.sacfeed){var g=b.packages=b.packages||{},q=b.packagemap={},f;for(f in g)for(var p=g[f],r=0,s=p.length;r<s;++r)q[p[r]]=f;var t={create:"POST",read:"GET",update:"PUT","delete":"DELETE"};for(f in t);b.urls.www=b.protocol+"//www.sacfeed.com/";b.urls.js=b.protocol+"//js.sacfeed.com/v"+(b.devmode?b.version+"/src/":b.build+"/min/");b.urls.img=b.protocol+"//img.sacfeed.com/v"+(b.devmode?b.version:b.build)+"/";b.urls.authorimg=b.urls.img+"author/";b.callbacks={};var h={},u=function(b){if(!b||
!Object.keys(b).length)return!1;var a=[],h;for(h in b){var d=b[h],c=encodeURIComponent(h);if(!0===d)a.push(c);else{var l=encodeURIComponent(d);d instanceof Array&&(l=get?"%5B"+l+"%5D":"["+l+"]");a.push(c+"="+l)}}return a.join("&")};b.modules.sacfeed=!0;b.init=function(e){delete b.init;e=e||b.noop;var d=["Poly","Detect"];a.jQuery||d.unshift("//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js");for(var m={ga:"Ext.GA"},p=0,n=d.length,g=function(){if(!(++p<n)){var d=b.$=a.jQuery;b.$window=d(a);
b.$document=d(document);b.$body=d("body");b.$header=d("header");b.$main=d("main");b.load=l;b.req=b.Detect.XHR?function(d,a,e,k){k=k||b.noop;var l=t[d];d="GET"===l||"DELETE"===l;var m=b.urls.api+a.replace(/\?.*$/,"").replace(/\.[^\/]*$/,"");a=null;(e=u(e))&&(d?m+="?"+e:a=e);if("GET"===l&&h[m]){if(h[m].expires>(new Date).getTime()){k(h[m].resp);return}delete h[m]}var f=new XMLHttpRequest;f.open(l,m,!0);var p=!1;f.onreadystatechange=function(){if(4===f.readyState&&!p){p=!0;for(var b={},d=f.getAllResponseHeaders().split("\n"),
a=0,e=d.length;a<e;++a){var n=d[a].trim();n&&(n=n.split(/\s*:\s*/),b[n[0].toLowerCase()]=n[1])}d=0;if(b["cache-control"])for(var g=b["cache-control"].split(/\s*[,;]\s*/),a=0,e=g.length;a<e;++a){if(n=g[a].trim().split(/\s*=\s*/),"max-age"===n[0]){d=parseInt(n[1]);break}}else b.expires&&(d=new Date,d=parseInt(((new Date(b.expires)).getTime()-d.getTime())/1E3));d=0>d?0:d;a=f.responseText.trim()||"{}";a=c.parse(a);200>f.status||299<f.status?(e=a,e.ttl=d,a={}):e={code:f.status,message:f.statusText,ttl:d};
b={status:e,headers:b,result:a};"GET"===l&&0<d&&(h[m]={resp:b,expires:(new Date).getTime()+1E3*d});k(b)}};f.send(a)}:b.Detect.XDR?function(d,a,e,k){k=k||b.noop;var l=t[d],m=b.urls.api+a.replace(/\?.*$/,"").replace(/\.[^\/]*$/,"")+".xdr";"DELETE"===l?(l="GET",e=e||{},e.m="d"):"PUT"===l&&(l="POST",e=e||{},e.m="u");a="GET"===l;var f=null;(e=u(e))&&(a?m+="?"+e:f=e);if("read"===d&&h[m]){if(h[m].expires>(new Date).getTime()){k(h[m].resp);return}delete h[m]}var n=new XDomainRequest;n.onprogress=b.noop;n.ontimeout=
b.noop;n.onerror=b.noop;var p=!1;n.onload=function(){if(!p){p=!0;var b=n.responseText.trim()||"{}",b=c.parse(b),a=b.status.ttl;"read"===d&&0<a&&(h[m]={resp:b,expires:(new Date).getTime()+1E3*a});k(b)}};n.open(l,m);n.send(f)}:function(d,a,e,l){l=l||b.noop;var k=t[d],c=b.urls.api+a.replace(/\?\.*$/,"").replace(/\.[^\/]*$/,"")+".jsonp";"POST"===k?(e=e||{},e.m="c"):"PUT"===k?(e=e||{},e.m="u"):"DELETE"===k&&(e=e||{},e.m="d");var k="GET",m=b.randID();e=u(e);var f;e?(c+="?"+e,f=c+"&"):f+=c+"?";f+="c="+encodeURIComponent(m);
if("read"===d&&h[c]){if(h[c].expires>(new Date).getTime()){l(h[c].resp);return}delete h[c]}b.callbacks[m]=function(e){delete b.callbacks[m];var a=e.status.ttl;"read"===d&&0<a&&(h[c]={resp:e,expires:(new Date).getTime()+1E3*a});l(e)};b.inc(f)};for(var d=0,k=b.delayed.length;d<k;++d){var f=b.delayed[d];"load"===f.type?b.load.apply(b,f.arguments):"req"===f.type&&b.req.apply(b,f.arguments)}delete b.delayed;d=0;for(k=b.analytics.length;d<k;++d){f=m[b.analytics[d]];if(!f)throw Error('Invalid analytics item "'+
b.analytics[d]+'"');b.load(f)}delete b.analytics;e()}},f=0;f<n;++f){var q=d[f];/^(?:https?:)?\/\//.test(q)?b.inc(q,g):l(q,g)}};b.randID=function(){return Math.floor(2147483647*Math.random()).toString(36)};var n={},d=function(e){if("sacfeed"===e)return b;var d=b;e=e.split(".");for(var a=0,h=e.length;a<h;++a)d=d[e[a]];return d},m=function(b){var a=function(){for(var a=0,d=n[b].length;a<d;++a)n[b][a]();delete n[b]},h=d(b);h.init?h.init(a):a()},l=function(a,h){h=h||b.noop;if(null===a)h();else if(a instanceof
Array)for(var c=0,f=a.length,p=function(){++c<f||h()},g=0;g<f;++g)l(a[g],p);else n[a]?n[a].push(h):b.modules[a]&&!d(a).init?h():(n[a]=[h],(p=q[a])?b.modules[p]?m(a):b.inc(b.urls.js+p.toLowerCase().replace(".","/")+".js",function(){m(a)}):b.inc(b.urls.js+a.toLowerCase().replace(".","/")+".js",function(){m(a)}))}}})(window,window.sacfeed,window.JSON);(function(a,b,c){if(!a.modules.Detect){var g=a.Detect={};a.modules.Detect=!0;g.init=function(q){delete g.init;q=q||a.noop;g.XHR=b&&"withCredentials"in new b;g.XDR="undefined"!==typeof c;q()}}})(window.sacfeed,window.XMLHttpRequest,window.XDomainRequest);(function(a,b,c,g,q){if(!a.modules.Poly){var f=a.Poly={};a.modules.Poly=!0;f.init=function(p){delete f.init;p=p||a.noop;b.toLowerCase=b.toLowerCase||b.toString;b.toUpperCase=b.toUpperCase||b.toString;c.ltrim=c.ltrim||c.trimLeft||function(){return this.replace(/^\s+/,"")};c.rtrim=c.rtrim||c.trimRight||function(){return this.replace(/\s+$/,"")};c.trim=c.trim||function(){return this.replace(/^\s+|\s+$/g,"")};c.ucwords=c.ucwords||function(){return this.replace(/\b[a-z]+\b/gi,function(a){return a.charAt(0).toUpperCase()+
a.substr(1).toLowerCase()})};c.entityEncode=c.entityEncode||function(){return $("<p/>").text(this).html()};c.entityDecode=c.entityDecode||function(){return $("<p/>").html(this).text()};var r=[{base:"A",letters:/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},{base:"AA",letters:/[\uA732]/g},{base:"AE",letters:/[\u00C6\u01FC\u01E2]/g},{base:"AO",
letters:/[\uA734]/g},{base:"AU",letters:/[\uA736]/g},{base:"AV",letters:/[\uA738\uA73A]/g},{base:"AY",letters:/[\uA73C]/g},{base:"B",letters:/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},{base:"C",letters:/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},{base:"D",letters:/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},{base:"DZ",letters:/[\u01F1\u01C4]/g},{base:"Dz",letters:/[\u01F2\u01C5]/g},{base:"E",letters:/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
{base:"F",letters:/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},{base:"G",letters:/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},{base:"H",letters:/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},{base:"I",letters:/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},{base:"J",letters:/[\u004A\u24BF\uFF2A\u0134\u0248]/g},{base:"K",letters:/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
{base:"L",letters:/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},{base:"LJ",letters:/[\u01C7]/g},{base:"Lj",letters:/[\u01C8]/g},{base:"M",letters:/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},{base:"N",letters:/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},{base:"NJ",letters:/[\u01CA]/g},{base:"Nj",letters:/[\u01CB]/g},{base:"O",letters:/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
{base:"OI",letters:/[\u01A2]/g},{base:"OO",letters:/[\uA74E]/g},{base:"OU",letters:/[\u0222]/g},{base:"P",letters:/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},{base:"Q",letters:/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},{base:"R",letters:/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},{base:"S",letters:/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},{base:"T",
letters:/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},{base:"TZ",letters:/[\uA728]/g},{base:"U",letters:/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},{base:"V",letters:/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},{base:"VY",letters:/[\uA760]/g},{base:"W",letters:/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
{base:"X",letters:/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},{base:"Y",letters:/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},{base:"Z",letters:/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},{base:"a",letters:/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
{base:"aa",letters:/[\uA733]/g},{base:"ae",letters:/[\u00E6\u01FD\u01E3]/g},{base:"ao",letters:/[\uA735]/g},{base:"au",letters:/[\uA737]/g},{base:"av",letters:/[\uA739\uA73B]/g},{base:"ay",letters:/[\uA73D]/g},{base:"b",letters:/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},{base:"c",letters:/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},{base:"d",letters:/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
{base:"dz",letters:/[\u01F3\u01C6]/g},{base:"e",letters:/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},{base:"f",letters:/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},{base:"g",letters:/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},{base:"h",letters:/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
{base:"hv",letters:/[\u0195]/g},{base:"i",letters:/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},{base:"j",letters:/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},{base:"k",letters:/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},{base:"l",letters:/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},{base:"lj",letters:/[\u01C9]/g},
{base:"m",letters:/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},{base:"n",letters:/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},{base:"nj",letters:/[\u01CC]/g},{base:"o",letters:/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
{base:"oe",letters:/\u0153/g},{base:"oi",letters:/[\u01A3]/g},{base:"ou",letters:/[\u0223]/g},{base:"oo",letters:/[\uA74F]/g},{base:"p",letters:/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},{base:"q",letters:/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},{base:"r",letters:/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},{base:"s",letters:/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
{base:"t",letters:/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},{base:"tz",letters:/[\uA729]/g},{base:"u",letters:/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},{base:"v",letters:/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},{base:"vy",letters:/[\uA761]/g},{base:"w",letters:/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
{base:"x",letters:/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},{base:"y",letters:/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},{base:"z",letters:/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}];c.deaccent=c.deaccent||function(){for(var a=this.toString(),b=0,c=r.length;b<c;b++)a=a.replace(r[b].letters,r[b].base);return a};g.bind=g.bind||function(a){var b=this;if("function"!==typeof b)throw new TypeError;var c=
Array.prototype.slice,d=c.call(arguments,1),m=function(){if(!(this instanceof m))return b.apply(a,d.concat(c.call(arguments)));var l=function(){};l.prototype=b.prototype;var l=new l,e=b.apply(l,d.concat(c.call(arguments)));return Object(e)===e?e:l};return m};var s=[["Inv","Invalid"],["Jan","January"],["Feb","February"],["Mar","March"],["Apr","April"],["May","May"],["Jun","June"],["Jul","July"],["Aug","August"],["Sep","September"],["Oct","October"],["Nov","November"],["Dec","December"]],t=[["Sun",
"Sunday"],["Mon","Monday"],["Tue","Tuesday"],["Wed","Wednesday"],["Thu","Thursday"],["Fri","Friday"],["Sat","Saturday"]];q.format=q.format||function(a,b){a=(a||"Y-m-d H:i:s").replace(/([a-z])/ig,"{{$1}}");var c,d,m,l,e,f,p;b?(c=this.getUTCFullYear(),d=this.getUTCMonth(),m=this.getUTCDate(),l=this.getUTCDay(),e=this.getUTCHours(),f=this.getUTCMinutes(),p=this.getUTCSeconds()):(c=this.getFullYear(),d=this.getMonth(),m=this.getDate(),l=this.getDay(),e=this.getHours(),f=this.getMinutes(),p=this.getSeconds());
++d;a=a.replace("{{Y}}",function(){for(var a=Math.abs(c),b="";1E3>a;)b+="0",a*=10;return b+c});a=a.replace("{{m}}",10>d?"0"+d:d);a=a.replace("{{M}}",s[d][0]);a=a.replace("{{F}}",s[d][1]);a=a.replace("{{d}}",10>m?"0"+m:m);a=a.replace("{{j}}",m);a=a.replace("{{l}}",t[l][1]);a=a.replace("{{H}}",10>e?"0"+e:e);a=a.replace("{{g}}",12<e?e-12:e);a=a.replace("{{A}}",11<e?"PM":"AM");a=a.replace("{{a}}",11<e?"pm":"am");a=a.replace("{{i}}",10>f?"0"+f:f);a=a.replace("{{s}}",10>p?"0"+p:p);return a.replace(/\{\{|\}\}/g,
"")};Object.create=Object.create||function(){var a=function(){};return function(b){if(1<arguments.length)throw Error("Second argument not supported");if("object"!==typeof b)throw new TypeError("Argument must be an object");a.prototype=b;var c=new a;a.prorotype=null;return c}}();Object.keys=Object.keys||function(){var a=Object.prototype.hasOwnProperty,b=!{toString:null}.propertyIsEnumerable("toString"),c="toString toLocaleString valueOf hasOwnProperty isPrototypeOf propertyIsEnumerable constructor".split(" "),
d=c.length;return function(f){if("object"!==typeof f&&("function"!==typeof f||null===f))throw new TypeError("Object.keys called on non-object");var l=[],e;for(e in f)a.call(f,e)&&l.push(e);if(b)for(e=0;e<d;e++)a.call(f,c[e])&&l.push(c[e]);return l}}();p()}}})(window.sacfeed,Number.prototype,String.prototype,Function.prototype,Date.prototype,Object);(function(a){if(!a.modules.UI){var b=a.UI=a.UI||{};a.modules.UI=!0;b.init=function(c){delete b.init;c=c||a.noop;c()}}})(window.sacfeed);(function(a){if(!a.modules["UI.Ele"]){var b=a.UI=a.UI||{},c=b.Ele={};a.modules["UI.Ele"]=!0;c.init=function(g){delete c.init;g=g||a.noop;a.load("UI",function(){var q=a.$;c=b.Ele=function(a){if(!(this instanceof c))return new c(a);a=a||{};if(!a.parent)throw Error("UI.Ele must have a parent option");this.$=q(a.parent);this.parent=a.parent;return this};c.prototype.render=function(){return this};g()})}}})(window.sacfeed);(function(a){if(!a.modules["UI.Nav"]){var b=a.UI=a.UI||{},c=b.Nav={};a.modules["UI.Nav"]=!0;c.init=function(g){delete c.init;g=g||a.noop;a.load("UI.Ele",function(){var q=a.$,f=b.Ele;c=b.Nav=function(a){if(!(this instanceof c))return new c(a);a=a||{};a.parent=a.parent||"nav";a.button=a.button||".sf-navbtn";f.prototype.constructor.call(this,a);if(!a.button)throw Error("UI.Nav must have a button option");this.$button=q(a.button);return this};c.prototype=Object.create(f.prototype);c.prototype.render=
function(){f.prototype.render.call(this);this.$.children("a").length||a.req("read","section/list",null,function(b){if(200===b.status.code&&b.result.sections&&b.result.sections.length)for(var c=0,f=b.result.sections.length;c<f;++c){var g=b.result.sections[c];this.$.append('<a href="'+a.urls.www+g.id.replace(/^\//,"")+'">'+g.name+"</a>")}}.bind(this));this.$button.click(function(a){this.$.fadeToggle(100);a.preventDefault&&a.preventDefault();return!1}.bind(this));a.$body.click(function(){this.$.fadeOut(100)}.bind(this));
return this};g()})}}})(window.sacfeed);(function(a){if(!a.modules["UI.Section"]){var b=a.UI=a.UI||{},c=b.Section={};a.modules["UI.Section"]=!0;c.init=function(g){delete c.init;g=g||a.noop;a.load(["UI.Ele","UI.Nav"],function(){var q=a.$,f=a.$window,p=a.$document,r=b.Ele;c=b.Section=function(d){if(!(this instanceof c))return new c(d);d=d||{};d.parent=d.parent||a.$body;this.section=d.section||"/";this.first=d.first||null;this.last=d.last||null;this.authormap=d.authormap||{};this.titlemap=d.titlemap||{};this.more=!0;this.queue=[];this.$queuebtn=
a.$header.find(".sf-queuebtn");r.prototype.constructor.call(this,d);this.nav=new b.Nav;return this};c.prototype=Object.create(r.prototype);var s=!1,t=function(a){if(200===a.status.code&&a.result.articles&&a.result.articles.length){var b=a.result.articles.length;this.last=a.result.articles[b-1].id;for(var c=0;c<b;++c)n.call(this,a.result.articles[c],!0);s=!1}else this.more=!1},h=function(){if(f.scrollTop()+f.height()>p.height()-1E3&&!s){s=!0;var b=t.bind(this),c={section:this.section};this.last&&(c.a=
this.last);a.req("read","article/list",c,b||a.noop)}},u=function(b){var c={section:this.section};this.first&&(c.s=this.first);a.req("read","article/list",c,b||a.noop)};c.prototype.render=function(){r.prototype.render.call(this);this.nav.render();this.$queuebtn.click(function(){this.$queuebtn.fadeOut(100);for(var a=this.queue.length-1;-1<a;--a)n.call(this,this.queue[a],!1);this.queue=[];q("html, body").animate({scrollTop:0},1E3)}.bind(this));a.$main.find("[data-ts]").each(function(){var a=q(this),
b=parseInt(a.data("ts"));a.text((new Date(b)).format("l, F j - g:i A"));a.removeAttr("data-ts")});var b={section:this.section};this.first&&(b.s=this.first);setInterval(u.bind(this,function(a){if(200===a.status.code&&a.result.articles&&a.result.articles.length){this.first=a.result.articles[0].id;for(var b=a.result.articles.length-1;-1<b;--b)this.queue.unshift(a.result.articles[b]);this.$queuebtn.text(9<this.queue.length?"9+":this.queue.length);this.$queuebtn.fadeIn(300)}}.bind(this)),18E4);var c;f.scroll(function(){this.more&&
(c&&clearTimeout(c),c=setTimeout(h.bind(this),100))}.bind(this));return this};var n=function(b,c){var f=new Date(b.ts),e="",k=b.author.replace(/^By\s+/,""),k=k.replace(/^(.*)\s+(the\s+\1)$/i,"$2"),k=k.trim();if(""===k)for(var g in this.titlemap){var h=this.titlemap[g],n=b.title.match(new RegExp(g,"i"));if(n){k=h.replace(/^.*\s([^\s]+)$/,"$1");k=(h[0]+k).toLowerCase()+"@sacbee.com";k=h+" "+k;break}}h=!1;(n=k.match(/\s+([^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i))?(k=k.replace(/\s+(?:[^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i,
""),g=k.toLowerCase(),this.authormap[g]?(h=!0,e=a.urls.authorimg+this.authormap[g]+".jpg",e='<img class="sf-profile" src="'+e+'" alt="'+k+'">'):(g=k.match(/^([^,]+)(?:,|\s+and)\s+/))&&this.authormap[g[1].toLowerCase()]&&(h=!0,e=a.urls.authorimg+this.authormap[g[1].toLowerCase()]+".jpg",e='<img class="sf-profile" src="'+e+'" alt="'+k+'">'),k='<p class="sf-byline"><span class="sf-name">'+k+"</span> "+n[1]+"</p>"):""!==k&&(k='<p class="sf-byline"><span class="sf-name">'+k.replace(/^the\s+/,"The ")+"</span></p>");
n="";b.thumb&&(n='<p class="sf-thumb"><a href="'+b.url+'"><img src="'+b.thumb+'" alt="'+b.title.replace('"',"'")+'"></a></p>');f=q('<article style="display: none" data-id="'+b.id+'"><div class="sf-top">'+n+'<h2><a href="'+b.url+'">'+b.title+'</a></h2><p class="sf-summary">'+b.summary.entityEncode()+'</p><p><a href="'+b.url+'">read more</a></p></div><div class="sf-bottom'+(h?" sf-authorimg":"")+'">'+e+k+'<p class="sf-date">'+f.format("l, F j - g:i A")+"</p></div></article>");c?a.$main.append(f):a.$main.prepend(f);
f.fadeIn(300)};g()})}}})(window.sacfeed);(function(a){if(!a.modules.Ext){var b=a.Ext=a.Ext||{};a.modules.Ext=!0;b.init=function(c){delete b.init;c=c||a.noop;c()}}})(window.sacfeed);
