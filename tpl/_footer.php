</main>
</div>
</section>
<section class="section" id="qrscanner" style="display: none">
    <div class="container" style="max-width: 800px">
        <div id="qr_loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
        <div style="max-width: 100%">
            <canvas id="qr_canvas" hidden style="max-width: 100%"></canvas>
        </div>
        <div id="qr_output" hidden>
            <div id="qr_outputMessage">No QR code detected.</div>
            <div hidden><b>Data:</b> <span id="qr_outputData"></span></div>
        </div>
        <a href="#" id="qr_manual" class="button is-fullwidth">zadat QR kód ručně</a>
    </div>
</section>
<style>
    .slovnik-nadpis, h3 { font-family: serif; font-weight: bold; }

    .slovnik dt {
        font-weight: bold; font-family: serif;
        break-inside: avoid;
    }

    .slovnik dt a {
        color: darkblue;
    }

    .slovnik dt small {
        font-weight: normal;
    }

    .slovnik dd {
        margin-left: 1em;
    }

    .slovnik dd h4 {
        font-weight: bold; margin-top: 0.5em;
    }

    .slovnik dd h5 {
        font-weight: bold; font-style: italic; font-size: small;
    }

    /* zachranné CSS aby se něco po resetu vůbec vyrenderovalo */

    .markdown h2 {
        font-size: large; font-weight: bold; font-family: serif; padding: 0.5em 0;
    }

    .markdown p {
        text-indent: 1em;
    }

    .markdown blockquote {
        background-color: lightyellow;
        font-style: italic;
        padding: 1em;
        display: block;
        margin-bottom: 0.5em;
        margin-top: 0.5em;
    }

    .markdown li {
        list-style-type: disc;
    }


</style>
<script src="/static/uboot.js"></script>
<script>
whenReady(function () {
    on('navbar-burger', 'click', function () {
        if (hasClass('navbar-burger', 'is-active')) {
            delClass('navbar-burger', 'is-active');
            delClass('navbar-menu', 'is-active');
        } else {
            addClass('navbar-burger', 'is-active');
            addClass('navbar-menu', 'is-active');
        }
    });
});
</script>

</body>
</html>
