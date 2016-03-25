#!/bin/bash

strAuthEmail='nextdotcomltd@gmail.com';
strAuthApiKey='59eac48eecb2b6accd15e1b179ad1b052821d';

#curl -X GET "https://api.cloudflare.com/client/v4/zones?name=imged.pl" -H "X-Auth-Email: ${strAuthEmail}" -H "X-Auth-Key: ${strAuthApiKey}" -H "Content-Type: application/json";
#3cf8b05ce064a4dc6e5f330cf2689f15
#exit;

strFilesString="";
strSeparator='';
for strFileUrl in $(find public_html/imagehost3 -name '*.js' | sed s/public_html/http:\\/\\/imged.pl/g); do 
  strFilesString+="${strSeparator}\"${strFileUrl}\"";
  strSeparator=', ';
done;
echo " ";
echo "JS Files to purge:";
echo "${strFilesString}";
echo " "; 
curl -X DELETE "https://api.cloudflare.com/client/v4/zones/3cf8b05ce064a4dc6e5f330cf2689f15/purge_cache" -H "X-Auth-Email: ${strAuthEmail}" -H "X-Auth-Key: ${strAuthApiKey}" -H "Content-Type: application/json" --data "{'files':[${strFilesString}]}";

strFilesString="";
strSeparator='';
for strFileUrl in $(find public_html/imagehost3 -name '*.css' | sed s/public_html/http:\\/\\/imged.pl/g); do 
  strFilesString+="${strSeparator}\"${strFileUrl}\"";
  strSeparator=', ';
done;
echo " ";
echo "CSS Files to purge:";
echo "${strFilesString}";
echo " "; 
curl -X DELETE "https://api.cloudflare.com/client/v4/zones/3cf8b05ce064a4dc6e5f330cf2689f15/purge_cache" -H "X-Auth-Email: ${strAuthEmail}" -H "X-Auth-Key: ${strAuthApiKey}" -H "Content-Type: application/json" --data "{'files':[${strFilesString}]}";

strFilesString="";
strSeparator='';
for strFileUrl in $(find public_html/subframe -name '*.js' -not -path "*/vendor/*" | sed s/public_html/http:\\/\\/imged.pl/g); do 
  strFilesString+="${strSeparator}\"${strFileUrl}\"";
  strSeparator=', ';
done;
echo " ";
echo "JS Subframe Files to purge:";
echo "${strFilesString}";
echo " "; 
curl -X DELETE "https://api.cloudflare.com/client/v4/zones/3cf8b05ce064a4dc6e5f330cf2689f15/purge_cache" -H "X-Auth-Email: ${strAuthEmail}" -H "X-Auth-Key: ${strAuthApiKey}" -H "Content-Type: application/json" --data "{'files':[${strFilesString}]}";

strFilesString="\"http://imged.pl/robots.txt\"";
echo " ";
echo "Other Files to purge:";
echo "${strFilesString}";
echo " ";
curl -X DELETE "https://api.cloudflare.com/client/v4/zones/3cf8b05ce064a4dc6e5f330cf2689f15/purge_cache" -H "X-Auth-Email: ${strAuthEmail}" -H "X-Auth-Key: ${strAuthApiKey}" -H "Content-Type: application/json" --data "{'files':[${strFilesString}]}";

