<div class="comments" id='disqus_thread'>
    <h2>{{q($strArtifactName)}} - komentarze</h2>
    {{IF $arrComments}}
        <ol>
            {{BEGIN arrComments}}
            <li>
                <div>Dodany {{this::prettyDateTime($add_timestamp_utc)}} przez {{$author_nickname}}</div>
                <p>{{q($content)}}</p>
            </li>
            {{END}}
        </ol>
    {{ELSE}}
        <p class="empty">Brak komentarzy</p>
    {{END if-list}}
</div>
{{if $boolEnableDisqus}}
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES * * */
        var disqus_shortname = 'imgedpl';
        var disqus_title = '{{q($strArtifactName)}} na imgED - komentarze';
        var disqus_identifier = '{{$strDisqusIdentifier}}';

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
{{end if-list}}