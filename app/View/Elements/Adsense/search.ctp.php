<?php $r = new Index(); ?>
<style type="text/css">
        @import url(http://www.google.com/cse/api/branding.css);
</style>
<div class="cse-branding-bottom" style="background-color:#FFFFFF;color:#000000">
        <div class="cse-branding-form">
                <form action="<?php echo $r->sitemap['e13__google']; ?>" id="cse-search-box">
                        <div>
                                <input type="hidden" name="cx" value="partner-pub-2922835116837672:1324051403" />
                                <input type="hidden" name="cof" value="FORID:10" />
                                <input type="hidden" name="ie" value="ISO-8859-15" />
                                <input type="text" name="q" size="25" />
                                <input type="submit" name="sa" value="Rechercher" />
                        </div>
                </form>
        </div>
        <div class="cse-branding-logo">
                <img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" alt="Google" />
        </div>
        <div class="cse-branding-text">
                <?php
                printf($r->lang("siteName"));
                ?>
        </div>
</div>
