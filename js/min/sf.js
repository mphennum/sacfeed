(function(f,g){if(!f.sacfeed){var a=f.sacfeed={version:0,urls:{}},l=g.getElementsByTagName("head")[0],b=new Date,k=a.noop=function(){};a.scripts={};a.modules={};a.build=[b.getFullYear(),b.getMonth()+1,b.getDate(),".",b.getHours()].join("");a.protocol="https:"===f.location.protocol?"https:":"http:";a.analytics=[];a.devmode=!1;for(var b=g.getElementsByTagName("script"),h=0;h<b.length;++h){var c=b[h].src.match(/\/(min|src)\/sf.js(?:#(.*))?$/);if(c){a.devmode="src"===c[1];c[2]&&(a.analytics=c[2].split(","));
break}}a.urls.api=a.protocol+"//api.sacfeed.com/v"+a.version+"/";a.urls.js=a.protocol+"//js.sacfeed.com/v"+a.version+"/"+(a.devmode?"src/":a.build+"/min/");a.delayed=[];a.load=function(){a.delayed.push({type:"load",arguments:arguments})};a.req=function(){a.delayed.push({type:"req",arguments:arguments})};a.inc=function(b,e){e=e||k;/^\/\//.test(b)&&(b=a.protocol+b);if(a.scripts[b])e();else{a.scripts[b]=!0;var d=g.createElement("script");d.type="text/javascript";d.async="true";var c=function(){e();e=
k};d.onload=c;d.onreadystatechange=function(){"loaded"!==this.readyState&&"complete"!==this.readyState||c()};d.src=b;l.appendChild(d)}};a.inc(a.urls.js+"sacfeed.js",function(){a.init()})}})(window,document);
