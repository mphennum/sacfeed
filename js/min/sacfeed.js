(function(a){a.build="0.0.34";a.packages={sf:["sf"],sacfeed:"sacfeed Detect Poly UI UI.Ele UI.Nav UI.Section Ext".split(" "),"Ext.GA":["Ext.GA"]}})(window.sacfeed);(function(a,b,c){if(!b.modules.sacfeed){var e=b.packages=b.packages||{},k=b.packageMap={},d;for(d in e)for(var g=e[d],m=0,q=g.length;m<q;++m)k[g[m]]=d;b.urls.js="//js.sacfeed.com/v"+(b.devmode?b.version+"/src/":b.build+"/min/");b.modules.sacfeed=!0;b.init=function(c){delete b.init;c=c||b.noop;var f=["Poly","Detect"];a.jQuery||f.unshift("//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js");for(var h={ga:"Ext.GA"},d=0,l=f.length,e=function(){if(!(++d<l)){var f=b.$=a.jQuery;b.$body=f("body");
b.load=r;b.request=b.Detect.XHR?function(c,a,f,n){n=n||b.noop;var h=new XMLHttpRequest;h.open(c,a,!0);var g=!1;h.onreadystatechange=function(){if(4===h.readyState&&!g){g=!0;for(var b={},c=h.getAllResponseHeaders().split("\n"),a=0,f=c.length;a<f;++a){var d=c[a].trim();d&&(d=d.split(/\s*:\s*/),b[d[0].toLowerCase()]=d[1])}c=0;if(b["cache-control"])for(var e=b["cache-control"].split(/\s*[,;]\s*/),a=0,f=e.length;a<f;++a){if(d=e[a].trim().split(/\s*=\s*/),"max-age"===d[0]){c=parseInt(d[1]);break}}else b.expires&&
(a=new Date,c=parseInt(((new Date(b.expires)).getTime()-a.getTime())/1E3));n({code:h.status,message:h.statusText,ttl:0>c?0:c},b,h.responseText)}};h.send(null)}:b.Detect.XDR?function(a,c,f,n){n=n||b.noop;var h=new XDomainRequest;h.onprogress=b.noop;h.ontimeout=b.noop;h.onerror=b.noop;var d=!1;h.onload=function(){d||(d=!0,n({},{},h.responseText))};h.open(a,c);h.send()}:b.noop;for(var f=0,e=b.delayed.length;f<e;++f){var g=b.delayed[f];"load"===g.type?b.load.apply(b,g.arguments):"request"===g.type&&b.request.apply(b,
g.arguments)}delete b.delayed;f=0;for(e=b.analytics.length;f<e;++f){g=h[b.analytics[f]];if(!g)throw Error('Invalid analytics item "'+b.analytics[f]+'"');b.load(g)}delete b.analytics;c()}},g=0;g<l;++g){var p=f[g];/^(?:https?:)?\/\//.test(p)?b.inc(p,e):r(p,e)}};var h={create:"POST",read:"GET",update:"PUT","delete":"DELETE"};b.req=function(a,f,d,g){g=g||b.noop;var e=h[a];if("GET"!==e)throw Error('CRUD action "'+a+'" not allowed for sacfeed api requests');a=b.urls.api+f;if("GET"===e||"DELETE"===e){f=
[];for(var l in d)f.push(encodeURIComponent(l)+"="+encodeURIComponent(d[l]));f.length&&(a+="?"+f.join("&"))}b.request(e,a,d,function(b,a,f){f=c.parse(f.trim());if(200>b.code||299<b.code){var h=b.ttl;b=f;b.ttl=h;f=null}g(b,a,f)})};b.randID=function(){return Math.floor(2147483647*Math.random()).toString(16)};var l={},s=function(a){if("sacfeed"===a)return b;var c=b;a=a.split(".");for(var h=0,d=a.length;h<d;++h)c=c[a[h]];return c},p=function(b){var a=function(){for(var a=0,c=l[b].length;a<c;++a)l[b][a]();
delete l[b]},c=s(b);c.init?c.init(a):a()},r=function(a,c){c=c||b.noop;if(null===a)c();else if(a instanceof Array)for(var h=0,d=a.length,g=function(){++h<d||c()},e=0;e<d;++e)r(a[e],g);else l[a]?l[a].push(c):b.modules[a]&&!s(a).init?c():(l[a]=[c],(g=k[a])?b.modules[g]?p(a):b.inc(b.urls.js+g.toLowerCase().replace(".","/")+".js",function(){p(a)}):b.inc(b.urls.js+a.toLowerCase().replace(".","/")+".js",function(){p(a)}))}}})(window,window.sacfeed,window.JSON);(function(a){if(!a.modules.Detect){var b=a.Detect={};a.modules.Detect=!0;b.init=function(c){delete b.init;c=c||a.noop;b.XHR=XMLHttpRequest&&"withCredentials"in new XMLHttpRequest;b.XDR="undefined"!==typeof XDomainRequest;c()}}})(window.sacfeed);(function(a,b,c,e,k){if(!a.modules.Poly){var d=a.Poly={};a.modules.Poly=!0;d.init=function(g){delete d.init;g=g||a.noop;b.toLowerCase=b.toLowerCase||b.toString;b.toUpperCase=b.toUpperCase||b.toString;c.ltrim=c.ltrim||c.trimLeft||function(){return this.replace(/^\s+/,"")};c.rtrim=c.rtrim||c.trimRight||function(){return this.replace(/\s+$/,"")};c.trim=c.trim||function(){return this.replace(/^\s+|\s+$/g,"")};c.ucwords=c.ucwords||function(){return this.replace(/\b[a-z]+\b/gi,function(a){return a.charAt(0).toUpperCase()+
a.substr(1).toLowerCase()})};var m=[{base:"A",letters:/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},{base:"AA",letters:/[\uA732]/g},{base:"AE",letters:/[\u00C6\u01FC\u01E2]/g},{base:"AO",letters:/[\uA734]/g},{base:"AU",letters:/[\uA736]/g},{base:"AV",letters:/[\uA738\uA73A]/g},{base:"AY",letters:/[\uA73C]/g},{base:"B",letters:/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
{base:"C",letters:/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},{base:"D",letters:/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},{base:"DZ",letters:/[\u01F1\u01C4]/g},{base:"Dz",letters:/[\u01F2\u01C5]/g},{base:"E",letters:/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},{base:"F",letters:/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
{base:"G",letters:/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},{base:"H",letters:/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},{base:"I",letters:/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},{base:"J",letters:/[\u004A\u24BF\uFF2A\u0134\u0248]/g},{base:"K",letters:/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
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
{base:"x",letters:/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},{base:"y",letters:/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},{base:"z",letters:/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}];c.deaccent=c.deaccent||function(){for(var a=this.toString(),b=0,c=m.length;b<c;b++)a=a.replace(m[b].letters,m[b].base);return a};e.bind=e.bind||function(a){var b=this;if("function"!==typeof b)throw new TypeError;var c=
Array.prototype.slice,d=c.call(arguments,1),g=function(){if(!(this instanceof g))return b.apply(a,d.concat(c.call(arguments)));var e=function(){};e.prototype=b.prototype;var e=new e,f=b.apply(e,d.concat(c.call(arguments)));return Object(f)===f?f:e};return g};var q=[["Inv","Invalid"],["Jan","January"],["Feb","February"],["Mar","March"],["Apr","April"],["May","May"],["Jun","June"],["Jul","July"],["Aug","August"],["Sep","September"],["Oct","October"],["Nov","November"],["Dec","December"]];k.format=k.format||
function(a,b){a=a||"Y-m-d H:i:s";var c,d,g,e,f,k;b?(c=this.getUTCFullYear(),d=this.getUTCMonth(),g=this.getUTCDate(),e=this.getUTCHours(),f=this.getUTCMinutes(),k=this.getUTCSeconds()):(c=this.getFullYear(),d=this.getMonth(),g=this.getDate(),e=this.getHours(),f=this.getMinutes(),k=this.getSeconds());++d;a=a.replace("Y",function(){for(var a=Math.abs(c),b="";1E3>a;)b+="0",a*=10;return b+c});a=a.replace("m",10>d?"0"+d:d);a=a.replace("M",q[d][0]);a=a.replace("d",10>g?"0"+g:g);a=a.replace("H",10>e?"0"+
e:e);a=a.replace("i",10>f?"0"+f:f);return a=a.replace("s",10>k?"0"+k:k)};g()}}})(window.sacfeed,Number.prototype,String.prototype,Function.prototype,Date.prototype);(function(a){if(!a.modules.UI){var b=a.UI=a.UI||{};a.modules.UI=!0;b.init=function(c){delete b.init;c=c||a.noop;c()}}})(window.sacfeed);(function(a){if(!a.modules["UI.Ele"]){var b=a.UI=a.UI||{},c=b.Ele={};a.modules["UI.Ele"]=!0;c.init=function(e){delete c.init;e=e||a.noop;a.load("UI",function(){var k=a.$;c=b.Ele=function(a){if(!(this instanceof c))return new c(a);a=a||{};if(!a.parent)throw Error("UI.Ele must have a parent option");this.$=k(a.parent);this.parent=a.parent;return this};c.prototype.render=function(){return this};e()})}}})(window.sacfeed);(function(a){if(!a.modules["UI.Nav"]){var b=a.UI=a.UI||{},c=b.Nav={};a.modules["UI.Nav"]=!0;c.init=function(e){delete c.init;e=e||a.noop;a.load("UI.Ele",function(){var k=a.$,d=b.Ele;c=b.Nav=function(a){if(!(this instanceof c))return new c(a);a=a||{};d.prototype.constructor.call(this,a);if(!a.button)throw Error("UI.Nav must have a button option");this.$button=k(a.button);return this};c.prototype=Object.create(d.prototype);c.prototype.render=function(){d.prototype.render.call(this);this.$button.click(function(a){this.$.toggle();
a.preventDefault&&a.preventDefault();return!1}.bind(this));a.$body.click(function(){this.$.hide()}.bind(this));return this};e()})}}})(window.sacfeed);(function(a){if(!a.modules["UI.Section"]){var b=a.UI=a.UI||{},c=b.Section={};a.modules["UI.Section"]=!0;c.init=function(e){delete c.init;e=e||a.noop;a.load(["UI.Ele","UI.Nav"],function(){var k=b.Ele;c=b.Section=function(d){if(!(this instanceof c))return new c(d);d=d||{};d.parent=d.parent||a.$body;k.prototype.constructor.call(this,d);this.nav=(new b.Nav({parent:"nav",button:".sf-navbtn"})).render();return this};c.prototype=Object.create(k.prototype);c.prototype.render=function(){k.prototype.render.call(this);
return this};e()})}}})(window.sacfeed);(function(a){if(!a.modules.Ext){var b=a.Ext=a.Ext||{};a.modules.Ext=!0;b.init=function(c){delete b.init;c=c||a.noop;c()}}})(window.sacfeed);
